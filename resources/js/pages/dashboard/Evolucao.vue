<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type SharedData } from '@/types';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
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
    notes: Record<'movement' | 'water' | 'sleep' | 'strength' | 'skill' | 'plus', string>;
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
    achievements: { id: number; achieved_on: string; type: string; label: string; points: number; detail: string; emotion: string; comment: string; nutritionist: string }[];
    types: { type: string; label: string; points: number }[];
    total: number;
    penalty: { missed: number; amount: number };
    canEdit: boolean;
    canComment: boolean;
    athleteName: string | null;
    nutritionistName: string | null;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Painel', href: '/dashboard' },
    { title: 'Evolução', href: '/atleta/evolucao' },
];

const tab = ref<'metas' | 'dias' | 'conquistas' | 'resumo'>(
    !props.canEdit || new URLSearchParams(window.location.search).get('aba') === 'conquistas' ? 'conquistas' : 'dias',
);
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
        notes: day.notes ?? '',
    })),
});
const conquests = [
    {
        type: 'cantinho',
        title: 'Cantinho da vitória',
        about: 'O que você fez de diferente e foi uma evolução. Ex.: fiz marmita na semana, preparei uma salada, preparei uma refeição em vez do iFood.',
        form: useForm({ type: 'cantinho', detail: '' }),
    },
    {
        type: 'resistido',
        title: 'Resistido do dia',
        about: 'Quais situações você conseguiu usar o auto controle e seguiu firme resistindo a tentações do dia.\nEx: bolo no trabalho;\nBatata frita no self service.\nSobremesa.',
        form: useForm({ type: 'resistido', detail: '' }),
    },
    {
        type: 'performance',
        title: 'Performance',
        about: 'Quando seu desempenho melhorou em relação ao treino anterior: mais carga, mais repetições, mais distância, mais tempo ou menos descanso. Ex.: aumentou a carga, correu mais de 10 minutos, bateu um novo recorde pessoal, aumentou a carga de um exercício, correu mais de 10 minutos sem parar, fez mais repetições do que na última vez, aumentou a carga no agachamento, correu por mais de 10 minutos, completou mais repetições que antes, aumentei a carga do supino, corri por mais de 10 minutos.',
        form: useForm({ type: 'performance', detail: '' }),
    },
];

const marks = [
    ['movement', 'Treino'],
    ['water', 'Água'],
    ['sleep', 'Sono'],
    ['strength', 'Alimentação'],
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
const saveConquista = (form: (typeof conquests)[number]['form']) =>
    form.post(route('athlete.achievements.store'), {
        onSuccess: () => form.reset('detail'),
    });

const nutriName = usePage<SharedData>().props.auth.user.name;
const emotions = ['👏', '🔥', '💪', '❤️', '⭐', '😍'];
const editing = ref<number | null>(null);
const commenting = ref<(typeof props.achievements)[number] | null>(null);
const editForm = useForm({ detail: '' });
const commentForm = useForm({ comment: '', emotion: '' });

const formatDate = (value: string) => {
    const [year, month, day] = value.split('-');

    return `${day}/${month}/${year}`;
};

const startEdit = (item: (typeof props.achievements)[number]) => {
    editing.value = item.id;
    editForm.detail = item.detail;
    editForm.clearErrors();
};

const saveEdit = (id: number) => {
    editForm.put(route('athlete.achievements.update', id), {
        preserveScroll: true,
        onSuccess: () => {
            editing.value = null;
        },
    });
};

const openComment = (item: (typeof props.achievements)[number]) => {
    commenting.value = item;
    commentForm.comment = item.comment;
    commentForm.emotion = item.emotion;
    commentForm.clearErrors();
};

const saveComment = () => {
    if (!commenting.value) {
        return;
    }

    commentForm.put(route('athlete.achievements.comment', commenting.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            commenting.value = null;
        },
    });
};
</script>

