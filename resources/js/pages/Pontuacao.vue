<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { BONUS_POINTS, DAILY_POINTS } from '@/composables/useChallenge';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Painel', href: '/dashboard' },
    { title: 'Pontuação', href: '/pontuacao' },
];
</script>

<template>
    <Head title="Pontuação" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
            <div>
                <div class="eyebrow">Como pontua</div>
                <h1 class="display-title text-[clamp(36px,5vw,56px)]">Até 10 pontos por dia</h1>
                <p class="m-0 mt-2 max-w-xl text-brand-muted">O dia só deixa de pontuar se não for registrado. Sem punição. Amanhã conta de novo.</p>
            </div>

            <section class="surface-card grid gap-4 p-5 sm:p-6">
                <h2 class="font-display m-0 text-2xl font-extrabold uppercase">Pontuação diária</h2>
                <div v-for="item in DAILY_POINTS" :key="item.t" class="flex items-baseline justify-between gap-4 border-b border-brand-line2 py-3 last:border-0 last:pb-0">
                    <span class="text-sm leading-snug">{{ item.t }}</span>
                    <span class="font-display text-xl font-black text-brand-gold">{{ item.p }}</span>
                </div>
            </section>

            <section class="surface-card grid gap-4 p-5 sm:p-6">
                <h2 class="font-display m-0 text-2xl font-extrabold uppercase">Bônus e conquistas</h2>
                <div v-for="item in BONUS_POINTS" :key="item.t" class="flex items-baseline justify-between gap-4 border-b border-brand-line2 py-3 last:border-0 last:pb-0">
                    <span class="text-sm leading-snug">{{ item.t }}</span>
                    <span class="font-display text-xl font-black text-brand-gold">{{ item.p }}</span>
                </div>
            </section>
        </div>
    </AppLayout>
</template>
