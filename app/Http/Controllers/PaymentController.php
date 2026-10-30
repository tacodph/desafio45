<?php

namespace App\Http\Controllers;

use App\Models\ChallengePayment;
use App\Models\User;
use App\Rules\Cpf;
use App\Services\Asaas\AsaasException;
use App\Services\ChallengePaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class PaymentController extends Controller
{
    public function __construct(private readonly ChallengePaymentService $payments) {}

    public function show(Request $request): Response|RedirectResponse
    {
        $user = $request->user();

        if (! $user->isAthlete()) {
            return to_route('dashboard');
        }

        $latest = $user->payments()->latest('id')->first();

        return Inertia::render('Pagamento', [
            'price' => config('desafio.price'),
            'needsCpf' => ! $user->asaas_customer_id,
            // Sem invoiceUrl de propósito: o link do Asaas nunca vai para o HTML.
            'payment' => [
                'state' => $this->state($user, $latest),
                'dueDate' => $latest?->due_date?->format('d/m/Y'),
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        abort_unless($user->isAthlete(), 403);

        if ($user->hasPaidChallenge()) {
            return to_route('pagamento.show');
        }

        $data = $request->validate([
            'cpf' => [Rule::requiredIf(! $user->asaas_customer_id), 'nullable', 'string', new Cpf],
        ]);

        try {
            $this->payments->ensureCharge($user, isset($data['cpf']) ? Cpf::digits($data['cpf']) : null);
        } catch (AsaasException $e) {
            return back()->withErrors(['asaas' => $e->userMessage()]);
        }

        return to_route('pagamento.show');
    }

    /** Leva para a fatura do Asaas. O botão "Pagar" aponta para cá, então o link visível é do nosso domínio. */
    public function pay(Request $request): RedirectResponse
    {
        $payment = $request->user()->payments()->pending()->whereNotNull('invoice_url')->latest('id')->first();

        if (! $payment) {
            return to_route('pagamento.show');
        }

        return redirect()->away($payment->invoice_url);
    }

    /** Volta do checkout: confere o status no Asaas uma vez e segue para a tela certa. */
    public function callback(Request $request): RedirectResponse
    {
        $user = $request->user();
        $payment = $user->payments()->latest('id')->first();

        if ($payment) {
            try {
                $this->payments->sync($payment);
            } catch (AsaasException) {
                // O webhook e a reconciliação resolvem; a tela segue em "aguardando".
            }
        }

        return to_route('pagamento.show');
    }

    private function state(User $user, ?ChallengePayment $latest): string
    {
        if ($user->hasPaidChallenge()) {
            return 'confirmado';
        }

        if (! $latest) {
            return 'novo';
        }

        if ($latest->status === ChallengePayment::REFUNDED) {
            return 'reembolsado';
        }

        if ($latest->status === ChallengePayment::PENDING && $latest->asaas_payment_id && $latest->due_date->gte(today())) {
            return 'pendente';
        }

        return 'vencido';
    }
}
