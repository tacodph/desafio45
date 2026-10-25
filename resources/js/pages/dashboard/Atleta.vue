<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { GOALS, useChallenge } from '@/composables/useChallenge';
import { images } from '@/data/images';
import { SKILL_IDEAS } from '@/data/skillIdeas';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Painel',
        href: '/dashboard',
    },
];

const props = defineProps<{
    total: number;
    skillWeeks: { week: number; period: string; picks: string[] }[];
    baseline: { weight: string | null; waist: string | null; abdomen: string | null; hip: string | null; photo: string | null };
}>();

const { countdown, milestones } = useChallenge();

const skills = useForm({
    weeks: props.skillWeeks.map((week) => [...week.picks]),
});

const taken = (weekIndex: number, slot: number, idea: string) =>
    skills.weeks[weekIndex].some((pick, index) => index !== slot && pick === idea);

const saveSkills = () => skills.put(route('athlete.skills.update'));
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

            <section class="flex flex-col gap-3">
                <div>
                    <div class="eyebrow">Alimentação</div>
                    <h2 class="font-display m-0 text-3xl font-extrabold uppercase">Cantinho da nutri</h2>
                    <p class="m-0 mt-2 max-w-2xl text-sm text-brand-muted">Praticidade e equilíbrio, sem terrorismo.</p>
                </div>
                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                    <Link :href="route('alimentacao.compras')" class="surface-card p-5">
                        <h3 class="font-display m-0 text-xl font-extrabold uppercase leading-tight">Listas de compras</h3>
                    </Link>
                    <div v-for="item in ['Cardápios da semana', 'Kit da vida real', 'Comer sem tudo ou nada']" :key="item" class="surface-card p-5">
                        <h3 class="font-display m-0 text-xl font-extrabold uppercase leading-tight">{{ item }}</h3>
                    </div>
                </div>
            </section>

            <div v-if="baseline.photo || baseline.weight || baseline.waist || baseline.abdomen || baseline.hip" class="surface-card flex flex-wrap items-center gap-5 p-5">
                <img v-if="baseline.photo" :src="baseline.photo" alt="Foto do dia 1" class="h-28 w-24 rounded-xl object-cover" />
                <div>
                    <span class="text-[13px] font-bold uppercase tracking-[2px] text-brand-gold">Dia 1 · ponto de partida</span>
                    <p class="m-0 mt-2 text-sm text-brand-muted">
                        <span v-if="baseline.weight">{{ baseline.weight }} kg</span>
                        <span v-if="baseline.waist"> · cintura {{ baseline.waist }} cm</span>
                        <span v-if="baseline.abdomen"> · abdômen {{ baseline.abdomen }} cm</span>
                        <span v-if="baseline.hip"> · quadril {{ baseline.hip }} cm</span>
                    </p>
                </div>
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

                <Link :href="route('objetivos.show', 'alimentacao')" class="surface-card flex flex-col gap-3 p-6">
                    <img :src="images.alimentacao" alt="" class="size-14" />
                    <span class="text-[13px] font-bold uppercase tracking-[2px] text-[#43A047]">Alimentação e organização</span>
                    <p class="m-0 text-sm text-brand-muted">Sem álcool (exceto 3 eventos). Uma refeição luxo e uma Plus por semana. A luxo é uma refeição inteira. A Plus é um alimento cultural.</p>
                </Link>

                <div class="surface-card flex items-center justify-center p-6">
                    <img :src="images.logo" alt="Desafio 45 Dias" class="max-h-40 object-contain" />
                </div>
            </div>

            <form class="flex flex-col gap-4" @submit.prevent="saveSkills">
                <div>
                    <div class="eyebrow">Habilidade nova</div>
                    <h2 class="font-display m-0 text-3xl font-extrabold uppercase">3 por semana</h2>
                    <p class="m-0 mt-2 max-w-2xl text-sm text-brand-muted">Escolha 3 ideias para cada semana. Elas podem mudar de uma semana para a outra.</p>
                </div>
                <div class="grid gap-4 lg:grid-cols-2">
                    <article v-for="(week, weekIndex) in skillWeeks" :key="week.week" class="surface-card grid gap-3 p-5">
                        <div class="flex items-baseline justify-between gap-3">
                            <span class="font-display text-xl font-extrabold uppercase">Semana {{ week.week }}</span>
                            <span class="text-sm text-brand-muted">{{ week.period }}</span>
                        </div>
                        <select
                            v-for="slot in 3"
                            :key="slot"
                            v-model="skills.weeks[weekIndex][slot - 1]"
                            class="h-11 rounded-xl border border-brand-line2 bg-brand-card2 px-3 text-sm text-brand-cream"
                        >
                            <option value="">Escolher</option>
                            <optgroup v-for="group in SKILL_IDEAS" :key="group.title" :label="group.title">
                                <option v-for="idea in group.items" :key="idea" :value="idea" :disabled="taken(weekIndex, slot - 1, idea)">
                                    {{ idea }}
                                </option>
                            </optgroup>
                        </select>
                    </article>
                </div>
                <InputError :message="skills.errors.weeks" />
                <Button type="submit" class="w-fit" :disabled="skills.processing">Salvar habilidades</Button>
            </form>

            <Link :href="route('athlete.progress')" class="surface-card flex items-center justify-between gap-4 p-6">
                <div>
                    <span class="text-[13px] font-bold uppercase tracking-[2px] text-brand-gold">Minha evolução</span>
                    <p class="m-0 mt-1 font-display text-3xl font-black">{{ total }} pts</p>
                </div>
                <span class="font-bold">Registrar o dia →</span>
            </Link>
        </div>
    </AppLayout>
</template>
