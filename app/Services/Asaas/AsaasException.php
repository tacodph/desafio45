<?php

namespace App\Services\Asaas;

use RuntimeException;

class AsaasException extends RuntimeException
{
    /** Mensagem segura para mostrar à pessoa (sem detalhes técnicos). */
    public function userMessage(): string
    {
        return 'Não conseguimos gerar o pagamento agora. Tente novamente em instantes.';
    }
}
