<?php

namespace App\Jobs;

use App\Models\AsaasWebhookEvent;
use App\Models\ChallengePayment;
use App\Services\ChallengePaymentService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ProcessAsaasWebhook implements ShouldQueue
{
    use Queueable;

    public int $tries = 5;

    public function __construct(public int $eventRecordId) {}

    /** @return list<int> */
    public function backoff(): array
    {
        return [10, 60, 300, 900];
    }

    public function handle(ChallengePaymentService $service): void
    {
        $record = AsaasWebhookEvent::query()->find($this->eventRecordId);

        if (! $record || $record->processed_at) {
            return;
        }

        $asaasPayment = (array) ($record->payload['payment'] ?? []);
        $payment = $this->locate($asaasPayment);

        if (! $payment) {
            Log::info('Asaas webhook: cobrança desconhecida', ['event' => $record->event, 'payment' => $asaasPayment['id'] ?? null]);
            $record->update(['processed_at' => now()]);

            return;
        }

        if ($record->event === 'PAYMENT_DELETED') {
            $service->markDeleted($payment);
        } else {
            // A API do Asaas é a fonte da verdade; o corpo do webhook só aponta qual cobrança mudou.
            $service->sync($payment);
        }

        $record->update(['processed_at' => now()]);
    }

    private function locate(array $asaasPayment): ?ChallengePayment
    {
        $id = $asaasPayment['id'] ?? null;
        $reference = $asaasPayment['externalReference'] ?? null;

        $payment = is_string($id) ? ChallengePayment::query()->where('asaas_payment_id', $id)->first() : null;

        if (! $payment && is_string($reference)) {
            $payment = ChallengePayment::query()->where('uuid', $reference)->first();

            if ($payment && ! $payment->asaas_payment_id && is_string($id)) {
                $payment->update(['asaas_payment_id' => $id]);
            }
        }

        return $payment;
    }
}
