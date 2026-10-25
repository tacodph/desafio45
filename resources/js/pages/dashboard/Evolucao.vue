<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

type Day = {
    day: number;
    date: string;
    weekday: string;
    week: number;
    checked_in: boolean;
    movement: boolean;
    water: boolean;
    sleep: boolean;
    strength: boolean;
    skill: boolean;
    plus_count: number;
    notes: string;
    points: number;
};

type Week = {
    week: number;
    period: string;
    checkins: number;
    daily: number;
    strength: number;
    skill: number;
    plus: number;
    plus_bonus: number;
    consistency: number;
    goals_bonus: number;
    achievements: number;
    total: number;
};

const props = defineProps<{
    currentWeek: number;
    profile: {
        movement_level: string;
        movement_minutes: number | string;
        water_liters: number | string;
        sleep_goal: string;
        skill: string;
        strength_goal: string;
        test_1: string;
        test_15: string;
        test_30: string;
        test_45: string;
        penalty: boolean;
        penalty_donated_on: string;
        penalty_note: string;
    };
    days: Day[];
    weeks: Week[];
    achievements: { id: number; achieved_on: string; type: string; label: string; points: number; detail: string }[];
    types: { type: string; label: string; points: number }[];
    total: number;
    penalty: { missed: number; amount: number };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Painel', href: '/dashboard' },
    { title: 'Evolução', href: '/atleta/evolucao' },
];

const tab = ref<'metas' | 'dias' | 'conquistas' | 'resumo'>('dias');
const week = ref(props.currentWeek);

const metas = useForm({ ...props.profile });
const dias = useForm({
    days: props.days.map((day) => ({
        day: day.day,
        checked_in: day.checked_in,
        movement: day.movement,
        water: day.water,
        sleep: day.sleep,
        strength: day.strength,
        skill: day.skill,
        plus_count: day.plus_count,
        notes: day.notes,
    })),
});
const conquista = useForm({
    achieved_on: '',
    type: 'first_time',
    detail: '',
});

const marks = [
    ['checked_in', 'Check-in'],
    ['movement', 'Treino'],
    ['water', 'Água'],
    ['sleep', 'Sono'],
    ['strength', 'Corpo'],
    ['skill', 'Habilidade'],
] as const;

const visibleDays = computed(() => dias.days.filter((day) => props.days.find((source) => source.day === day.day)?.week === week.value));

const dayLabel = (day: number) => props.days.find((source) => source.day === day);

const dayPoints = (day: (typeof dias.days)[number]) =>
    (day.checked_in ? 1 : 0) +
    (day.movement ? 3 : 0) +
    (day.water ? 1 : 0) +
    (day.sleep ? 1 : 0) +
    (day.strength ? 2 : 0) +
    (day.skill ? 2 : 0);

const saveMetas = () => metas.put(route('athlete.goals.update'));
const saveDias = () => dias.put(route('athlete.days.update'));
const saveConquista = () =>
    conquista.post(route('athlete.achievements.store'), {
        onSuccess: () => conquista.reset(),
    });
</script>

