<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

type Props = {
    className?: HTMLAttributes['class'];
};

defineProps<Props>();
</script>

<template>
    <svg
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 64 64"
        :class="className"
        v-bind="$attrs"
    >
        <g
            fill="none"
            stroke="currentColor"
            stroke-width="3.5"
            stroke-linecap="round"
            stroke-linejoin="round"
        >
            <rect x="8" y="10" width="48" height="46" rx="8" />
            <path d="M20 6v8M44 6v8M8 22h48" />
            <path d="m19 37 8 8 14-14" />
            <path d="M43 45a11 11 0 1 0 22 0 11 11 0 1 0-22 0" />
            <path d="M54 39v6l4 3" />
        </g>
    </svg>
</template>
