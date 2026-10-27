<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavGroupItem, type NavItem, type SharedData } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { Apple, ClipboardCheck, Droplets, Dumbbell, Flag, Flame, LayoutGrid, Lightbulb, Salad, Settings, Star, Trophy, Users } from 'lucide-vue-next';
import { computed } from 'vue';
import AppLogo from './AppLogo.vue';

const page = usePage<SharedData>();
const role = computed(() => page.props.auth.user?.user_type?.name ?? 'atleta');

const menuLabel = computed(() => {
    if (role.value === 'administrador') {
        return 'Administração';
    }

    if (role.value === 'atleta') {
        return 'Meus objetivos';
    }

    return 'Equipe';
});

const objective = (goal: string) => ({ title: 'Objetivo', href: `/meus-objetivos/${goal}` });

const mainNavItems = computed<NavGroupItem[]>(() => {
    if (role.value === 'administrador') {
        return [
            { title: 'Painel', href: '/dashboard', icon: LayoutGrid },
            { title: 'Usuários', href: '/admin/usuarios', icon: Users },
            { title: 'Alimentos', href: '/admin/alimentos', icon: Apple },
            { title: 'Objetivos', href: '/meus-objetivos/alimentacao', icon: Salad },
            { title: 'Pontuação', href: '/pontuacao', icon: Trophy },
            { title: 'Configurações', href: '/settings/profile', icon: Settings },
        ];
    }

    if (role.value === 'atleta') {
        return [
            { title: 'Painel', href: '/dashboard', icon: LayoutGrid },
            {
                title: 'Ponto de partida',
                icon: Flag,
                children: [
                    { title: 'Primeiro dia', href: '/atleta/ponto-de-partida/primeiro-dia' },
                    { title: 'Metas', href: '/atleta/ponto-de-partida/metas' },
                ],
            },
            {
                title: 'Evolução',
                icon: ClipboardCheck,
                children: [
                    { title: 'Dias', href: '/atleta/evolucao/dias' },
                    { title: 'Conquistas', href: '/atleta/evolucao/conquistas' },
                    { title: 'Resumo', href: '/atleta/evolucao/resumo' },
                    { title: 'Pontuação', href: '/pontuacao' },
                ],
            },
            { title: 'Alimentação', icon: Salad, children: [objective('alimentacao'), { title: 'Monte seu Cardápio', href: '/objetivos/alimentacao/compras' }] },
            { title: 'Treino', icon: Dumbbell, children: [objective('treino')] },
            { title: 'Hidratação', icon: Droplets, children: [objective('hidratacao')] },
            { title: 'Habilidade', icon: Lightbulb, children: [objective('habilidade')] },
            { title: 'Plus', icon: Star, children: [objective('plus')] },
        ];
    }

    return [
        { title: 'Painel', href: '/dashboard', icon: LayoutGrid },
        { title: 'Atletas', href: '/equipe/atletas', icon: Users },
        { title: 'Objetivos', href: '/meus-objetivos/alimentacao', icon: Salad },
        { title: 'Pontuação', href: '/pontuacao', icon: Trophy },
    ];
});

const footerNavItems: NavItem[] = [
    {
        title: 'Instagram',
        href: 'https://www.instagram.com/desafio60.firme/',
        icon: Flame,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route('dashboard')">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" :label="menuLabel" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
