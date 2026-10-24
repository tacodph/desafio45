<script setup lang="ts">
import BrandArt from '@/components/brand/BrandArt.vue';
import { useChallenge } from '@/composables/useChallenge';
import { Head, Link } from '@inertiajs/vue3';
import { Menu, X } from 'lucide-vue-next';
import { ref } from 'vue';

const { countdown, goals, pushDay, pushReps, bars, fails, failTotal, failNote, failDots, incFail, decFail, earn, milestones, instagramUrl } =
    useChallenge();

const menuOpen = ref(false);

const navLinks = [
    { href: '#objetivos', label: 'Objetivos' },
    { href: '#regras', label: 'Regras' },
    { href: '#jornada', label: 'Jornada' },
    { href: '#mindset', label: 'Mindset' },
];
</script>

<template>
    <Head title="A gente cresce junto" />

    <div class="min-h-screen overflow-x-hidden bg-brand-ink text-brand-cream">
        <header class="sticky top-0 z-20 border-b border-brand-line bg-brand-ink/88 backdrop-blur-[10px]">
            <div class="mx-auto flex max-w-[1200px] items-center justify-between gap-4 px-6 py-3">
                <a href="#topo" class="flex items-center gap-2.5 text-brand-cream hover:text-brand-cream">
                    <img src="/images/logo.webp" alt="Desafio 45 Firme" class="size-11 object-contain" />
                    <span class="font-display text-[22px] font-extrabold italic uppercase tracking-wide">
                        Desafio <span class="text-brand-gold">45</span> Firme
                    </span>
                </a>

                <nav class="hidden items-center justify-end gap-6 lg:flex">
                    <a
                        v-for="link in navLinks"
                        :key="link.href"
                        :href="link.href"
                        class="text-[15px] font-medium text-brand-muted-3 hover:text-brand-cream"
                    >
                        {{ link.label }}
                    </a>
                    <Link
                        v-if="!$page.props.auth.user"
                        :href="route('login')"
                        class="rounded-full bg-brand-gold px-[18px] py-2.5 text-[15px] font-bold text-brand-ink hover:bg-brand-gold-hover hover:text-brand-ink"
                    >
                        Entrar
                    </Link>
                    <Link
                        v-if="$page.props.auth.user"
                        :href="route('dashboard')"
                        class="text-[15px] font-medium text-brand-muted-3 hover:text-brand-cream"
                    >
                        Painel
                    </Link>
                </nav>

                <button class="rounded-full border border-brand-line3 p-2 text-brand-cream lg:hidden" type="button" @click="menuOpen = !menuOpen">
                    <X v-if="menuOpen" class="size-5" />
                    <Menu v-else class="size-5" />
                </button>
            </div>

            <nav v-if="menuOpen" class="flex flex-col gap-3 border-t border-brand-line px-6 py-4 lg:hidden">
                <a
                    v-for="link in navLinks"
                    :key="link.href"
                    :href="link.href"
                    class="text-[15px] font-medium text-brand-muted-3"
                    @click="menuOpen = false"
                >
                    {{ link.label }}
                </a>
                <Link
                    v-if="!$page.props.auth.user"
                    :href="route('login')"
                    class="inline-flex w-fit rounded-full bg-brand-gold px-[18px] py-2.5 text-[15px] font-bold text-brand-ink"
                >
                    Entrar
                </Link>
                <Link v-if="$page.props.auth.user" :href="route('dashboard')" class="text-[15px] font-medium text-brand-muted-3"> Painel </Link>
            </nav>
        </header>

        <section
            id="topo"
            class="relative mx-auto grid max-w-[1200px] items-center gap-8 px-6 py-10 sm:py-16 lg:grid-cols-2 lg:gap-16 lg:py-24"
        >
            <div class="relative z-10 flex flex-col gap-7">
                <div class="flex items-center gap-3 text-sm font-bold uppercase tracking-[3px] text-brand-gold">
                    <span class="h-0.5 w-8 bg-brand-gold" />
                    <span>6 nov → 20 dez · 45 dias antes do verão</span>
                </div>
                <h1 class="display-title m-0 text-[clamp(56px,8.4vw,112px)] tracking-[-1px]">
                    <span class="text-brand-gold">45 dias.</span><br />5 objetivos.<br />Uma vida melhor.
                </h1>
                <p class="m-0 max-w-[520px] text-[clamp(18px,1.6vw,21px)] leading-normal text-brand-muted-3">
                    Enquanto o verão se aproxima, a gente se prepara. Não é só ficar com o corpo melhor para a praia (spoiler: vai ficar). É sobre
                    transformação real — mente, fé, amizade e regra de vida.
                </p>
                <div class="flex flex-wrap gap-3">
                    <a
                        :href="instagramUrl"
                        target="_blank"
                        rel="noopener"
                        class="inline-flex items-center gap-2.5 rounded-full bg-brand-gold px-7 py-4 text-[17px] font-bold text-brand-ink hover:bg-brand-gold-hover hover:text-brand-ink"
                    >
                        Quem topa? <span>→</span>
                    </a>
                    <a
                        href="#regras"
                        class="rounded-full border-[1.5px] border-brand-line3 px-7 py-4 text-[17px] font-semibold text-brand-cream hover:border-brand-cream hover:text-brand-cream"
                    >
                        Ver as regras
                    </a>
                </div>

                <div class="flex flex-col gap-3 pt-2">
                    <div class="text-[13px] font-bold uppercase tracking-[2.5px] text-brand-muted-2">{{ countdown.label }}</div>
                    <div v-if="countdown.isCounting" class="flex flex-wrap gap-2.5">
                        <div
                            v-for="unit in countdown.units"
                            :key="unit.l"
                            class="flex min-w-[78px] flex-col items-center gap-0.5 rounded-[14px] border border-brand-line2 bg-brand-card2 px-3.5 py-3"
                        >
                            <span class="font-display text-[40px] font-extrabold leading-none tabular-nums text-brand-cream">{{ unit.v }}</span>
                            <span class="text-xs font-semibold uppercase tracking-widest text-brand-muted-2">{{ unit.l }}</span>
                        </div>
                    </div>
                    <div v-else-if="countdown.isRunning" class="flex max-w-[440px] flex-col gap-2.5">
                        <div class="font-display text-[44px] font-extrabold italic uppercase leading-none">
                            Dia <span class="text-brand-gold">{{ countdown.day }}</span> de 45
                        </div>
                        <div class="h-2.5 overflow-hidden rounded-full bg-brand-line">
                            <div class="h-full rounded-full bg-brand-gold" :style="{ width: countdown.pct }" />
                        </div>
                    </div>
                    <div v-else class="font-display text-[44px] font-extrabold italic uppercase leading-none text-brand-gold">
                        45 dias firmes. Até o verão.
                    </div>
                </div>
            </div>

            <div class="relative flex items-center justify-center">
                <div
                    class="absolute aspect-square w-[78%] rounded-full bg-[radial-gradient(circle,rgba(246,168,33,0.22)_0%,rgba(246,168,33,0)_68%)]"
                />
                <img
                    src="/images/logo.webp"
                    alt="Logo Desafio 45 Firme — 45 dias antes do verão. A gente cresce junto"
                    class="relative block h-auto w-full max-w-[580px]"
                />
            </div>
        </section>

        <div class="border-y border-brand-line bg-brand-strip">
            <div
                class="font-display mx-auto flex max-w-[1200px] flex-wrap justify-between gap-x-8 gap-y-4 px-6 py-[18px] text-[clamp(18px,2vw,24px)] font-bold uppercase tracking-[2px]"
            >
                <span class="text-brand-corpo">Corpo</span>
                <span class="text-brand-mente">Mente</span>
                <span class="text-brand-fe">Fé</span>
                <span class="text-brand-familia">Família</span>
                <span class="text-brand-conhecimento">Conhecimento</span>
            </div>
        </div>

        <section id="objetivos" class="mx-auto max-w-[1200px] px-6 py-[clamp(72px,9vw,120px)]">
            <div class="mb-10 flex flex-wrap items-end justify-between gap-6">
                <div class="flex flex-col gap-3.5">
                    <div class="eyebrow">Todo dia, sem exceção</div>
                    <h2 class="display-title m-0 text-[clamp(44px,6vw,80px)]">5 objetivos por dia</h2>
                </div>
                <p class="m-0 max-w-[360px] text-[17px] leading-normal text-brand-muted">
                    Toque em cada objetivo para ver a regra, por que funciona e dicas práticas.
                </p>
            </div>

            <div class="grid grid-cols-[repeat(auto-fit,minmax(min(100%,200px),1fr))] gap-4">
                <Link
                    v-for="goal in goals"
                    :key="goal.key"
                    :href="route('objetivos.show', goal.key)"
                    class="flex flex-col gap-4 rounded-[22px] border-2 border-brand-line2 bg-brand-card p-6 text-brand-cream transition hover:-translate-y-0.5 hover:border-brand-line3 hover:text-brand-cream"
                >
                    <img :src="goal.icon" alt="" class="block size-[88px]" />
                    <div class="flex flex-1 flex-col gap-1.5">
                        <span class="font-display text-[34px] font-extrabold leading-none" :style="{ color: goal.color }">{{ goal.metric }}</span>
                        <span class="font-display text-[22px] font-bold uppercase leading-tight tracking-wide text-brand-cream">{{ goal.title }}</span>
                        <span class="text-[15px] leading-snug text-brand-muted">{{ goal.desc }}</span>
                    </div>
                    <span class="flex items-center gap-2 text-sm font-bold uppercase tracking-wider" :style="{ color: goal.color }">
                        <span>Ver regra e dicas</span><span>→</span>
                    </span>
                </Link>
            </div>
        </section>

        <section id="objetivo-flexoes" class="border-y border-brand-line bg-brand-strip">
            <div class="mx-auto grid max-w-[1200px] items-center gap-12 px-6 py-[clamp(72px,9vw,120px)] lg:grid-cols-2">
                <div class="flex flex-col gap-5">
                    <div class="text-sm font-bold uppercase tracking-[3px] text-brand-conhecimento">Flexões progressivas</div>
                    <h2 class="display-title m-0 text-[clamp(44px,6vw,80px)]">
                        Começa com 30.<br /><span class="text-brand-conhecimento">Termina com 100.</span>
                    </h2>
                    <p class="m-0 max-w-[460px] text-[17px] leading-relaxed text-brand-muted">
                        A meta sobe a cada 8 dias: 30, 45, 60, 75, 90 e 100 — dá pra dividir em séries ao longo do dia. Arraste para ver a meta de
                        cada dia. Fazendo em grupo? Criem o grupo de vocês no Gymrats para acompanhar a evolução juntos.
                    </p>
                    <div class="flex max-w-[460px] flex-col gap-3.5 rounded-[22px] border border-brand-line2 bg-brand-ink px-6 pb-[18px] pt-6">
                        <BrandArt kind="flexoes" />
                        <div class="flex justify-between text-[13px] font-bold uppercase tracking-widest text-brand-muted-2">
                            <span>30 / dia</span>
                            <span class="text-brand-conhecimento">2.900 no total</span>
                            <span>100 / dia</span>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col gap-6 rounded-3xl border border-brand-line2 bg-brand-ink p-7">
                    <div class="flex items-end justify-between gap-4">
                        <div class="flex flex-col gap-1">
                            <span class="text-[13px] font-bold uppercase tracking-[2px] text-brand-muted-2">Dia {{ pushDay }}</span>
                            <span class="font-display text-[88px] font-black leading-none tabular-nums text-brand-cream">{{ pushReps }}</span>
                        </div>
                        <span class="font-display pb-1.5 text-[22px] font-bold uppercase text-brand-conhecimento">flexões</span>
                    </div>
                    <div class="flex h-[120px] items-end gap-0.5">
                        <div
                            v-for="(bar, index) in bars"
                            :key="index"
                            class="flex-1 rounded-t-sm"
                            :style="{ height: bar.h, background: bar.c }"
                        />
                    </div>
                    <input v-model.number="pushDay" type="range" min="1" max="45" aria-label="Dia do desafio" class="w-full" />
                    <div class="flex justify-between text-[13px] font-semibold text-brand-muted-2">
                        <span>Dia 1 · 30</span>
                        <span>Dia 23 · 60</span>
                        <span>Dia 45 · 100</span>
                    </div>
                </div>
            </div>
        </section>

        <section id="regras" class="mx-auto flex max-w-[1200px] flex-col gap-10 px-6 py-[clamp(72px,9vw,120px)]">
            <div class="flex flex-col gap-3.5">
                <div class="eyebrow">Como funciona</div>
                <h2 class="display-title m-0 text-[clamp(44px,6vw,80px)]">Regras simples.<br />Compromisso sério.</h2>
                <p class="m-0 max-w-[560px] text-[17px] leading-relaxed text-brand-muted">
                    Dá pra fazer sozinho ou com a sua turma. Quem for em grupo cria o próprio grupo — no WhatsApp e no Gymrats — e segue as regras de
                    grupo.
                </p>
            </div>

            <div class="grid grid-cols-[repeat(auto-fit,minmax(min(100%,250px),1fr))] gap-3">
                <div class="surface-card flex flex-col gap-3 p-7">
                    <span class="font-display text-xl font-extrabold text-brand-gold">01</span>
                    <h3 class="font-display m-0 text-[28px] font-extrabold uppercase leading-none">Check-in até 23h</h3>
                    <p class="m-0 text-base leading-normal text-brand-muted">Todo dia: foto + checklist dos 5 objetivos. Em grupo, manda no grupo de vocês.</p>
                </div>
                <div class="surface-card flex flex-col gap-3 p-7">
                    <span class="font-display text-xl font-extrabold text-brand-gold">02</span>
                    <h3 class="font-display m-0 text-[28px] font-extrabold uppercase leading-none">Ranking semanal</h3>
                    <p class="m-0 text-base leading-normal text-brand-muted">Em grupo: todo domingo sai quem está “on fire” na semana.</p>
                </div>
                <div class="surface-card flex flex-col gap-3 p-7">
                    <span class="font-display text-xl font-extrabold text-brand-gold">03</span>
                    <h3 class="font-display m-0 text-[28px] font-extrabold uppercase leading-none">Refeição luxo</h3>
                    <p class="m-0 text-base leading-normal text-brand-muted">
                        1x por semana — só na sexta à noite, sábado ou domingo. Pode ser o que quiser.
                    </p>
                </div>
                <div class="surface-card flex flex-col gap-3 p-7">
                    <span class="font-display text-xl font-extrabold text-brand-gold">04</span>
                    <h3 class="font-display m-0 text-[28px] font-extrabold uppercase leading-none">Fora disso</h3>
                    <p class="m-0 text-base leading-normal text-brand-muted">
                        Sem fast food, sem doce industrializado. Álcool só nos 3 eventos que você declarar no dia 1.
                    </p>
                </div>
            </div>

            <div
                id="penalidade"
                class="grid items-center gap-7 rounded-[26px] bg-brand-gold p-7 text-brand-ink lg:grid-cols-2 lg:gap-14 lg:p-12"
            >
                <div class="flex flex-col gap-[22px]">
                    <div class="flex flex-col gap-2.5">
                        <span class="text-sm font-bold uppercase tracking-[3px]">Regra de ouro</span>
                        <h3 class="display-title m-0 text-[clamp(48px,6vw,84px)] leading-[0.86]">Penalidade<br />solidária</h3>
                        <span class="font-script text-[clamp(24px,2.6vw,32px)] leading-tight">Transformar falhas em generosidade</span>
                    </div>
                    <p class="m-0 max-w-[520px] text-[19px] font-semibold leading-snug">
                        Cada dia que você não cumprir os 5 objetivos, você guarda <strong class="font-extrabold">R$ 10,00</strong> para doação.
                    </p>
                    <div class="flex flex-col border-t-[1.5px] border-brand-ink/25">
                        <div class="grid grid-cols-[minmax(0,190px)_minmax(0,1fr)] items-baseline gap-4 border-b-[1.5px] border-brand-ink/25 py-3.5">
                            <span class="font-display text-2xl font-extrabold uppercase leading-none">Falhou 1 dia?</span>
                            <span class="text-[17px] font-semibold leading-snug">R$ 10,00 para alguém na rua.</span>
                        </div>
                        <div class="grid grid-cols-[minmax(0,190px)_minmax(0,1fr)] items-baseline gap-4 border-b-[1.5px] border-brand-ink/25 py-3.5">
                            <span class="font-display text-2xl font-extrabold uppercase leading-none">Entrou atrasado?</span>
                            <span class="text-[17px] font-semibold leading-snug">Cada dia não cumprido = R$ 10,00.</span>
                        </div>
                        <div class="grid grid-cols-[minmax(0,190px)_minmax(0,1fr)] items-baseline gap-4 border-b-[1.5px] border-brand-ink/25 py-3.5">
                            <span class="font-display text-2xl font-extrabold uppercase leading-none">No final?</span>
                            <span class="text-[17px] font-semibold leading-snug">Doa seu valor acumulado e faz diferença real.</span>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-[22px] rounded-[22px] bg-brand-ink p-6 text-brand-cream lg:p-9">
                    <span class="text-[13px] font-bold uppercase tracking-[2px] text-brand-muted-2">Calcule sua doação</span>
                    <div class="flex items-center justify-between gap-4">
                        <span class="text-[17px] font-semibold text-brand-muted-3">Dias não cumpridos</span>
                        <div class="flex items-center gap-1.5">
                            <button
                                type="button"
                                aria-label="Menos um dia"
                                class="flex size-11 items-center justify-center rounded-full border-[1.5px] border-brand-line3 text-[22px] font-bold text-brand-cream hover:border-brand-cream"
                                @click="decFail"
                            >
                                −
                            </button>
                            <span class="font-display min-w-14 text-center text-[40px] font-extrabold leading-none tabular-nums">{{ fails }}</span>
                            <button
                                type="button"
                                aria-label="Mais um dia"
                                class="flex size-11 items-center justify-center rounded-full border-[1.5px] border-brand-line3 text-[22px] font-bold text-brand-cream hover:border-brand-cream"
                                @click="incFail"
                            >
                                +
                            </button>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-1">
                        <span v-for="(dot, index) in failDots" :key="index" class="size-2.5 rounded-sm" :style="{ background: dot.c }" />
                    </div>
                    <div class="flex flex-col gap-1.5 border-t border-brand-line2 pt-[18px]">
                        <span class="text-[13px] font-bold uppercase tracking-[2px] text-brand-muted-2">Valor acumulado para doar</span>
                        <span class="font-display text-[clamp(56px,6vw,76px)] font-black leading-[0.9] tabular-nums text-brand-gold">{{
                            failTotal
                        }}</span>
                        <span class="text-[15px] leading-normal text-brand-muted">{{ failNote }}</span>
                    </div>
                </div>
            </div>
        </section>

        <section class="mx-auto flex max-w-[1200px] flex-col gap-8 px-6 pb-[clamp(72px,9vw,120px)]">
            <div class="flex flex-wrap items-end justify-between gap-6">
                <h2 class="display-title m-0 text-[clamp(36px,4.6vw,60px)]">Sistema de pontos</h2>
                <span class="text-[15px] font-semibold text-brand-muted-2">Sugestão para quem for fazer em grupo</span>
            </div>
            <div class="grid grid-cols-[repeat(auto-fit,minmax(min(100%,320px),1fr))] gap-4">
                <div class="flex flex-col gap-1 rounded-[22px] border border-brand-line2 bg-brand-card2 p-7">
                    <span class="mb-2.5 text-[13px] font-bold uppercase tracking-[2px] text-brand-mente">Ganhar</span>
                    <div v-for="row in earn" :key="row.t" class="flex items-baseline justify-between gap-4 border-b border-brand-line2 py-3">
                        <span class="text-base text-brand-muted-3">{{ row.t }}</span>
                        <span class="font-display whitespace-nowrap text-[26px] font-extrabold text-brand-mente">{{ row.p }}</span>
                    </div>
                </div>
                <div class="flex flex-col gap-1 rounded-[22px] border border-brand-line2 bg-brand-card2 p-7">
                    <span class="mb-2.5 text-[13px] font-bold uppercase tracking-[2px] text-brand-corpo">Perder</span>
                    <div class="flex items-baseline justify-between gap-4 border-b border-brand-line2 py-3">
                        <span class="text-base text-brand-muted-3">Falhar 1 dia (+ R$ 10 na penalidade solidária)</span>
                        <span class="font-display text-[26px] font-extrabold text-brand-corpo">−5</span>
                    </div>
                    <span class="mb-2 mt-[22px] text-[13px] font-bold uppercase tracking-[2px] text-brand-muted-2">Punições divertidas</span>
                    <span class="text-[15px] leading-relaxed text-brand-muted">
                        Paga drinks na próxima saída · Foto “constrangida” pro grupo · Paga um café da manhã comunitário · Fica marcado na próxima
                        refeição luxo (paga pra todos)
                    </span>
                </div>
            </div>
            <div class="grid grid-cols-[repeat(auto-fit,minmax(min(100%,240px),1fr))] gap-4">
                <div class="flex items-center gap-[18px] rounded-[20px] border-[1.5px] border-brand-gold p-6">
                    <span class="font-display text-[56px] font-black italic leading-none text-brand-gold">1º</span>
                    <span class="text-base leading-snug text-brand-muted-3">Escolhe o <strong class="text-brand-cream">restaurante</strong> da celebração</span>
                </div>
                <div class="flex items-center gap-[18px] rounded-[20px] border-[1.5px] border-brand-silver p-6">
                    <span class="font-display text-[56px] font-black italic leading-none text-brand-silver">2º</span>
                    <span class="text-base leading-snug text-brand-muted-3">Escolhe o <strong class="text-brand-cream">bar/balada</strong> da celebração</span>
                </div>
                <div class="flex items-center gap-[18px] rounded-[20px] border-[1.5px] border-brand-bronze p-6">
                    <span class="font-display text-[56px] font-black italic leading-none text-brand-bronze">3º</span>
                    <span class="text-base leading-snug text-brand-muted-3">Escolhe a <strong class="text-brand-cream">atividade</strong> da celebração</span>
                </div>
            </div>
        </section>

        <section id="jornada" class="border-y border-brand-line bg-brand-strip">
            <div class="mx-auto flex max-w-[1200px] flex-col gap-12 px-6 py-[clamp(72px,9vw,120px)]">
                <div class="flex flex-col gap-3.5">
                    <div class="eyebrow">A jornada</div>
                    <h2 class="display-title m-0 text-[clamp(44px,6vw,80px)]">Do dia 1<br />ao dia 45</h2>
                </div>
                <div class="grid grid-cols-[repeat(auto-fit,minmax(min(100%,160px),1fr))] gap-3">
                    <div
                        v-for="mile in milestones"
                        :key="mile.day"
                        class="flex flex-col gap-3.5 rounded-[20px] px-[18px] py-[22px]"
                        :style="{ background: mile.bg, border: `1.5px solid ${mile.border}` }"
                    >
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-[13px] font-bold uppercase tracking-widest text-brand-muted-2">{{ mile.date }}</span>
                            <span class="text-[11px] font-bold uppercase tracking-widest" :style="{ color: mile.tagColor }">{{ mile.tag }}</span>
                        </div>
                        <span class="font-display text-[48px] font-black italic leading-[0.85]" :style="{ color: mile.numColor }">Dia {{ mile.day }}</span>
                        <span class="font-display text-[22px] font-extrabold uppercase leading-tight">{{ mile.title }}</span>
                        <span class="text-[15px] leading-normal text-brand-muted">{{ mile.line }}</span>
                    </div>
                </div>
            </div>
        </section>

        <section id="mindset" class="mx-auto flex max-w-[1200px] flex-col gap-10 px-6 pb-0 pt-[clamp(72px,9vw,120px)]">
            <div class="flex flex-wrap items-end justify-between gap-6">
                <div class="flex flex-col gap-3.5">
                    <div class="eyebrow">O mindset</div>
                    <h2 class="display-title m-0 text-[clamp(44px,6vw,80px)]">Sozinho ou<br />em grupo</h2>
                </div>
                <p class="m-0 max-w-[400px] text-[17px] leading-relaxed text-brand-muted">
                    O jeito de fazer muda. O espírito, não. Quatro princípios que valem pra todo mundo.
                </p>
            </div>

            <div class="flex flex-col border-t border-brand-line2">
                <div
                    v-for="item in [
                        {
                            n: '01',
                            title: 'Comunidade,<br>não competição',
                            solo: 'Sua única comparação é com quem você era ontem.',
                            group: 'O ranking é brincadeira. A meta é todo mundo cruzar a linha de chegada.',
                        },
                        {
                            n: '02',
                            title: 'Acolher,<br>não julgar',
                            solo: 'Falhou um dia? Sem culpa. Recomeça amanhã com mais vontade.',
                            group: 'Quem tropeçar recebe mão estendida, não dedo apontado.',
                        },
                        {
                            n: '03',
                            title: 'Celebrar<br>tudo',
                            solo: 'Primeiro treino, primeira oração, primeira skill aprendida — anota e comemora.',
                            group: 'Reação de fogo em cada check-in completo. A vitória de um é de todos.',
                        },
                        {
                            n: '04',
                            title: 'Lembrar<br>todo dia',
                            solo: 'Você não está só: tem fé, família e muita gente fazendo o mesmo desafio.',
                            group: '“A gente cresce junto” é o hino do desafio. No fim de todo post e de toda mensagem importante.',
                        },
                    ]"
                    :key="item.n"
                    class="flex flex-wrap items-start gap-5 border-b border-brand-line2 py-8 lg:gap-10"
                >
                    <div class="flex min-w-[280px] flex-1 items-baseline gap-[18px]">
                        <span class="font-display text-xl font-extrabold text-brand-gold">{{ item.n }}</span>
                        <h3 class="display-title m-0 text-[clamp(30px,3.4vw,44px)] leading-[0.95]" v-html="item.title" />
                    </div>
                    <div class="grid min-w-[280px] flex-[2] grid-cols-1 gap-6 sm:grid-cols-2">
                        <div class="flex flex-col gap-1.5">
                            <span class="text-xs font-bold uppercase tracking-[2px] text-brand-conhecimento">Sozinho</span>
                            <p class="m-0 text-base leading-relaxed text-brand-muted-3">{{ item.solo }}</p>
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <span class="text-xs font-bold uppercase tracking-[2px] text-brand-gold">Em grupo</span>
                            <p class="m-0 text-base leading-relaxed text-brand-muted-3">{{ item.group }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-center pt-2">
                <span class="font-script text-center text-[clamp(34px,5vw,60px)] leading-tight text-brand-gold">“A gente cresce junto”</span>
            </div>
        </section>

        <section class="mx-auto grid max-w-[1200px] items-start gap-12 px-6 py-[clamp(72px,9vw,120px)] lg:grid-cols-2">
            <div class="flex flex-col gap-6">
                <div class="eyebrow">Mais do que um desafio</div>
                <h2 class="display-title m-0 text-[clamp(44px,6vw,80px)]">Uma regra<br />de vida.</h2>
                <p class="m-0 max-w-[480px] text-[19px] leading-relaxed text-brand-muted-3">
                    A gente quer estar melhor no verão. Corpo mais definido, mente mais clara, espírito mais firme. Mas acima de tudo, a gente quer
                    <strong class="text-brand-cream">crescer junto</strong>. Amigos que se cobram, que se motivam, que se celebram.
                </p>
                <p class="m-0 max-w-[480px] text-[19px] leading-relaxed text-brand-muted-3">
                    Isso não é um desafio de 45 dias. É uma regra de vida que a gente vai repetir todo ano.
                </p>
            </div>
            <div class="grid grid-cols-2 gap-3.5">
                <div
                    class="flex aspect-square flex-col justify-between gap-3 overflow-hidden rounded-[22px] border border-brand-line2 border-t-4 border-t-brand-corpo bg-brand-card2 p-6"
                >
                    <div class="flex min-h-0 flex-1 items-center justify-center">
                        <BrandArt kind="corpo" />
                    </div>
                    <span class="font-display text-[clamp(24px,2.6vw,32px)] font-extrabold uppercase leading-[0.95]">Corpo<br />transformado</span>
                </div>
                <div
                    class="flex aspect-square flex-col justify-between gap-3 overflow-hidden rounded-[22px] border border-brand-line2 border-t-4 border-t-brand-familia bg-brand-card2 p-6"
                >
                    <div class="flex min-h-0 flex-1 items-center justify-center">
                        <BrandArt kind="mente" />
                    </div>
                    <span class="font-display text-[clamp(24px,2.6vw,32px)] font-extrabold uppercase leading-[0.95]">Mente<br />clara</span>
                </div>
                <div
                    class="flex aspect-square flex-col justify-between gap-3 overflow-hidden rounded-[22px] border border-brand-line2 border-t-4 border-t-brand-fe bg-brand-card2 p-6"
                >
                    <div class="flex min-h-0 flex-1 items-center justify-center">
                        <BrandArt kind="espirito" />
                    </div>
                    <span class="font-display text-[clamp(24px,2.6vw,32px)] font-extrabold uppercase leading-[0.95]">Espírito<br />firme</span>
                </div>
                <div
                    class="flex aspect-square flex-col justify-between gap-3 overflow-hidden rounded-[22px] border border-brand-line2 border-t-4 border-t-brand-gold bg-brand-card2 p-6"
                >
                    <div class="flex min-h-0 flex-1 items-center justify-center">
                        <BrandArt kind="amizade" />
                    </div>
                    <span class="font-display text-[clamp(24px,2.6vw,32px)] font-extrabold uppercase leading-[0.95]">Amizade<br />blindada</span>
                </div>
            </div>
        </section>

        <section id="entrar" class="bg-brand-gold text-brand-ink">
            <div class="mx-auto flex max-w-[1200px] flex-col items-center gap-6 px-6 py-[clamp(72px,10vw,128px)] text-center">
                <span class="font-script text-[clamp(28px,3.4vw,40px)] leading-none">A gente cresce junto</span>
                <h2 class="display-title m-0 text-[clamp(72px,13vw,180px)] tracking-[-2px] leading-[0.82]">Quem topa?</h2>
                <p class="m-0 max-w-[560px] text-[clamp(18px,1.8vw,22px)] font-semibold leading-snug">
                    Bora ficar 45 dias firme. 6 de novembro a 20 de dezembro — sozinho ou com seus amigos de verdade.
                </p>
                <div class="flex flex-wrap justify-center gap-3 pt-2">
                    <Link
                        :href="route('register')"
                        class="rounded-full bg-brand-ink px-[30px] py-[18px] text-[17px] font-bold text-brand-cream hover:bg-brand-line hover:text-brand-cream"
                    >
                        Criar conta
                    </Link>
                </div>
            </div>
        </section>

        <footer class="mx-auto flex max-w-[1200px] flex-wrap items-center justify-between gap-5 px-6 py-10">
            <div class="flex items-center gap-3">
                <img src="/images/logo.webp" alt="" class="size-[52px] object-contain" />
                <div class="flex flex-col gap-0.5">
                    <span class="font-display text-xl font-extrabold italic uppercase">
                        Desafio <span class="text-brand-gold">45</span> Firme
                    </span>
                    <span class="text-sm text-brand-muted-2">45 dias | Verão melhor | Amigos de vida · 6 nov — 20 dez</span>
                </div>
            </div>
            <span class="text-sm font-semibold text-brand-muted-2">#Desafio45Firme #VerãoMelhor #TransformaçãoReal #AmigosDeVida</span>
        </footer>
    </div>
</template>
