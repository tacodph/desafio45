<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

type Food = { id: number; name: string; active: boolean };
type Group = { id: number; category: string; name: string; notes: string[] | null; foods: Food[] };

const props = defineProps<{
    groups: Group[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Administração', href: '/dashboard' },
    { title: 'Alimentos', href: '/admin/alimentos' },
];

const form = useForm({
    category: '',
    group: '',
    name: '',
});

const draft = ref<Record<number, string>>({});

const categories = computed(() => {
    const rows: { title: string; groups: Group[] }[] = [];

    props.groups.forEach((group) => {
        const found = rows.find((row) => row.title === group.category);
        if (found) {
            found.groups.push(group);
        } else {
            rows.push({ title: group.category, groups: [group] });
        }
    });

    return rows;
});

const save = () => {
    form.post(route('admin.foods.store'), {
        preserveScroll: true,
        onSuccess: () => form.reset('name'),
    });
};

const addToGroup = (group: Group) => {
    const name = (draft.value[group.id] ?? '').trim();
    if (!name) {
        return;
    }

    form.category = group.category;
    form.group = group.name;
    form.name = name;
    form.post(route('admin.foods.store'), {
        preserveScroll: true,
        onSuccess: () => {
            draft.value[group.id] = '';
            form.reset();
        },
    });
};
</script>

<template>
    <Head title="Alimentos" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
            <div>
                <div class="eyebrow">Lista de compras</div>
                <h1 class="display-title text-[clamp(36px,5vw,56px)]">Alimentos</h1>
                <p class="mt-2 max-w-2xl text-brand-muted">Categorias da lista da nutri. O atleta marca, na semana dele, só o que estiver cadastrado aqui.</p>
            </div>

            <form class="surface-card grid gap-3 p-4 sm:p-5 md:grid-cols-[1fr_1fr_1fr_auto]" @submit.prevent="save">
                <input v-model="form.category" list="categorias" required placeholder="Categoria" class="h-11 rounded-xl border border-brand-line2 bg-brand-card2 px-3 text-sm text-brand-cream" />
                <input v-model="form.group" list="grupos" placeholder="Grupo" class="h-11 rounded-xl border border-brand-line2 bg-brand-card2 px-3 text-sm text-brand-cream" />
                <input v-model="form.name" required placeholder="Alimento" class="h-11 rounded-xl border border-brand-line2 bg-brand-card2 px-3 text-sm text-brand-cream" />
                <button type="submit" class="h-11 rounded-full bg-[#43A047] px-5 text-sm font-bold text-white" :disabled="form.processing">Adicionar</button>
                <datalist id="categorias">
                    <option v-for="category in categories" :key="category.title" :value="category.title" />
                </datalist>
                <datalist id="grupos">
                    <option v-for="group in groups" :key="group.id" :value="group.name" />
                </datalist>
            </form>
            <p v-if="form.errors.name || form.errors.category" class="m-0 text-sm text-red-300">{{ form.errors.category || form.errors.name }}</p>

            <section v-for="category in categories" :key="category.title" class="surface-card flex flex-col gap-5 p-4 sm:p-5">
                <h2 class="m-0 font-display text-2xl font-extrabold uppercase">{{ category.title }}</h2>
                <div v-for="group in category.groups" :key="group.id" class="flex flex-col gap-2">
                    <h3 v-if="group.name" class="m-0 text-sm font-bold uppercase tracking-[2px] text-[#43A047]">{{ group.name }}</h3>
                    <div v-for="food in group.foods" :key="food.id" class="flex items-center justify-between gap-3 border-t border-brand-line py-2 text-sm">
                        <span :class="food.active ? '' : 'text-brand-muted line-through'">{{ food.name }}</span>
                        <span class="flex shrink-0 gap-1">
                            <Link :href="route('admin.foods.toggle', food.id)" method="patch" as="button" preserve-scroll class="px-2 py-2 text-xs font-bold uppercase text-[#43A047]">
                                {{ food.active ? 'Inativar' : 'Ativar' }}
                            </Link>
                            <Link :href="route('admin.foods.destroy', food.id)" method="delete" as="button" preserve-scroll class="px-2 py-2 text-xs font-bold uppercase text-brand-muted hover:text-brand-cream">
                                Excluir
                            </Link>
                        </span>
                    </div>
                    <form class="flex gap-2" @submit.prevent="addToGroup(group)">
                        <input v-model="draft[group.id]" placeholder="Novo alimento neste grupo" class="h-10 flex-1 rounded-xl border border-brand-line2 bg-brand-card2 px-3 text-sm text-brand-cream" />
                        <button type="submit" class="px-2 text-sm font-bold text-[#43A047]">Adicionar</button>
                    </form>
                </div>
            </section>
        </div>
    </AppLayout>
</template>