<template>
    <Head title="Evolução" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <div class="eyebrow">Meu desafio</div>
                    <h1 class="display-title text-[clamp(36px,5vw,56px)]">Evolução</h1>
                </div>
                <span class="font-display text-4xl font-black text-brand-gold">{{ total }} pts</span>
            </div>

            <div class="flex flex-wrap gap-2">
                <button
                    v-for="item in [
                        ['metas', 'Metas'],
                        ['dias', 'Dias'],
                        ['conquistas', 'Conquistas'],
                        ['resumo', 'Resumo'],
                    ]"
                    :key="item[0]"
                    type="button"
                    class="rounded-full px-4 py-2 text-sm font-bold"
                    :class="tab === item[0] ? 'bg-brand-gold text-brand-ink' : 'border border-brand-line2 text-brand-muted'"
                    @click="tab = item[0] as typeof tab"
                >
                    {{ item[1] }}
                </button>
            </div>

            <form v-if="tab === 'metas'" class="grid max-w-3xl gap-4" @submit.prevent="saveMetas">
                <p class="text-brand-muted">Preencha no dia 1. Pode ajustar no dia 15 e no dia 30.</p>
                <div class="grid gap-2">
                    <Label for="movement_level">Nível de movimento</Label>
                    <select id="movement_level" v-model="metas.movement_level" class="h-11 rounded-xl border border-brand-line2 bg-brand-card2 px-3">
                        <option value="">Escolher</option>
                        <option value="iniciante">Iniciante (20–30 min)</option>
                        <option value="intermediario">Intermediário (30–45 min)</option>
                        <option value="avancado">Avançado (45+ min)</option>
                    </select>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="grid gap-2">
                        <Label for="movement_minutes">Minutos de movimento por dia</Label>
                        <Input id="movement_minutes" v-model="metas.movement_minutes" type="number" min="0" max="300" />
                        <InputError :message="metas.errors.movement_minutes" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="water_liters">Meta de água (litros)</Label>
                        <Input id="water_liters" v-model="metas.water_liters" type="number" min="0" max="15" step="0.1" />
                        <InputError :message="metas.errors.water_liters" />
                    </div>
                </div>
                <div class="grid gap-2">
                    <Label for="sleep_goal">Meta de sono</Label>
                    <Input id="sleep_goal" v-model="metas.sleep_goal" placeholder="7h por noite ou deitar às 23h" />
                </div>
                <div class="grid gap-2">
                    <Label for="skill">Habilidade nova</Label>
                    <Input id="skill" v-model="metas.skill" placeholder="Ex.: cozinhar 5 pratos saudáveis" />
                </div>
                <div class="grid gap-2">
                    <Label for="strength_goal">Meta de Corpo forte</Label>
                    <Input id="strength_goal" v-model="metas.strength_goal" placeholder="Ex.: 3 x 60 s de prancha" />
                </div>
                <div class="grid gap-3 sm:grid-cols-2">
                    <div v-for="test in [
                        ['test_1', 'Teste dia 1 · 6/11'],
                        ['test_15', 'Teste dia 15 · 20/11'],
                        ['test_30', 'Teste dia 30 · 5/12'],
                        ['test_45', 'Teste dia 45 · 20/12'],
                    ]" :key="test[0]" class="grid gap-2">
                        <Label :for="test[0]">{{ test[1] }}</Label>
                        <Input :id="test[0]" v-model="metas[test[0]]" placeholder="Repetições, segundos ou quilos" />
                    </div>
                </div>
                <p class="text-sm text-brand-muted">Use números no teste. Se evoluir nos dias 15, 30 ou 45, registre a conquista.</p>
                <label class="flex items-center gap-3 text-sm">
                    <input v-model="metas.penalty" type="checkbox" class="size-4 accent-brand-gold" />
                    Penalidade solidária: R$ 10 por dia sem check-in
                </label>
                <div v-if="metas.penalty" class="grid gap-4 sm:grid-cols-2">
                    <div class="grid gap-2">
                        <Label for="penalty_donated_on">Data da doação</Label>
                        <Input id="penalty_donated_on" v-model="metas.penalty_donated_on" type="date" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="penalty_note">Para quem</Label>
                        <Input id="penalty_note" v-model="metas.penalty_note" />
                    </div>
                    <p class="text-sm text-brand-muted sm:col-span-2">
                        {{ penalty.missed }} dias sem check-in · R$ {{ penalty.amount }},00
                    </p>
                </div>
                <Button type="submit" :disabled="metas.processing">Salvar metas</Button>
            </form>

            <form v-else-if="tab === 'dias'" class="flex flex-col gap-4" @submit.prevent="saveDias">
                <div class="flex flex-wrap gap-2">
                    <button
                        v-for="item in weeks"
                        :key="item.week"
                        type="button"
                        class="rounded-full px-3 py-1.5 text-sm font-bold"
                        :class="week === item.week ? 'bg-brand-gold text-brand-ink' : 'border border-brand-line2 text-brand-muted'"
                        @click="week = item.week"
                    >
                        Semana {{ item.week }}
                    </button>
                </div>
                <article v-for="day in visibleDays" :key="day.day" class="surface-card grid gap-3 p-4">
                    <div class="flex items-baseline justify-between gap-3">
                        <span class="font-display text-xl font-extrabold uppercase">
                            Dia {{ day.day }}
                            <span class="text-sm font-semibold text-brand-muted">{{ dayLabel(day.day)?.weekday }} {{ dayLabel(day.day)?.date }}</span>
                        </span>
                        <span class="font-display text-2xl font-black text-brand-gold">{{ dayPoints(day) }}</span>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <label v-for="mark in marks" :key="mark[0]" class="flex items-center gap-2 text-sm">
                            <input v-model="day[mark[0]]" type="checkbox" class="size-4 accent-brand-gold" />
                            {{ mark[1] }}
                        </label>
                        <label class="flex items-center gap-2 text-sm">
                            Plus
                            <input v-model.number="day.plus_count" type="number" min="0" max="9" class="h-9 w-16 rounded-lg border border-brand-line2 bg-brand-card2 px-2" />
                        </label>
                    </div>
                    <Input v-model="day.notes" placeholder="Observação" />
                </article>
                <Button type="submit" class="w-fit" :disabled="dias.processing">Salvar dias</Button>
                <InputError :message="Object.values(dias.errors)[0]" />
            </form>

            <div v-else-if="tab === 'conquistas'" class="grid max-w-3xl gap-4">
                <form class="surface-card grid gap-4 p-5" @submit.prevent="saveConquista">
                    <div class="grid gap-2">
                        <Label for="achieved_on">Data</Label>
                        <Input id="achieved_on" v-model="conquista.achieved_on" type="date" min="2026-11-06" max="2026-12-20" required />
                        <InputError :message="conquista.errors.achieved_on" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="type">Conquista</Label>
                        <select id="type" v-model="conquista.type" class="h-11 rounded-xl border border-brand-line2 bg-brand-card2 px-3">
                            <option v-for="type in types" :key="type.type" :value="type.type">{{ type.label }} · +{{ type.points }}</option>
                        </select>
                    </div>
                    <div class="grid gap-2">
                        <Label for="detail">Detalhe</Label>
                        <Input id="detail" v-model="conquista.detail" />
                    </div>
                    <Button type="submit" class="w-fit" :disabled="conquista.processing">Registrar</Button>
                </form>
                <article v-for="item in achievements" :key="item.id" class="surface-card flex items-start justify-between gap-4 p-4">
                    <div>
                        <p class="m-0 font-bold">{{ item.label }} · +{{ item.points }}</p>
                        <p class="m-0 text-sm text-brand-muted">{{ item.achieved_on }} {{ item.detail }}</p>
                    </div>
                    <button type="button" class="text-sm font-semibold text-brand-muted" @click="router.delete(route('athlete.achievements.destroy', item.id))">
                        Apagar
                    </button>
                </article>
            </div>

            <div v-else class="grid gap-3">
                <article v-for="item in weeks" :key="item.week" class="surface-card grid gap-2 p-5">
                    <div class="flex items-baseline justify-between gap-3">
                        <span class="font-display text-2xl font-extrabold uppercase">Semana {{ item.week }}</span>
                        <span class="font-display text-3xl font-black text-brand-gold">{{ item.total }}</span>
                    </div>
                    <p class="m-0 text-sm text-brand-muted">{{ item.period }}</p>
                    <p class="m-0 text-sm text-brand-muted-3">
                        Diários {{ item.daily }} · Plus {{ item.plus_bonus }} · Consistência {{ item.consistency }} · Metas {{ item.goals_bonus }} ·
                        Conquistas {{ item.achievements }}
                    </p>
                </article>
            </div>
        </div>
    </AppLayout>
</template>
