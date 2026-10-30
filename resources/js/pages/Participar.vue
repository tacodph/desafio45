<script setup lang="ts">
import { GOAL_DETAILS } from '@/data/goalDetails';
import { images } from '@/data/images';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps<{
    price: number;
}>();

const page = usePage();
const priceLabel = computed(() => props.price.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }));
// Quem já está logada vai direto para o pagamento; quem não está, cria a conta primeiro.
const ctaHref = computed(() => (page.props.auth?.user ? route('pagamento.show') : route('register')));
</script>

<template>
    <Head title="Quem topa" />

    <div class="min-h-screen bg-brand-ink text-brand-cream">
        <header class="border-b border-brand-line">
            <div class="mx-auto flex max-w-[760px] items-center justify-between px-6 py-4">
                <Link :href="route('home')" class="flex items-center gap-2.5 text-brand-cream hover:text-brand-cream">
                    <img :src="images.logoMark" alt="" class="size-11 object-contain" />
                    <span class="font-display text-[22px] font-extrabold italic uppercase tracking-wide">
                        Desafio <span class="text-brand-gold">45</span> Dias
                    </span>
                </Link>
                <Link :href="route('home')" class="text-sm font-semibold text-brand-muted-3 hover:text-brand-cream">Voltar</Link>
            </div>
        </header>

        <main class="mx-auto flex max-w-[760px] flex-col gap-10 px-6 py-12">
            <div class="flex flex-col gap-4">
                <div class="text-sm font-bold uppercase tracking-[3px] text-brand-gold">6 nov → 20 dez 2026</div>
                <h1 class="display-title m-0 text-[clamp(48px,8vw,80px)] leading-[0.9]">45 dias.<br />5 objetivos.</h1>
                <p class="m-0 text-[18px] leading-relaxed text-brand-muted-3">
                    De 6 de novembro a 20 de dezembro. Alimentação, movimento, água, sono e uma habilidade nova. O desafio premia
                    consistência e participação, não perfeição. O check-in do dia fica aberto até as 23h.
                </p>
            </div>

            <ul class="m-0 flex list-none flex-col gap-3 p-0">
                <li
                    v-for="goal in GOAL_DETAILS"
                    :key="goal.key"
                    class="rounded-[22px] border border-brand-line2 bg-brand-card2 px-5 py-4"
                >
                    <span class="font-display text-xl font-extrabold uppercase" :style="{ color: goal.color }">{{ goal.title }}</span>
                    <p class="m-0 mt-1 text-[16px] text-brand-muted-3">{{ goal.desc }}</p>
                </li>
            </ul>

            <section id="pagamento" class="flex flex-col gap-4 rounded-[28px] bg-brand-gold px-7 py-8 text-brand-ink">
                <span class="text-sm font-bold uppercase tracking-[2px]">Inscrição</span>
                <p class="display-title m-0 text-[clamp(56px,8vw,80px)] leading-none">{{ priceLabel }}</p>
                <p class="m-0 max-w-[460px] text-[17px] font-semibold leading-snug">
                    Um pagamento para os 45 dias. Depois é só entrar e acompanhar o desafio.
                </p>
                <a
                    :href="ctaHref"
                    class="mt-2 inline-flex w-fit items-center rounded-full bg-brand-ink px-7 py-4 text-[17px] font-bold text-brand-cream hover:bg-brand-line hover:text-brand-cream"
                >
                    Pagar {{ priceLabel }}
                </a>
            </section>
        </main>
    </div>
</template>
