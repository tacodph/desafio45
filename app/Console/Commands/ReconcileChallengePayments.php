<?php

namespace App\Console\Commands;

use App\Models\ChallengePayment;
use App\Services\Asaas\AsaasException;
use App\Services\ChallengePaymentService;
use Illuminate\Console\Command;

class ReconcileChallengePayments extends Command
{
    protected $signature = 'desafio:reconcile-payments';

    protected $description = 'Confere no Asaas as cobranças pendentes (rede de segurança caso um webhook se perca)';

    public function handle(ChallengePaymentService $service): int
    {
        $payments = ChallengePayment::query()
            ->where('status', ChallengePayment::PENDING)
            ->whereNotNull('asaas_payment_id')
            ->where('created_at', '<=', now()->subMinutes(10))
            ->where('created_at', '>=', now()->subDays(30))
            ->get();

        $failed = 0;

        foreach ($payments as $payment) {
            try {
                $service->sync($payment);
            } catch (AsaasException) {
                $failed++;
            }
        }

        $this->info("Conferidas: {$payments->count()} | falhas: {$failed}");

        return self::SUCCESS;
    }
}
