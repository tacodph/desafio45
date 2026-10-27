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

const preparo = [
    'Use uma fita métrica flexível.',
    'Fique em pé, com postura natural e abdômen relaxado.',
    'Mantenha a fita encostada na pele, sem apertar.',
    'Faça a leitura com a fita reta e paralela ao chão.',
    'Registre as medidas em centímetros.',
    'Sempre que possível, repita nas mesmas condições e nos mesmos pontos.',
];

const medidas = [
    {
        sigla: 'CC',
        nome: 'Cintura',
        imagem: '/images/medidas/cintura.webp',
        texto: 'Meça na parte mais estreita da cintura, que geralmente fica acima do umbigo e abaixo das costelas.',
    },
    {
        sigla: 'CA',
        nome: 'Abdômen',
        imagem: '/images/medidas/abdomen.webp',
        texto: 'Meça 2 dedos abaixo do umbigo, com o abdômen relaxado, sem prender a respiração.',
    },
    {
        sigla: 'CQ',
        nome: 'Quadril',
        imagem: '/images/medidas/quadril.webp',
        texto: 'Meça na parte mais larga do quadril, que geralmente fica na altura do glúteo.',
    },
];

const fotos = [
    { nome: 'Frente', imagem: '/images/medidas/foto-frente.webp', texto: 'De frente, braços estendidos para a frente, paralelos ao chão.' },
    { nome: 'Lado', imagem: '/images/medidas/foto-lado.webp', texto: 'De lado, braços estendidos para a frente, paralelos ao chão.' },
    { nome: 'Costas', imagem: '/images/medidas/foto-costas.webp', texto: 'De costas, braços estendidos para a frente, paralelos ao chão.' },
];

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

            <section class="surface-card p-6">
                <div class="flex flex-col gap-1">
                    <div class="eyebrow">Material de apoio</div>
                    <h2 class="m-0 text-xl font-bold text-brand-cream">Como tirar suas medidas</h2>
                </div>

                <div class="mt-5 flex flex-col gap-6">
                    <p class="m-0 text-brand-muted">
                        As medidas ajudam a acompanhar a sua evolução ao longo do desafio. Meça antes de preencher o formulário abaixo.
                    </p>

                    <div class="flex flex-col gap-2">
                        <h3 class="m-0 text-sm font-bold uppercase tracking-wider text-brand-cream">Antes de começar</h3>
                        <ul class="m-0 flex list-none flex-col gap-1.5 p-0 text-sm text-brand-muted-3">
                            <li v-for="item in preparo" :key="item" class="flex gap-2">
                                <span class="text-brand-gold">✓</span>
                                <span>{{ item }}</span>
                            </li>
                        </ul>
                    </div>

                    <div class="flex flex-col gap-4">
                        <div
                            v-for="(medida, i) in medidas"
                            :key="medida.sigla"
                            class="flex flex-col gap-3 rounded-xl border border-brand-line2 bg-brand-card2 p-3 sm:flex-row"
                        >
                            <img
                                :src="medida.imagem"
                                :alt="`Como medir: ${medida.nome}`"
                                class="h-32 w-full rounded-lg object-cover sm:w-44"
                                loading="lazy"
                            />
                            <div class="flex flex-col gap-1">
                                <div class="flex items-center gap-2">
                                    <span
                                        class="flex size-7 items-center justify-center rounded-full bg-brand-gold text-sm font-bold text-brand-ink"
                                        >{{ i + 1 }}</span
                                    >
                                    <span class="font-bold text-brand-cream">{{ medida.sigla }} · {{ medida.nome }}</span>
                                </div>
                                <p class="m-0 text-sm text-brand-muted">{{ medida.texto }}</p>
                                <p class="m-0 text-xs text-brand-muted-2">Lance no campo “{{ medida.nome }} (cm)”.</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col gap-3">
                        <h3 class="m-0 text-sm font-bold uppercase tracking-wider text-brand-cream">Foto de evolução</h3>
                        <p class="m-0 text-sm text-brand-muted">
                            Fotos de frente, de lado e de costas, com top e short (mulheres) ou sunga (homens). Use um lugar bem iluminado, fundo
                            neutro, câmera na altura do tronco e postura natural, sem filtros. Repita nas mesmas condições nos próximos registros.
                        </p>
                        <div class="grid grid-cols-3 gap-2">
                            <figure v-for="foto in fotos" :key="foto.nome" class="m-0 flex flex-col gap-1">
                                <img
                                    :src="foto.imagem"
                                    :alt="`Foto de evolução: ${foto.nome}`"
                                    class="aspect-[1/2] w-full rounded-lg object-cover"
                                    loading="lazy"
                                />
                                <figcaption class="text-center text-xs font-semibold text-brand-cream">{{ foto.nome }}</figcaption>
                                <span class="text-center text-[11px] leading-tight text-brand-muted-2">{{ foto.texto }}</span>
                            </figure>
                        </div>
                    </div>

                    <p class="m-0 rounded-xl border border-brand-line2 bg-brand-strip p-3 text-sm text-brand-muted-3">
                        <strong class="text-brand-gold">Importante:</strong> se tiver dúvidas sobre os pontos corretos de medição, peça orientação à
                        sua nutricionista. A padronização é essencial para comparar os resultados ao longo do tempo.
                    </p>
                </div>
            </section>

            <form class="surface-card flex flex-col gap-4 p-6" @submit.prevent="save">
                <label class="flex flex-col gap-1.5 text-sm font-semibold">
                    Peso (kg)
                    <input v-model="form.weight" type="number" min="30" max="300" step="0.1" :class="fieldClass" />
                    <InputError :message="form.errors.weight" />
                </label>
                <label class="flex flex-col gap-1.5 text-sm font-semibold">
                    Cintura (cm) <span class="font-normal text-brand-muted-2">· CC</span>
                    <input v-model="form.waist" type="number" min="40" max="200" step="0.1" :class="fieldClass" />
                    <InputError :message="form.errors.waist" />
                </label>
                <label class="flex flex-col gap-1.5 text-sm font-semibold">
                    Abdômen (cm) <span class="font-normal text-brand-muted-2">· CA</span>
                    <input v-model="form.abdomen" type="number" min="40" max="200" step="0.1" :class="fieldClass" />
                    <InputError :message="form.errors.abdomen" />
                </label>
                <label class="flex flex-col gap-1.5 text-sm font-semibold">
                    Quadril (cm) <span class="font-normal text-brand-muted-2">· CQ</span>
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
