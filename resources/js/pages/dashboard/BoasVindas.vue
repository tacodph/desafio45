<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Boas-vindas', href: '/atleta/boas-vindas' }];

const form = useForm({
    weight: '',
    waist: '',
    abdomen: '',
    hip: '',
    photo: null as File | null,
});

const onPhoto = (event: Event) => {
    form.photo = (event.target as HTMLInputElement).files?.[0] ?? null;
};

const save = () => form.post(route('athlete.welcome.store'));
</script>

<template>
    <Head title="Boas-vindas" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto flex w-full max-w-xl flex-col gap-6 p-4 md:p-6">
            <div class="flex flex-col gap-3">
                <div class="eyebrow">Dia 1 · início da jornada</div>
                <h1 class="display-title text-[clamp(36px,5vw,56px)]">Seu ponto de partida</h1>
                <p class="m-0 text-brand-muted">
                    Registre como você está hoje. Peso, cintura, abdômen e quadril, e uma foto se quiser. No dia 45, essa foto mostra a sua evolução.
                </p>
            </div>

            <form class="surface-card flex flex-col gap-4 p-6" @submit.prevent="save">
                <label class="flex flex-col gap-1.5 text-sm font-semibold">
                    Peso (kg)
                    <input v-model="form.weight" type="number" min="30" max="300" step="0.1" class="h-11 rounded-xl border border-brand-line2 bg-brand-card2 px-3 font-normal text-brand-cream" />
                    <InputError :message="form.errors.weight" />
                </label>
                <label class="flex flex-col gap-1.5 text-sm font-semibold">
                    Cintura (cm)
                    <input v-model="form.waist" type="number" min="40" max="200" step="0.1" class="h-11 rounded-xl border border-brand-line2 bg-brand-card2 px-3 font-normal text-brand-cream" />
                    <InputError :message="form.errors.waist" />
                </label>
                <label class="flex flex-col gap-1.5 text-sm font-semibold">
                    Abdômen (cm)
                    <input v-model="form.abdomen" type="number" min="40" max="200" step="0.1" class="h-11 rounded-xl border border-brand-line2 bg-brand-card2 px-3 font-normal text-brand-cream" />
                    <InputError :message="form.errors.abdomen" />
                </label>
                <label class="flex flex-col gap-1.5 text-sm font-semibold">
                    Quadril (cm)
                    <input v-model="form.hip" type="number" min="40" max="200" step="0.1" class="h-11 rounded-xl border border-brand-line2 bg-brand-card2 px-3 font-normal text-brand-cream" />
                    <InputError :message="form.errors.hip" />
                </label>
                <label class="flex flex-col gap-1.5 text-sm font-semibold">
                    Foto de hoje
                    <input type="file" accept="image/*" class="text-sm font-normal text-brand-muted" @change="onPhoto" />
                    <InputError :message="form.errors.photo" />
                </label>
                <Button type="submit" class="w-fit" :disabled="form.processing">Começar o desafio</Button>
            </form>
        </div>
    </AppLayout>
</template>
