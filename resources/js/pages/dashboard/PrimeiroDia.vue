<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps<{
    firstAccess: boolean;
    baseline: {
        weight: string | number;
        waist: string | number;
        abdomen: string | number;
        hip: string | number;
        photo: string | null;
    };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Ponto de partida', href: '#' },
    { title: 'Primeiro dia', href: '/atleta/ponto-de-partida/primeiro-dia' },
];

const form = useForm({
    weight: props.baseline.weight,
    waist: props.baseline.waist,
    abdomen: props.baseline.abdomen,
    hip: props.baseline.hip,
    photo: null as File | null,
});

const onPhoto = (event: Event) => {
    form.photo = (event.target as HTMLInputElement).files?.[0] ?? null;
};

const save = () => form.post(route('athlete.start.first-day.store'));

const fieldClass = 'h-11 rounded-xl border border-brand-line2 bg-brand-card2 px-3 font-normal text-brand-cream';
</script>

<template>
    <Head title="Primeiro dia" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto flex w-full max-w-xl flex-col gap-6 p-4 md:p-6">
            <div class="flex flex-col gap-3">
                <div class="eyebrow">{{ firstAccess ? 'Passo 1 de 2 · ponto de partida' : 'Ponto de partida' }}</div>
                <h1 class="display-title text-[clamp(36px,5vw,56px)]">Primeiro dia</h1>
                <p class="m-0 text-brand-muted">
                    Registre como você está hoje. Peso, cintura, abdômen e quadril, e uma foto se quiser. No dia 45, essa foto mostra a sua evolução.
                </p>
            </div>

            <form class="surface-card flex flex-col gap-4 p-6" @submit.prevent="save">
                <label class="flex flex-col gap-1.5 text-sm font-semibold">
                    Peso (kg)
                    <input v-model="form.weight" type="number" min="30" max="300" step="0.1" :class="fieldClass" />
                    <InputError :message="form.errors.weight" />
                </label>
                <label class="flex flex-col gap-1.5 text-sm font-semibold">
                    Cintura (cm)
                    <input v-model="form.waist" type="number" min="40" max="200" step="0.1" :class="fieldClass" />
                    <InputError :message="form.errors.waist" />
                </label>
                <label class="flex flex-col gap-1.5 text-sm font-semibold">
                    Abdômen (cm)
                    <input v-model="form.abdomen" type="number" min="40" max="200" step="0.1" :class="fieldClass" />
                    <InputError :message="form.errors.abdomen" />
                </label>
                <label class="flex flex-col gap-1.5 text-sm font-semibold">
                    Quadril (cm)
                    <input v-model="form.hip" type="number" min="40" max="200" step="0.1" :class="fieldClass" />
                    <InputError :message="form.errors.hip" />
                </label>
                <div class="flex flex-col gap-2">
                    <img v-if="baseline.photo" :src="baseline.photo" alt="Foto do dia 1" class="h-32 w-28 rounded-xl object-cover" />
                    <label class="flex flex-col gap-1.5 text-sm font-semibold">
                        {{ baseline.photo ? 'Trocar a foto' : 'Foto de hoje' }}
                        <input type="file" accept="image/*" class="text-sm font-normal text-brand-muted" @change="onPhoto" />
                        <InputError :message="form.errors.photo" />
                    </label>
                </div>
                <Button type="submit" class="w-fit" :disabled="form.processing">{{ firstAccess ? 'Continuar para as metas' : 'Salvar' }}</Button>
            </form>
        </div>
    </AppLayout>
</template>
