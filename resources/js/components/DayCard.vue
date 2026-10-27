<script setup lang="ts">
import { achieved, dayPoints, workoutMinutes, type DayDetails, type DayGoals } from '@/data/dayLog';
import { Link } from '@inertiajs/vue3';
import { ChevronDown, Droplets, Dumbbell, Lightbulb, Moon, Plus, Salad, Star, X } from 'lucide-vue-next';
import { computed, ref } from 'vue';

type DayForm = {
    day: number;
    checked_in: boolean;
    plus_count: number;
    notes: Record<string, string>;
    details: DayDetails;
};

const props = defineProps<{
    entry: DayForm;
    date: string;
    weekday: string;
    goals: DayGoals;
    workoutTypes: string[];
    weekSkills: string[];
    defaultOpen?: boolean;
}>();

const open = ref(props.defaultOpen ?? false);

const done = computed(() => achieved(props.entry.details, props.goals));
const points = computed(() => dayPoints(props.entry.checked_in, props.entry.details, props.goals));
const minutes = computed(() => workoutMinutes(props.entry.details));
const luxuryDay = computed(() => ['sex', 'sáb', 'dom'].includes(props.weekday));

const pct = (value: number, goal: number) => `${Math.min(100, Math.round((value / goal) * 100))}%`;

const liters = computed({
    get: () => props.entry.details.water_ml / 1000,
    set: (value: number | string) => {
        props.entry.details.water_ml = Math.max(0, Math.round((Number(value) || 0) * 1000));
    },
});

// Ao marcar o check-in, lança as metas de água, sono e alimentação nos campos ainda vazios.
// Treino e habilidade são específicos de cada dia, então o atleta preenche.
const launchGoals = () => {
    if (!props.entry.checked_in) {
        return;
    }

    const details = props.entry.details;

    if (details.water_ml === 0) {
        details.water_ml = props.goals.water_ml;
    }

    if (!details.sleep_hours) {
        details.sleep_hours = props.goals.sleep_hours;
    }

    if (!Object.values(details.food).some(Boolean)) {
        details.food.no_alcohol = true;
        details.food.no_fast_food = true;
        details.food.no_sweet = true;
    }
};

const addWorkout = () => props.entry.details.workouts.push({ type: props.workoutTypes[0], minutes: '' });
const addMinutes = (index: number, amount: number) => {
    const workout = props.entry.details.workouts[index];
    workout.minutes = (Number(workout.minutes) || 0) + amount;
};
const addWater = (ml: number) => {
    props.entry.details.water_ml += ml;
};

const foodChips = [
    ['no_alcohol', 'Sem álcool'],
    ['no_fast_food', 'Sem fast food'],
    ['no_sweet', 'Sem doce industrializado'],
] as const;

const fieldClass =
    'h-11 rounded-lg border border-brand-line2 bg-brand-card2 px-3 text-sm text-brand-cream placeholder:text-brand-muted-2 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-gold [color-scheme:dark]';
const chipClass = (active: boolean) =>
    ['min-h-9 rounded-full border px-3.5 py-2 text-xs font-bold', active ? 'border-brand-gold bg-brand-gold text-brand-ink' : 'border-brand-line3 text-brand-muted'];
</script>

