<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

interface Props {
    href: string;
    tabindex?: number;
    method?: string;
    as?: string;
}

defineProps<Props>();
</script>

<template>
    <Link
        :href="href"
        :tabindex="tabindex"
        :method="method"
        :as="as"
        class="text-brand-gold underline decoration-brand-gold/40 underline-offset-4 transition-colors duration-300 ease-out hover:text-brand-gold-hover hover:decoration-brand-gold-hover"
    >
        <slot />
    </Link>
</template>
