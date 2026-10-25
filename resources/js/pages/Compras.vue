<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { shoppingList } from '@/data/shoppingList';
import { type BreadcrumbItem, type SharedData } from '@/types';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

const props = defineProps<{
    weeks: { week: number; period: string }[];
    lists: { week: number; period: string; items: string[]; extras: string[] }[];
    canSave: boolean;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Alimentação', href: '/objetivos/alimentacao' },
    { title: 'Lista de compras', href: '/objetivos/alimentacao/compras' },
];

const loggedIn = computed(() => Boolean(usePage<SharedData>().props.auth.user));

const intro = shoppingList.sections[0].groups.flatMap((group) => group.notes);
const sections = shoppingList.sections.filter((section) => /^\d/.test(section.title));

const editing = ref(props.lists.length === 0);
const week = ref(props.weeks[0]?.week ?? 1);
const checked = ref<Record<string, boolean>>({});
const extras = ref<Record<string, string>>({});
const form = useForm({
    week: 1,
    items: [] as string[],
    extras: [] as string[],
});

const showForm = computed(() => !props.canSave || editing.value);

const itemId = (section: string, group: string, index: number) => `${section}|${group}|${index}`;

const isBlank = (item: string) => item.includes('____');

const blankIds = () => {
    const ids: string[] = [];

    sections.forEach((section) => {
        section.groups.forEach((group) => {
            group.items.forEach((item, index) => {
                if (isBlank(item)) {
                    ids.push(itemId(section.title, group.title, index));
                }
            });
        });
    });

    return ids;
};

const loadWeek = (weekNumber: number) => {
    const list = props.lists.find((item) => item.week === weekNumber);
    const selected = new Set(list?.items ?? []);
    const next: Record<string, boolean> = {};

    sections.forEach((section) => {
        section.groups.forEach((group) => {
            group.items.forEach((item, index) => {
                if (!isBlank(item) && selected.has(item)) {
                    next[itemId(section.title, group.title, index)] = true;
                }
            });
        });
    });

    checked.value = next;
    extras.value = Object.fromEntries(blankIds().map((id, index) => [id, list?.extras[index] ?? '']));
};

watch(week, loadWeek, { immediate: true });

const openWeek = (weekNumber: number) => {
    week.value = weekNumber;
    editing.value = true;
    loadWeek(weekNumber);
};

const startNew = () => {
    const empty = props.weeks.find((item) => !props.lists.some((list) => list.week === item.week));
    openWeek(empty?.week ?? props.weeks[0].week);
};

const save = () => {
    const items = new Set<string>();

    sections.forEach((section) => {
        section.groups.forEach((group) => {
            group.items.forEach((item, index) => {
                if (!isBlank(item) && checked.value[itemId(section.title, group.title, index)]) {
                    items.add(item);
                }
            });
        });
    });

    form.week = week.value;
    form.items = [...items];
    form.extras = Object.values(extras.value).map((item) => item.trim()).filter(Boolean);
    form.put(route('alimentacao.compras.update'), {
        onSuccess: () => {
            editing.value = false;
        },
    });
};
</script>

