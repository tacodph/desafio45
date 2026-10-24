<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Administração', href: '/dashboard' },
    { title: 'Usuários', href: '/admin/usuarios' },
];

defineProps<{
    users: { id: number; name: string; email: string; user_type: { name: string } | null }[];
}>();
</script>

<template>
    <Head title="Usuários" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <h1 class="display-title text-[clamp(36px,5vw,56px)]">Usuários</h1>
                <Link :href="route('admin.users.create')" class="surface-card px-5 py-4 font-display text-lg font-bold uppercase">
                    Cadastrar usuário
                </Link>
            </div>

            <div class="surface-card overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="text-[13px] uppercase tracking-[2px] text-brand-muted-2">
                        <tr>
                            <th class="px-5 py-4 font-bold">Nome</th>
                            <th class="px-5 py-4 font-bold">E-mail</th>
                            <th class="px-5 py-4 font-bold">Tipo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in users" :key="user.id" class="border-t border-brand-line">
                            <td class="px-5 py-4 font-semibold">{{ user.name }}</td>
                            <td class="px-5 py-4 text-brand-muted">{{ user.email }}</td>
                            <td class="px-5 py-4 uppercase">{{ user.user_type?.name }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>
