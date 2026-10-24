<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Equipe', href: '/dashboard' },
    { title: 'Atletas', href: '/equipe/atletas' },
];

defineProps<{
    athletes: { id: number; name: string; email: string }[];
}>();
</script>

<template>
    <Head title="Atletas" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
            <h1 class="display-title text-[clamp(36px,5vw,56px)]">Atletas</h1>

            <div class="surface-card overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="text-[13px] uppercase tracking-[2px] text-brand-muted-2">
                        <tr>
                            <th class="px-5 py-4 font-bold">Nome</th>
                            <th class="px-5 py-4 font-bold">E-mail</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="athletes.length === 0">
                            <td class="px-5 py-4 text-brand-muted" colspan="2">Nenhum atleta ainda.</td>
                        </tr>
                        <tr v-for="athlete in athletes" :key="athlete.id" class="border-t border-brand-line">
                            <td class="px-5 py-4 font-semibold">{{ athlete.name }}</td>
                            <td class="px-5 py-4 text-brand-muted">{{ athlete.email }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>