<template>
    <Head title="Lista de compras" />

    <component :is="loggedIn ? AppLayout : 'div'" v-bind="loggedIn ? { breadcrumbs } : { class: 'min-h-screen bg-brand-ink text-brand-cream' }">
        <header v-if="!loggedIn" class="sticky top-0 z-20 border-b border-brand-line bg-brand-ink/88 backdrop-blur-[10px]">
            <div class="mx-auto flex max-w-[900px] flex-wrap items-center gap-2 px-6 py-4">
                <Link :href="route('objetivos.show', 'alimentacao')" class="rounded-full border border-brand-line3 px-4 py-2 text-sm font-bold text-brand-cream hover:border-brand-cream">
                    A regra
                </Link>
                <span class="rounded-full bg-[#43A047] px-4 py-2 text-sm font-bold text-white">Lista de compras</span>
            </div>
        </header>

        <main class="mx-auto flex max-w-[900px] flex-col gap-8 px-6 py-10">
            <div>
                <div class="text-sm font-bold uppercase tracking-[3px] text-[#43A047]">Alimentação e organização</div>
                <h1 class="mt-2 text-3xl font-black">Lista de Compras Inteligente</h1>
                <p class="mt-1 text-sm text-brand-cream/70">Nutri gente como a gente · Helen Freitas</p>
                <p v-for="note in intro" :key="note" class="mt-4 text-[15px] leading-relaxed text-brand-cream/85">{{ note }}</p>
            </div>

            <section v-if="canSave" class="flex flex-col gap-4">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <h2 class="m-0 text-xl font-black">Listas criadas</h2>
                    <button type="button" class="rounded-full bg-[#43A047] px-4 py-2 text-sm font-bold text-white" @click="startNew">Nova lista</button>
                </div>
                <p v-if="lists.length === 0" class="m-0 text-sm text-brand-cream/70">Nenhuma lista ainda. Escolha a semana e marque o que vai comprar.</p>
                <article v-for="list in lists" :key="list.week" class="surface-card flex flex-col gap-3 p-6">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <h3 class="m-0 text-lg font-black">Semana {{ list.week }} · {{ list.period }}</h3>
                        <button type="button" class="text-sm font-bold text-[#43A047]" @click="openWeek(list.week)">Editar</button>
                    </div>
                    <ul class="m-0 flex flex-col gap-1 pl-5 text-[15px]">
                        <li v-for="item in list.items" :key="item">{{ item }}</li>
                        <li v-for="extra in list.extras" :key="extra">{{ extra }}</li>
                    </ul>
                </article>
            </section>

            <form v-if="showForm" class="flex flex-col gap-8" @submit.prevent="save">
                <div v-if="canSave" class="surface-card flex flex-col gap-3 p-6">
                    <label class="text-sm font-bold uppercase tracking-[2px] text-[#43A047]" for="semana">Semana desta lista</label>
                    <select id="semana" v-model.number="week" class="h-11 rounded-xl border border-brand-line3 bg-transparent px-3 text-brand-cream">
                        <option v-for="item in weeks" :key="item.week" :value="item.week">Semana {{ item.week }} · {{ item.period }}</option>
                    </select>
                    <p v-if="form.errors.week" class="m-0 text-sm text-red-300">{{ form.errors.week }}</p>
                </div>

                <section v-for="section in sections" :key="section.title" class="surface-card flex flex-col gap-6 p-6">
                    <h2 class="text-xl font-black">{{ section.title }}</h2>
                    <div
                        v-for="group in section.groups.filter((item) => item.items.length || item.notes.length)"
                        :key="section.title + group.title"
                        class="flex flex-col gap-3"
                    >
                        <h3 v-if="group.title" class="text-sm font-bold uppercase tracking-[2px] text-[#43A047]">{{ group.title }}</h3>
                        <label v-for="(item, index) in group.items" :key="itemId(section.title, group.title, index)" class="flex items-start gap-3 text-[15px]">
                            <input v-if="canSave && !isBlank(item)" v-model="checked[itemId(section.title, group.title, index)]" type="checkbox" class="mt-1 size-4 accent-[#43A047]" />
                            <span v-if="!isBlank(item)" class="leading-snug">{{ item }}</span>
                            <input
                                v-else-if="canSave"
                                v-model="extras[itemId(section.title, group.title, index)]"
                                type="text"
                                placeholder="Outro item"
                                class="w-full rounded-lg border border-brand-line3 bg-transparent px-3 py-1.5 text-brand-cream outline-none placeholder:text-brand-cream/40"
                            />
                        </label>
                        <p v-for="note in group.notes" :key="note" class="text-sm leading-relaxed text-brand-cream/70">{{ note }}</p>
                    </div>
                </section>

                <div v-if="canSave" class="flex flex-wrap gap-3">
                    <button type="submit" class="rounded-full bg-[#43A047] px-5 py-3 text-sm font-bold text-white disabled:opacity-60" :disabled="form.processing">
                        Salvar lista
                    </button>
                    <button v-if="lists.length" type="button" class="rounded-full border border-brand-line3 px-5 py-3 text-sm font-bold" @click="editing = false">
                        Cancelar
                    </button>
                </div>
            </form>

            <section class="surface-card flex flex-col gap-3 p-6">
                <h2 class="text-xl font-black">Dicas para uma compra que funciona na vida real</h2>
                <ul class="flex flex-col gap-2 text-[15px] leading-relaxed">
                    <li v-for="tip in shoppingList.tips" :key="tip">{{ tip }}</li>
                </ul>
                <p v-for="line in shoppingList.closing" :key="line" class="text-sm leading-relaxed text-brand-cream/70">{{ line }}</p>
            </section>
        </main>
    </component>
</template>
