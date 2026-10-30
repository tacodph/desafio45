<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';

type PaymentRecord = {
    id: number;
    athlete: string | null;
    email: string | null;
    asaasPaymentId: string | null;
    status: string;
    billingType: string | null;
    value: string;
    dueDate: string | null;
    confirmedAt: string | null;
    createdAt: string | null;
};

type PaginationLink = {
    url: string | null;
    label: string;
    active: boolean;
};

defineProps<{
    payments: {
        data: PaymentRecord[];
        links: PaginationLink[];
        from: number | null;
        to: number | null;
        total: number;
    };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Painel', href: '/dashboard' },
    { title: 'Solicitações de pagamento', href: '/admin/pagamentos' },
];

const statusLabel = (status: string) =>
    ({
        PENDING: 'Pendente',
        RECEIVED: 'Recebido',
        CONFIRMED: 'Confirmado',
        OVERDUE: 'Vencido',
        REFUNDED: 'Reembolsado',
        DELETED: 'Excluído',
    })[status] ?? status;

const billingTypeLabel = (billingType: string | null) =>
    ({
        PIX: 'Pix',
        BOLETO: 'Boleto',
        CREDIT_CARD: 'Cartão',
        UNDEFINED: 'A definir',
    })[billingType ?? ''] ?? billingType ?? '—';

const linkLabel = (label: string) => {
    if (label.includes('Previous')) return 'Anterior';
    if (label.includes('Next')) return 'Próxima';
    return label;
};

const money = (value: string) => Number(value).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
</script>

<template>
    <Head title="Solicitações de pagamento" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
            <div class="flex flex-col gap-3">
                <div class="eyebrow">Asaas</div>
                <h1 class="display-title text-[clamp(36px,5vw,56px)]">Solicitações de pagamento</h1>
                <p class="max-w-xl text-brand-muted">Cobranças de inscrição registradas para os atletas do desafio.</p>
            </div>

            <div class="surface-card overflow-x-auto">
                <table class="w-full min-w-[900px] text-left text-sm">
                    <thead class="text-[13px] uppercase tracking-[2px] text-brand-muted-2">
                        <tr>
                            <th class="px-5 py-4 font-bold">Atleta</th>
                            <th class="px-5 py-4 font-bold">ID Asaas</th>
                            <th class="px-5 py-4 font-bold">Status</th>
                            <th class="px-5 py-4 font-bold">Forma</th>
                            <th class="px-5 py-4 font-bold">Valor</th>
                            <th class="px-5 py-4 font-bold">Vencimento</th>
                            <th class="px-5 py-4 font-bold">Confirmação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="payment in payments.data" :key="payment.id" class="border-t border-brand-line">
                            <td class="px-5 py-4">
                                <div class="font-semibold">{{ payment.athlete ?? 'Conta removida' }}</div>
                                <div class="text-brand-muted">{{ payment.email ?? '—' }}</div>
                            </td>
                            <td class="px-5 py-4 font-mono text-xs text-brand-muted">{{ payment.asaasPaymentId ?? '—' }}</td>
                            <td class="px-5 py-4">{{ statusLabel(payment.status) }}</td>
                            <td class="px-5 py-4">{{ billingTypeLabel(payment.billingType) }}</td>
                            <td class="px-5 py-4 font-semibold">{{ money(payment.value) }}</td>
                            <td class="px-5 py-4">{{ payment.dueDate ?? '—' }}</td>
                            <td class="px-5 py-4">{{ payment.confirmedAt ?? '—' }}</td>
                        </tr>
                        <tr v-if="payments.data.length === 0">
                            <td colspan="7" class="px-5 py-10 text-center text-brand-muted">Nenhuma solicitação de pagamento foi registrada.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="flex flex-wrap items-center justify-between gap-4">
                <p class="m-0 text-sm text-brand-muted">
                    {{ payments.from ?? 0 }}–{{ payments.to ?? 0 }} de {{ payments.total }} solicitações
                </p>
                <nav v-if="payments.links.length > 3" class="flex flex-wrap gap-2" aria-label="Paginação">
                    <Link
                        v-for="link in payments.links"
                        :key="link.label"
                        :href="link.url ?? ''"
                        :aria-disabled="!link.url"
                        class="rounded-lg border px-3 py-2 text-sm"
                        :class="[
                            link.active ? 'border-brand-gold bg-brand-gold text-brand-ink' : 'border-brand-line2 text-brand-cream',
                            !link.url ? 'pointer-events-none opacity-40' : 'hover:border-brand-cream',
                        ]"
                    >
                        {{ linkLabel(link.label) }}
                    </Link>
                </nav>
            </div>
        </div>
    </AppLayout>
</template>
