<script setup lang="ts">
import { BONUS_POINTS, DAILY_POINTS, useChallenge } from '@/composables/useChallenge';

import { images } from '@/data/images';
import { Head, Link } from '@inertiajs/vue3';
import { Menu, X } from 'lucide-vue-next';
import { ref } from 'vue';

const { countdown, goals, fails, failTotal, failNote, failDots, incFail, decFail, milestones } = useChallenge();

const menuOpen = ref(false);

const navLinks = [
    { href: '#objetivos', label: 'Objetivos' },
    { href: '#regras', label: 'Regras' },
    { href: '#jornada', label: 'Jornada' },
    { href: '#mindset', label: 'Mindset' },
    { href: '#nutri', label: 'Nutri' },
    { href: '#parceiros', label: 'Parceiros' },
];
</script>

<template>
    <Head title="A gente cresce junto" />

    <div class="min-h-screen overflow-x-hidden bg-brand-ink text-brand-cream">
        <header class="sticky top-0 z-20 border-b border-brand-line bg-brand-ink/88 backdrop-blur-[10px]">
            <div class="mx-auto flex max-w-[1200px] items-center justify-between gap-4 px-6 py-3">
                <a href="#topo" class="flex items-center gap-2.5 text-brand-cream hover:text-brand-cream">
                    <img :src="images.logo" alt="Desafio 45 Dias" class="size-11 object-contain" />
                    <span class="font-display text-[22px] font-extrabold italic uppercase tracking-wide">
                        Desafio <span class="text-brand-gold">45</span> Dias
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
                    <span class="text-brand-gold">45 dias.</span><br />5 objetivos.<br />Antes do verão.
                </h1>
                <p class="m-0 max-w-[520px] text-[clamp(18px,1.6vw,21px)] leading-normal text-brand-muted-3">
                    De 6 de novembro a 20 de dezembro de 2026. Alimentação, movimento, água, sono e uma habilidade nova. O desafio
                    premia consistência e participação, não perfeição.
                </p>
                <div class="flex flex-wrap gap-3">
                    <Link
                        :href="route('participar')"
                        class="inline-flex items-center gap-2.5 rounded-full bg-brand-gold px-7 py-4 text-[17px] font-bold text-brand-ink hover:bg-brand-gold-hover hover:text-brand-ink"
                    >
                        Quem topa? <span>→</span>
                    </Link>
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
                    :src="images.logo"
                    alt="Emblema Desafio 45 Dias — antes do verão. A gente cresce junto"
                    class="relative block h-auto w-full max-w-[580px]"
                />
            </div>
        </section>

        <section id="alimentacao" class="border-y border-brand-line bg-brand-strip">
            <div class="mx-auto flex max-w-[760px] flex-col gap-5 px-6 py-[clamp(72px,9vw,120px)]">
                <img :src="images.alimentacao" alt="" class="size-24" />
                <div class="text-sm font-bold uppercase tracking-[3px] text-[#43A047]">Alimentação e organização</div>
                <p class="m-0 text-[18px] leading-relaxed text-brand-muted">
                    O Desafio 45 Dias é um compromisso de 45 dias seguidos, de sexta, 6 de novembro, a domingo, 20 de dezembro de 2026. O último dia cai na véspera do início do verão (21 de dezembro), daí o subtítulo "Antes do verão".
                </p>
                <p class="m-0 text-[18px] leading-relaxed text-brand-muted">
                    São 5 objetivos que cuidam do movimento, da hidratação e do sono, da autonomia na rotina, da alimentação e de pequenas experiências que deixam a vida mais prazerosa.
                </p>
                <p class="m-0 text-[18px] leading-relaxed text-brand-muted">
                    Você vai treinar seu autocontrole, experimentar coisas novas e encarar desafios semanais com metas possíveis, que vão te mostrar do que você é capaz quando se compromete com suas escolhas.
                </p>
                <Link :href="route('objetivos.show', 'alimentacao')" class="w-fit text-sm font-bold uppercase tracking-wider text-[#43A047]">
                    Ver a regra →
                </Link>
            </div>
        </section>

        <div class="border-y border-brand-line bg-brand-strip">
            <div
                class="font-display mx-auto flex max-w-[1200px] flex-wrap justify-between gap-x-8 gap-y-4 px-6 py-[18px] text-[clamp(18px,2vw,24px)] font-bold uppercase tracking-[2px]"
            >
                <span v-for="goal in goals" :key="goal.key" :style="{ color: goal.color }">{{ goal.title }}</span>
            </div>
        </div>

        <section id="objetivos" class="mx-auto max-w-[1200px] px-6 py-[clamp(72px,9vw,120px)]">
            <div class="mb-10 flex flex-wrap items-end justify-between gap-6">
                <div class="flex flex-col gap-3.5">
                    <div class="eyebrow">Consistência, não perfeição</div>
                    <h2 class="display-title m-0 text-[clamp(44px,6vw,80px)]">5 objetivos</h2>
                </div>
                <p class="m-0 max-w-[360px] text-[17px] leading-normal text-brand-muted">
                    Nem tudo é diário. Toque em cada objetivo para ver a regra, por que funciona e as dicas.
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
                    <p class="m-0 text-base leading-normal text-brand-muted">Foto do dia e o checklist: alimentação, treino, água e sono, habilidade e, se rolou, o Plus.</p>
                </div>
                <div class="surface-card flex flex-col gap-3 p-7">
                    <span class="font-display text-xl font-extrabold text-brand-gold">02</span>
                    <h3 class="font-display m-0 text-[28px] font-extrabold uppercase leading-none">Ranking de domingo</h3>
                    <p class="m-0 text-base leading-normal text-brand-muted">A soma da semana sai todo domingo. O geral acompanha os 45 dias.</p>
                </div>
                <div class="surface-card flex flex-col gap-3 p-7">
                    <span class="font-display text-xl font-extrabold text-brand-gold">03</span>
                    <h3 class="font-display m-0 text-[28px] font-extrabold uppercase leading-none">Sem punição</h3>
                    <p class="m-0 text-base leading-normal text-brand-muted">Falhou um dia? Avisa o grupo. O dia só deixa de pontuar. Amanhã conta de novo.</p>
                </div>
                <div class="surface-card flex flex-col gap-3 p-7">
                    <span class="font-display text-xl font-extrabold text-brand-gold">04</span>
                    <h3 class="font-display m-0 text-[28px] font-extrabold uppercase leading-none">Máximo 10</h3>
                    <p class="m-0 text-base leading-normal text-brand-muted">Check-in, alimentação, treino, água, sono e habilidade. O Plus entra como bônus.</p>
                </div>
            </div>

            <div
                id="penalidade"
                class="grid items-center gap-7 rounded-[26px] bg-brand-gold p-7 text-brand-ink lg:grid-cols-2 lg:gap-14 lg:p-12"
            >
                <div class="flex flex-col gap-[22px]">
                    <div class="flex flex-col gap-2.5">
                        <span class="text-sm font-bold uppercase tracking-[3px]">Opcional, no dia 1</span>
                        <h3 class="display-title m-0 text-[clamp(48px,6vw,84px)] leading-[0.86]">Penalidade<br />solidária</h3>
                        <span class="font-script text-[clamp(24px,2.6vw,32px)] leading-tight">Se o grupo combinar</span>
                    </div>
                    <p class="m-0 max-w-[520px] text-[19px] font-semibold leading-snug">
                        Quem topar, guarda <strong class="font-extrabold">R$ 10,00</strong> por dia sem check-in. No fim, vira doação ou a comemoração.
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
                <span class="text-[15px] font-semibold text-brand-muted-2">Máximo de 10 pontos por dia</span>
            </div>
            <div class="grid grid-cols-[repeat(auto-fit,minmax(min(100%,320px),1fr))] gap-4">
                <div class="flex flex-col gap-1 rounded-[22px] border border-brand-line2 bg-brand-card2 p-7">
                    <span class="mb-2.5 text-[13px] font-bold uppercase tracking-[2px] text-brand-mente">No dia</span>
                    <div v-for="row in DAILY_POINTS" :key="row.t" class="flex items-baseline justify-between gap-4 border-b border-brand-line2 py-3">
                        <span class="text-base text-brand-muted-3">{{ row.t }}</span>
                        <span class="font-display whitespace-nowrap text-[26px] font-extrabold text-brand-mente">{{ row.p }}</span>
                    </div>
                </div>
                <div class="flex flex-col gap-1 rounded-[22px] border border-brand-line2 bg-brand-card2 p-7">
                    <span class="mb-2.5 text-[13px] font-bold uppercase tracking-[2px] text-brand-gold">Bônus</span>
                    <div v-for="row in BONUS_POINTS" :key="row.t" class="flex items-baseline justify-between gap-4 border-b border-brand-line2 py-3">
                        <span class="text-base text-brand-muted-3">{{ row.t }}</span>
                        <span class="font-display whitespace-nowrap text-[26px] font-extrabold text-brand-gold">{{ row.p }}</span>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-[repeat(auto-fit,minmax(min(100%,240px),1fr))] gap-4">
                <div class="flex items-center gap-[18px] rounded-[20px] border-[1.5px] border-brand-gold p-6">
                    <span class="font-display text-[56px] font-black italic leading-none text-brand-gold">1º</span>
                    <span class="text-base leading-snug text-brand-muted-3">Escolhe o <strong class="text-brand-cream">restaurante</strong> da celebração</span>
                </div>
                <div class="flex items-center gap-[18px] rounded-[20px] border-[1.5px] border-brand-silver p-6">
                    <span class="font-display text-[56px] font-black italic leading-none text-brand-silver">2º</span>
                    <span class="text-base leading-snug text-brand-muted-3">Escolhe o <strong class="text-brand-cream">programa</strong> depois da celebração</span>
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
                            solo: 'Primeira marmita, primeiro treino, primeira habilidade — anota e comemora.',
                            group: 'Reação de fogo em cada check-in completo. A vitória de um é de todos.',
                        },
                        {
                            n: '04',
                            title: 'Lembrar<br>todo dia',
                            solo: 'Você não está só: tem gente fazendo o mesmo desafio, no seu ritmo.',
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
                    A gente quer chegar melhor no verão. Cada um no próprio nível, com o grupo cobrando, motivando e celebrando. Acima de tudo, a gente
                    quer <strong class="text-brand-cream">crescer junto</strong>.
                </p>
                <p class="m-0 max-w-[480px] text-[19px] leading-relaxed text-brand-muted-3">
                    Isso não é um desafio de 45 dias. É uma regra de vida que a gente vai repetir todo ano.
                </p>
            </div>
            <div class="grid grid-cols-2 gap-3.5 sm:grid-cols-3">
                <div
                    v-for="goal in goals"
                    :key="goal.key"
                    class="flex flex-col justify-between gap-3 overflow-hidden rounded-[22px] border border-brand-line2 bg-brand-card2 p-5"
                    :style="{ borderTop: `4px solid ${goal.color}` }"
                >
                    <img :src="goal.icon" alt="" class="size-20 object-contain" />
                    <span class="font-display text-[clamp(22px,2.4vw,28px)] font-extrabold uppercase leading-[0.95]">{{ goal.title }}</span>
                </div>
            </div>
        </section>

        <section id="nutri" class="border-t border-brand-line bg-brand-strip">
            <div class="mx-auto flex max-w-[1200px] flex-col gap-8 px-6 py-[clamp(72px,9vw,120px)]">
                <div class="flex flex-col gap-3.5">
                    <div class="eyebrow">Alimentação</div>
                    <h2 class="display-title m-0 text-[clamp(44px,6vw,80px)]">Cantinho da nutri</h2>
                    <p class="m-0 max-w-[560px] text-[17px] leading-relaxed text-brand-muted">
                        Praticidade e equilíbrio, sem terrorismo. O conteúdo de apoio fica com a nutricionista do desafio.
                    </p>
                </div>
                <div class="grid grid-cols-[repeat(auto-fit,minmax(min(100%,220px),1fr))] gap-3">
                    <div v-for="item in ['Listas de compras', 'Cardápios da semana', 'Kit da vida real', 'Comer sem tudo ou nada']" :key="item" class="surface-card p-6">
                        <h3 class="font-display m-0 text-2xl font-extrabold uppercase leading-tight">{{ item }}</h3>
                    </div>
                </div>
            </div>
        </section>

        <section id="parceiros" class="mx-auto flex max-w-[1200px] flex-col gap-10 px-6 py-[clamp(72px,9vw,120px)]">
            <div class="flex flex-col gap-3.5">
                <div class="eyebrow">Quem caminha com a gente</div>
                <h2 class="display-title m-0 text-[clamp(44px,6vw,80px)]">Parceiros</h2>
            </div>

            <article class="grid items-center gap-8 overflow-hidden rounded-[22px] border border-brand-line2 bg-brand-card2 p-5 sm:p-8 lg:grid-cols-[320px_1fr]">
                <img :src="images.helen" alt="Helen Freitas" class="aspect-[3/4] w-full rounded-[18px] object-cover object-top" />
                <div class="flex flex-col gap-5">
                    <span class="text-xs font-bold uppercase tracking-[2px] text-brand-gold">Nutricionista</span>
                    <h3 class="display-title m-0 text-[clamp(40px,5vw,64px)] leading-none">Helen Freitas</h3>
                    <div class="flex flex-col gap-3 text-[17px]">
                        <a
                            href="https://www.instagram.com/helenfreitasnutri/"
                            target="_blank"
                            rel="noopener"
                            class="font-semibold text-brand-cream hover:text-brand-gold"
                        >
                            Instagram · @helenfreitasnutri
                        </a>
                        <a href="https://helenfreitasnutri.com.br/" target="_blank" rel="noopener" class="font-semibold text-brand-cream hover:text-brand-gold">
                            Site · helenfreitasnutri.com.br
                        </a>
                        <a href="tel:+5561992098384" class="font-semibold text-brand-cream hover:text-brand-gold">Telefone · (61) 99209-8384</a>
                    </div>
                </div>
            </article>
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
                <img :src="images.logo" alt="" class="size-[52px] object-contain" />
                <div class="flex flex-col gap-0.5">
                    <span class="font-display text-xl font-extrabold italic uppercase">
                        Desafio <span class="text-brand-gold">45</span> Dias
                    </span>
                    <span class="text-sm text-brand-muted-2">Antes do verão · 6 nov — 20 dez</span>
                </div>
            </div>
            <span class="text-sm font-semibold text-brand-muted-2">#Desafio45Dias #AntesDoVerao #AGenteCresceJunto</span>
        </footer>
    </div>
</template>