<template>
    <Head title="Evolução" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <div class="eyebrow">{{ canComment ? nutriName : 'Meu desafio' }}</div>
                    <h1 class="display-title text-[clamp(36px,5vw,56px)]">{{ athleteName ?? 'Evolução' }}</h1>
                    <p v-if="!canComment && nutritionistName" class="eyebrow m-0 mt-2">{{ nutritionistName }}</p>
                </div>
                <span class="font-display text-4xl font-black text-brand-gold">{{ total }} pts</span>
            </div>

            <div class="flex flex-wrap gap-2">
                <button
                    v-for="item in [
                        ...(canEdit
                            ? [
                                  ['metas', 'Metas'],
                                  ['dias', 'Dias'],
                              ]
                            : []),
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
                    <Label for="strength_goal">3 eventos com álcool</Label>
                    <Input id="strength_goal" v-model="metas.strength_goal" placeholder="Ex.: casamento, aniversário, confraternização" />
                </div>
                <p class="text-sm text-brand-muted">Sem fast food e doce, exceto na refeição luxo. Uma refeição luxo e uma Plus por semana. A luxo é uma refeição inteira. A Plus é um alimento cultural, como bolo, pão de queijo ou pipoca.</p>
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
                <article v-for="day in visibleDays" :key="day.day" class="surface-card grid gap-4 p-5">
                    <div class="flex items-baseline justify-between gap-3">
                        <span class="font-display text-xl font-extrabold uppercase">
                            Dia {{ day.day }}
                            <span class="text-sm font-semibold text-brand-muted">{{ dayLabel(day.day)?.weekday }} {{ dayLabel(day.day)?.date }}</span>
                        </span>
                        <span class="font-display text-2xl font-black text-brand-gold">{{ dayPoints(day) }}</span>
                    </div>
                    <div class="grid gap-3">
                        <div
                            v-for="mark in marks"
                            :key="mark[0]"
                            class="grid gap-2 rounded-xl border px-3 py-3"
                            :class="day[mark[0]] ? 'border-brand-gold bg-brand-gold/10' : 'border-brand-line2'"
                        >
                            <label class="flex items-center gap-3 text-sm font-semibold" :class="day[mark[0]] ? 'text-brand-cream' : 'text-brand-muted'">
                                <input v-model="day[mark[0]]" type="checkbox" class="size-4 accent-brand-gold" />
                                {{ mark[1] }}
                            </label>
                            <input
                                v-model="day.notes[mark[0]]"
                                maxlength="255"
                                placeholder="Observação"
                                class="h-10 rounded-lg border border-brand-line2 bg-brand-card2 px-3 text-sm font-normal text-brand-cream placeholder:text-brand-muted-2 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-gold"
                            />
                        </div>
                        <div class="grid gap-2 rounded-xl border border-brand-line2 px-3 py-3">
                            <label class="flex items-center justify-between gap-3 text-sm font-semibold text-brand-muted">
                                Plus
                                <input v-model.number="day.plus_count" type="number" min="0" max="9" class="h-9 w-16 rounded-lg border border-brand-line2 bg-brand-card2 px-2 text-brand-cream" />
                            </label>
                            <input
                                v-model="day.notes.plus"
                                maxlength="255"
                                placeholder="Observação"
                                class="h-10 rounded-lg border border-brand-line2 bg-brand-card2 px-3 text-sm font-normal text-brand-cream placeholder:text-brand-muted-2 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-gold"
                            />
                        </div>
                    </div>
                </article>
                <Button type="submit" class="w-fit" :disabled="dias.processing">Salvar dias</Button>
                <InputError :message="Object.values(dias.errors)[0]" />
            </form>

            <div v-else-if="tab === 'conquistas'" class="flex max-w-3xl flex-col gap-4">
                <section v-for="card in conquests" :key="card.type" class="surface-card grid gap-4 p-5">
                    <div>
                        <h2 class="font-display m-0 text-2xl font-extrabold uppercase">{{ card.title }}</h2>
                        <p class="m-0 mt-2 whitespace-pre-line text-sm leading-relaxed text-brand-muted">{{ card.about }}</p>
                    </div>
                    <form v-if="canEdit" class="grid gap-4" @submit.prevent="saveConquista(card.form)">
                        <div class="grid gap-2">
                            <Label :for="`conquista-${card.type}`">Conquista</Label>
                            <Input :id="`conquista-${card.type}`" v-model="card.form.detail" placeholder="O que aconteceu" />
                            <InputError :message="card.form.errors.detail" />
                        </div>
                        <Button type="submit" class="w-fit" :disabled="card.form.processing">Adicionar</Button>
                    </form>
                    <article
                        v-for="item in achievements.filter((entry) => entry.type === card.type)"
                        :key="item.id"
                        class="grid gap-2 border-t border-brand-line2 pt-3"
                    >
                        <div class="flex items-start justify-between gap-4">
                            <div class="grid min-w-0 flex-1 gap-1">
                                <p class="m-0 text-xs font-semibold uppercase tracking-[1px] text-brand-muted">{{ formatDate(item.achieved_on) }}</p>
                                <form v-if="editing === item.id" class="grid gap-2" @submit.prevent="saveEdit(item.id)">
                                    <Input v-model="editForm.detail" />
                                    <InputError :message="editForm.errors.detail" />
                                    <Button type="submit" class="w-fit" :disabled="editForm.processing">Salvar</Button>
                                </form>
                                <p v-else class="m-0 text-brand-cream">{{ item.detail }}</p>
                            </div>
                            <div class="flex shrink-0 gap-3">
                                <button v-if="canEdit && editing !== item.id" type="button" class="text-sm font-semibold text-brand-gold" @click="startEdit(item)">
                                    Editar
                                </button>
                                <button v-if="canComment" type="button" class="text-sm font-semibold text-brand-gold" @click="openComment(item)">
                                    Comentar
                                </button>
                                <button
                                    v-if="canEdit"
                                    type="button"
                                    class="text-sm font-semibold text-brand-muted"
                                    @click="router.delete(route('athlete.achievements.destroy', item.id))"
                                >
                                    Apagar
                                </button>
                            </div>
                        </div>
                        <p v-if="item.comment" class="m-0 text-sm text-brand-muted">
                            <span v-if="item.nutritionist || nutritionistName" class="font-semibold text-brand-cream">{{ item.nutritionist || nutritionistName }} · </span>{{ item.emotion }} {{ item.comment }}
                        </p>
                    </article>
                </section>
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

        <Dialog :open="commenting !== null" @update:open="(open) => !open && (commenting = null)">
            <DialogContent class="border-brand-line2 bg-brand-card text-brand-cream">
                <DialogHeader>
                    <DialogTitle>Comentário de {{ nutriName }}</DialogTitle>
                    <DialogDescription class="text-brand-muted">{{ commenting?.detail }}</DialogDescription>
                </DialogHeader>
                <div class="grid gap-3">
                    <div class="flex flex-wrap gap-2">
                        <button
                            v-for="emoji in emotions"
                            :key="emoji"
                            type="button"
                            class="rounded-full border px-3 py-1 text-xl"
                            :class="commentForm.emotion === emoji ? 'border-brand-gold' : 'border-brand-line2'"
                            @click="commentForm.emotion = emoji"
                        >
                            {{ emoji }}
                        </button>
                    </div>
                    <InputError :message="commentForm.errors.emotion" />
                    <textarea
                        v-model="commentForm.comment"
                        rows="4"
                        placeholder="Comentário"
                        class="w-full rounded-xl border border-brand-line2 bg-brand-card2 px-3 py-2 text-sm text-brand-cream placeholder:text-brand-muted-2 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-gold"
                    />
                    <InputError :message="commentForm.errors.comment" />
                </div>
                <DialogFooter>
                    <Button type="button" :disabled="commentForm.processing" @click="saveComment">Salvar</Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
