<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

interface Props {
    className?: HTMLAttributes['class'];
}

defineProps<Props>();
</script>

<template>
    <img
        src="/images/logo.webp"
        alt="Desafio 45 Firme"
        :class="className"
        class="object-contain"
        v-bind="$attrs"
    />
</template>
