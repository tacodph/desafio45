<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem, type SharedData } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { ClipboardCheck, Droplets, Dumbbell, Flame, LayoutGrid, Lightbulb, Salad, Settings, Star, Users } from 'lucide-vue-next';
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

const mainNavItems = computed<NavItem[]>(() => {
    if (role.value === 'administrador') {
        return [
            { title: 'Painel', href: '/dashboard', icon: LayoutGrid },
            { title: 'Usuários', href: '/admin/usuarios', icon: Users },
            { title: 'Objetivos', href: '/objetivos/alimentacao', icon: Salad },
            { title: 'Configurações', href: '/settings/profile', icon: Settings },
        ];
    }

    if (role.value === 'atleta') {
        return [
            { title: 'Painel', href: '/dashboard', icon: LayoutGrid },
            { title: 'Evolução', href: '/atleta/evolucao', icon: ClipboardCheck },
            { title: 'Alimentação', href: '/objetivos/alimentacao', icon: Salad, children: [{ title: 'Lista de compras', href: '/objetivos/alimentacao/compras' }] },
            { title: 'Treino', href: '/objetivos/treino', icon: Dumbbell },
            { title: 'Hidratação', href: '/objetivos/hidratacao', icon: Droplets },
            { title: 'Habilidade', href: '/objetivos/habilidade', icon: Lightbulb },
            { title: 'Plus', href: '/objetivos/plus', icon: Star },
        ];
    }

    return [
        { title: 'Painel', href: '/dashboard', icon: LayoutGrid },
        { title: 'Atletas', href: '/equipe/atletas', icon: Users },
        { title: 'Objetivos', href: '/objetivos/alimentacao', icon: Salad },
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