<template>
    <article class="surface-card grid gap-4 p-4 sm:p-5">
        <button type="button" class="flex w-full items-center justify-between gap-3 text-left" :aria-expanded="open" @click="open = !open">
            <span class="flex min-w-0 flex-col gap-2">
                <span class="font-display text-xl font-extrabold uppercase">
                    Dia {{ entry.day }}
                    <span class="text-sm font-semibold text-brand-muted">{{ weekday }} {{ date }}</span>
                </span>
                <span class="flex items-center gap-2.5">
                    <Dumbbell class="size-4" :class="done.movement ? 'text-brand-gold' : 'text-brand-line3'" />
                    <Droplets class="size-4" :class="done.water ? 'text-brand-gold' : 'text-brand-line3'" />
                    <Moon class="size-4" :class="done.sleep ? 'text-brand-gold' : 'text-brand-line3'" />
                    <Salad class="size-4" :class="done.strength ? 'text-brand-gold' : 'text-brand-line3'" />
                    <Lightbulb class="size-4" :class="done.skill ? 'text-brand-gold' : 'text-brand-line3'" />
                    <Star class="size-4" :class="entry.plus_count > 0 ? 'text-brand-gold' : 'text-brand-line3'" />
                </span>
            </span>
            <span class="flex shrink-0 items-center gap-3">
                <span class="font-display text-2xl font-black text-brand-gold">{{ points }}</span>
                <ChevronDown class="size-5 text-brand-muted transition-transform" :class="open ? 'rotate-180' : ''" />
            </span>
        </button>

        <div v-show="open" class="grid gap-3">
            <label class="flex min-h-12 items-center gap-3 rounded-xl border px-3 py-3 text-sm font-semibold" :class="entry.checked_in ? 'border-brand-gold bg-brand-gold/10 text-brand-cream' : 'border-brand-line2 text-brand-muted'">
                <input v-model="entry.checked_in" type="checkbox" class="size-5 shrink-0 accent-brand-gold" @change="launchGoals" />
                Check-in do dia (foto e checklist no grupo)
            </label>
            <p class="m-0 -mt-1 px-1 text-xs text-brand-muted">Ao marcar o check-in, as metas de água, sono e alimentação são lançadas. Treino e habilidade você preenche. Ajuste o que foi diferente.</p>

            <!-- Treino -->
            <section class="grid gap-3 rounded-xl border px-3 py-3" :class="done.movement ? 'border-brand-gold bg-brand-gold/10' : 'border-brand-line2'">
                <div class="flex items-center justify-between gap-3 text-sm font-semibold">
                    <span class="flex items-center gap-2"><Dumbbell class="size-4 text-brand-gold" /> Treino</span>
                    <span :class="done.movement ? 'text-brand-gold' : 'text-brand-muted'">{{ minutes }} / {{ goals.movement_minutes }} min</span>
                </div>
                <div class="h-2 overflow-hidden rounded-full bg-brand-line3/40">
                    <div class="h-full rounded-full bg-brand-gold" :style="{ width: pct(minutes, goals.movement_minutes) }" />
                </div>
                <div v-for="(workout, index) in entry.details.workouts" :key="index" class="grid gap-2">
                    <div class="flex items-center gap-2">
                        <select v-model="workout.type" :class="[fieldClass, 'min-w-0 flex-1']">
                            <option v-for="type in workoutTypes" :key="type" :value="type">{{ type }}</option>
                        </select>
                        <input v-model.number="workout.minutes" type="number" min="0" max="600" placeholder="min" :class="[fieldClass, 'w-20 shrink-0']" />
                        <button type="button" class="shrink-0 rounded-full p-1.5 text-brand-muted hover:text-brand-cream" aria-label="Remover treino" @click="entry.details.workouts.splice(index, 1)">
                            <X class="size-4" />
                        </button>
                    </div>
                    <div class="flex flex-wrap gap-1.5">
                        <button v-for="amount in [15, 30, 45, 60]" :key="amount" type="button" :class="chipClass(false)" @click="addMinutes(index, amount)">+{{ amount }}</button>
                    </div>
                </div>
                <button type="button" class="flex min-h-9 w-fit items-center gap-1.5 text-sm font-semibold text-brand-gold" @click="addWorkout"><Plus class="size-4" /> Adicionar treino</button>
                <label v-if="minutes < goals.movement_minutes" class="flex min-h-9 items-center gap-2.5 text-xs text-brand-muted">
                    <input v-model="entry.details.manual.movement" type="checkbox" class="size-5 shrink-0 accent-brand-gold" />
                    Contar mesmo sem bater a meta
                </label>
            </section>

            <!-- Água -->
            <section class="grid gap-3 rounded-xl border px-3 py-3" :class="done.water ? 'border-brand-gold bg-brand-gold/10' : 'border-brand-line2'">
                <div class="flex items-center justify-between gap-3 text-sm font-semibold">
                    <span class="flex items-center gap-2"><Droplets class="size-4 text-brand-gold" /> Água</span>
                    <span :class="done.water ? 'text-brand-gold' : 'text-brand-muted'">{{ (entry.details.water_ml / 1000).toLocaleString('pt-BR', { maximumFractionDigits: 2 }) }} / {{ (goals.water_ml / 1000).toLocaleString('pt-BR', { maximumFractionDigits: 2 }) }} L</span>
                </div>
                <div class="h-2 overflow-hidden rounded-full bg-brand-line3/40">
                    <div class="h-full rounded-full bg-brand-gold" :style="{ width: pct(entry.details.water_ml, goals.water_ml) }" />
                </div>
                <div class="flex flex-wrap items-center gap-1.5">
                    <input v-model.number="liters" type="number" min="0" max="10" step="0.1" aria-label="Litros de água" :class="[fieldClass, 'w-24']" />
                    <span class="text-sm text-brand-muted">L</span>
                    <button type="button" :class="chipClass(false)" @click="addWater(250)">+ copo 250 ml</button>
                    <button type="button" :class="chipClass(false)" @click="addWater(500)">+ garrafa 500 ml</button>
                    <button type="button" :class="chipClass(false)" @click="addWater(1000)">+ 1 L</button>
                    <button type="button" :class="chipClass(false)" @click="entry.details.water_ml = 0">Zerar</button>
                </div>
                <label v-if="entry.details.water_ml < goals.water_ml" class="flex min-h-9 items-center gap-2.5 text-xs text-brand-muted">
                    <input v-model="entry.details.manual.water" type="checkbox" class="size-5 shrink-0 accent-brand-gold" />
                    Contar mesmo sem bater a meta
                </label>
            </section>

            <!-- Sono -->
            <section class="grid gap-3 rounded-xl border px-3 py-3" :class="done.sleep ? 'border-brand-gold bg-brand-gold/10' : 'border-brand-line2'">
                <div class="flex items-center justify-between gap-3 text-sm font-semibold">
                    <span class="flex items-center gap-2"><Moon class="size-4 text-brand-gold" /> Sono</span>
                    <span :class="done.sleep ? 'text-brand-gold' : 'text-brand-muted'">{{ entry.details.sleep_hours || 0 }} / {{ goals.sleep_hours }} h</span>
                </div>
                <div class="h-2 overflow-hidden rounded-full bg-brand-line3/40">
                    <div class="h-full rounded-full bg-brand-gold" :style="{ width: pct(Number(entry.details.sleep_hours) || 0, goals.sleep_hours) }" />
                </div>
                <div class="flex items-center gap-2">
                    <input v-model.number="entry.details.sleep_hours" type="number" min="0" max="24" step="0.5" placeholder="Horas dormidas" aria-label="Horas dormidas" :class="[fieldClass, 'w-40']" />
                    <span class="text-sm text-brand-muted">horas</span>
                </div>
                <label v-if="(Number(entry.details.sleep_hours) || 0) < goals.sleep_hours" class="flex min-h-9 items-center gap-2.5 text-xs text-brand-muted">
                    <input v-model="entry.details.manual.sleep" type="checkbox" class="size-5 shrink-0 accent-brand-gold" />
                    Contar mesmo sem bater a meta
                </label>
            </section>

            <!-- Alimentação -->
            <section class="grid gap-3 rounded-xl border px-3 py-3" :class="done.strength ? 'border-brand-gold bg-brand-gold/10' : 'border-brand-line2'">
                <div class="flex items-center justify-between gap-3 text-sm font-semibold">
                    <span class="flex items-center gap-2"><Salad class="size-4 text-brand-gold" /> Alimentação</span>
                    <span :class="done.strength ? 'text-brand-gold' : 'text-brand-muted'">{{ done.strength ? 'Cumprida' : 'Marque o que cumpriu' }}</span>
                </div>
                <div class="flex flex-wrap gap-1.5">
                    <button v-for="chip in foodChips" :key="chip[0]" type="button" :class="chipClass(entry.details.food[chip[0]])" @click="entry.details.food[chip[0]] = !entry.details.food[chip[0]]">
                        {{ chip[1] }}
                    </button>
                    <button v-if="luxuryDay" type="button" :class="chipClass(entry.details.food.luxury)" @click="entry.details.food.luxury = !entry.details.food.luxury">
                        Refeição luxo hoje
                    </button>
                </div>
                <label v-if="!done.strength || entry.details.manual.strength" class="flex min-h-9 items-center gap-2.5 text-xs text-brand-muted">
                    <input v-model="entry.details.manual.strength" type="checkbox" class="size-5 shrink-0 accent-brand-gold" />
                    Contar mesmo sem cumprir tudo
                </label>
            </section>

            <!-- Habilidade -->
            <section class="grid gap-3 rounded-xl border px-3 py-3" :class="done.skill ? 'border-brand-gold bg-brand-gold/10' : 'border-brand-line2'">
                <div class="flex items-center justify-between gap-3 text-sm font-semibold">
                    <span class="flex items-center gap-2"><Lightbulb class="size-4 text-brand-gold" /> Habilidade</span>
                    <span :class="done.skill ? 'text-brand-gold' : 'text-brand-muted'">{{ done.skill ? 'Praticada' : 'Ainda não' }}</span>
                </div>
                <select v-if="weekSkills.length" v-model="entry.details.skill" :class="fieldClass">
                    <option value="">Não pratiquei hoje</option>
                    <option v-for="skill in weekSkills" :key="skill" :value="skill">{{ skill }}</option>
                </select>
                <p v-else class="m-0 text-xs text-brand-muted">
                    Você ainda não escolheu as habilidades desta semana.
                    <Link :href="route('dashboard')" class="font-semibold text-brand-gold">Escolher no painel</Link>
                </p>
                <label v-if="!entry.details.skill" class="flex min-h-9 items-center gap-2.5 text-xs text-brand-muted">
                    <input v-model="entry.details.manual.skill" type="checkbox" class="size-5 shrink-0 accent-brand-gold" />
                    Contar a habilidade do dia
                </label>
            </section>

            <!-- Plus -->
            <section class="grid gap-2 rounded-xl border px-3 py-3" :class="entry.plus_count > 0 ? 'border-brand-gold bg-brand-gold/10' : 'border-brand-line2'">
                <div class="flex items-center justify-between gap-3 text-sm font-semibold">
                    <span class="flex items-center gap-2"><Star class="size-4 text-brand-gold" /> Plus</span>
                    <input v-model.number="entry.plus_count" type="number" min="0" max="9" aria-label="Quantidade de Plus" :class="[fieldClass, 'w-16']" />
                </div>
                <input v-model="entry.notes.plus" maxlength="255" placeholder="O que foi? Ex.: bolo, pão de queijo, pipoca" :class="fieldClass" />
            </section>

            <input v-model="entry.notes.day" maxlength="255" placeholder="Observação do dia (opcional)" :class="fieldClass" />
        </div>
    </article>
</template>
