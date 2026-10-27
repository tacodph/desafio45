<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import DayCard from '@/components/DayCard.vue';
import { type DayDetails, type DayGoals } from '@/data/dayLog';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type SharedData } from '@/types';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

type Day = {
    day: number;
    date: string;
    weekday: string;
    week: number;
    checked_in: boolean;
    plus_count: number;
    details: DayDetails;
    notes: Record<string, string>;
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
    tab: 'dias' | 'conquistas' | 'resumo' | null;
    currentWeek: number;
    today: number;
    goals: DayGoals;
    workoutTypes: string[];
    weeklySkills: string[][];
    days: Day[];
    weeks: Week[];
    achievements: { id: number; achieved_on: string; type: string; label: string; points: number; detail: string; emotion: string; comment: string; nutritionist: string }[];
    types: { type: string; label: string; points: number }[];
    total: number;
    canEdit: boolean;
    canComment: boolean;
    athleteName: string | null;
    nutritionistName: string | null;
}>();

type Tab = 'dias' | 'conquistas' | 'resumo';

const tabs: [Tab, string][] = [
    ['dias', 'Dias'],
    ['conquistas', 'Conquistas'],
    ['resumo', 'Resumo'],
];

// O atleta navega por páginas (/atleta/evolucao/{aba}); a nutricionista alterna as abas na mesma tela.
const localTab = ref<Tab>('conquistas');
const tab = computed<Tab>(() => (props.canEdit ? (props.tab ?? 'dias') : localTab.value));

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    { title: 'Painel', href: '/dashboard' },
    { title: 'Evolução', href: '#' },
    { title: tabs.find((item) => item[0] === tab.value)?.[1] ?? 'Dias', href: '#' },
]);
const week = ref(props.currentWeek);

const dias = useForm({
    days: props.days.map((day) => ({
        day: day.day,
        checked_in: day.checked_in,
        plus_count: day.plus_count,
        notes: { ...day.notes },
        details: JSON.parse(JSON.stringify(day.details)) as DayDetails,
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

const visibleDays = computed(() =>
    dias.days
        .map((entry) => ({ entry, info: props.days.find((source) => source.day === entry.day)! }))
        .filter((item) => item.info.week === week.value),
);

// Abre só o dia de hoje; se a semana não tem o dia de hoje, abre o primeiro dia dela.
const openDay = computed(() => (visibleDays.value.some((item) => item.entry.day === props.today) ? props.today : visibleDays.value[0]?.entry.day));

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
                <template v-for="item in canEdit ? tabs : tabs.slice(1)" :key="item[0]">
                    <Link
                        v-if="canEdit"
                        :href="route('athlete.progress.tab', item[0])"
                        class="rounded-full px-4 py-2 text-sm font-bold"
                        :class="tab === item[0] ? 'bg-brand-gold text-brand-ink' : 'border border-brand-line2 text-brand-muted'"
                    >
                        {{ item[1] }}
                    </Link>
                    <button
                        v-else
                        type="button"
                        class="rounded-full px-4 py-2 text-sm font-bold"
                        :class="tab === item[0] ? 'bg-brand-gold text-brand-ink' : 'border border-brand-line2 text-brand-muted'"
                        @click="localTab = item[0]"
                    >
                        {{ item[1] }}
                    </button>
                </template>
            </div>

            <form v-if="tab === 'dias'" class="flex flex-col gap-4" @submit.prevent="saveDias">
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
                <DayCard
                    v-for="item in visibleDays"
                    :key="item.entry.day"
                    :entry="item.entry"
                    :date="item.info.date"
                    :weekday="item.info.weekday"
                    :goals="goals"
                    :workout-types="workoutTypes"
                    :week-skills="weeklySkills[item.info.week - 1] ?? []"
                    :default-open="item.entry.day === openDay"
                />
                <Button type="submit" class="w-fit" :disabled="dias.processing">Salvar dias</Button>
                <InputError :message="Object.values(dias.errors)[0]" />
            </form>

            <div v-else-if="tab === 'conquistas'" class="flex max-w-3xl flex-col gap-4">
                <section v-for="card in conquests" :key="card.type" class="surface-card grid gap-4 p-4 sm:p-5">
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
                            <div class="flex shrink-0 flex-wrap justify-end gap-x-1 gap-y-0">
                                <button v-if="canEdit && editing !== item.id" type="button" class="px-2 py-1.5 text-sm font-semibold text-brand-gold" @click="startEdit(item)">
                                    Editar
                                </button>
                                <button v-if="canComment" type="button" class="px-2 py-1.5 text-sm font-semibold text-brand-gold" @click="openComment(item)">
                                    Comentar
                                </button>
                                <button
                                    v-if="canEdit"
                                    type="button"
                                    class="px-2 py-1.5 text-sm font-semibold text-brand-muted"
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
