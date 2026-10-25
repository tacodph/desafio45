<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { GOALS, useChallenge } from '@/composables/useChallenge';
import { CORPO_BLOCKS } from '@/data/goalDetails';
import { images } from '@/data/images';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Painel',
        href: '/dashboard',
    },
];

defineProps<{
    total: number;
}>();

const { countdown, milestones } = useChallenge();
</script>

<template>
    <Head title="Painel" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-4 md:p-6">
            <div class="flex flex-col gap-3">
                <div class="eyebrow">Desafio 45 Dias</div>
                <h1 class="display-title text-[clamp(36px,5vw,56px)]">
                    A gente <span class="text-brand-gold">cresce junto</span>
                </h1>
                <p class="max-w-xl text-brand-muted">
                    {{ countdown.label }}. Cinco objetivos, de 6 de novembro a 20 de dezembro.
                </p>
            </div>

            <div class="grid gap-4 md:grid-cols-3">
                <div class="surface-card flex flex-col justify-between gap-4 p-6">
                    <span class="text-[13px] font-bold uppercase tracking-[2px] text-brand-muted-2">{{ countdown.label }}</span>
                    <div v-if="countdown.isCounting" class="flex gap-2">
                        <div v-for="unit in countdown.units.slice(0, 2)" :key="unit.l" class="text-center">
                            <div class="font-display text-4xl font-extrabold tabular-nums">{{ unit.v }}</div>
                            <div class="text-xs uppercase tracking-widest text-brand-muted-2">{{ unit.l }}</div>
                        </div>
                    </div>
                    <div v-else-if="countdown.isRunning" class="flex flex-col gap-3">
                        <div class="font-display text-5xl font-black italic uppercase">
                            Dia <span class="text-brand-gold">{{ countdown.day }}</span>
                        </div>
                        <div class="h-2 overflow-hidden rounded-full bg-brand-line">
                            <div class="h-full rounded-full bg-brand-gold" :style="{ width: countdown.pct }" />
                        </div>
                    </div>
                    <div v-else class="font-display text-3xl font-black italic uppercase text-brand-gold">Concluído</div>
                </div>

                <div class="surface-card flex flex-col gap-3 p-6">
                    <span class="text-[13px] font-bold uppercase tracking-[2px] text-[#43A047]">Corpo forte</span>
                    <p v-for="block in CORPO_BLOCKS" :key="block.title" class="m-0 text-sm text-brand-muted">
                        <strong class="text-brand-cream">{{ block.title }}</strong> · {{ block.when }}
                    </p>
                </div>

                <div class="surface-card flex items-center justify-center p-6">
                    <img :src="images.logo" alt="Desafio 45 Dias" class="max-h-40 object-contain" />
                </div>
            </div>

            <Link :href="route('athlete.progress')" class="surface-card flex items-center justify-between gap-4 p-6">
                <div>
                    <span class="text-[13px] font-bold uppercase tracking-[2px] text-brand-gold">Minha evolução</span>
                    <p class="m-0 mt-1 font-display text-3xl font-black">{{ total }} pts</p>
                </div>
                <span class="font-bold">Registrar o dia →</span>
            </Link>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
                <Link v-for="goal in GOALS" :key="goal.key" :href="route('objetivos.show', goal.key)" class="surface-card flex flex-col gap-3 p-5">
                    <img :src="goal.icon" alt="" class="size-14" />
                    <span class="font-display text-2xl font-extrabold leading-none" :style="{ color: goal.color }">{{ goal.metric }}</span>
                    <span class="font-display text-lg font-bold uppercase leading-tight">{{ goal.title }}</span>
                </Link>
            </div>

            <div class="grid gap-3 md:grid-cols-4">
                <div
                    v-for="mile in milestones"
                    :key="mile.day"
                    class="flex flex-col gap-2 rounded-[20px] p-4"
                    :style="{ background: mile.bg, border: `1.5px solid ${mile.border}` }"
                >
                    <span class="text-[11px] font-bold uppercase tracking-widest text-brand-muted-2">{{ mile.date }}</span>
                    <span class="font-display text-3xl font-black italic" :style="{ color: mile.numColor }">Dia {{ mile.day }}</span>
                    <span class="font-display text-base font-extrabold uppercase">{{ mile.title }}</span>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
