<template>
    <span
        class="whitespace-nowrap inline-flex items-center rounded-md text-xs transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 hover-elevate border-transparent shadow-xs border-0 px-3 py-1 font-bold"
        :class="variantClasses"
    >
        <slot />
    </span>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    variant: {
        type: String,
        default: 'default',
        validator: (value) => ['default', 'primary', 'secondary', 'success', 'warning', 'danger'].includes(value),
    },
    size: {
        type: String,
        default: 'sm',
        validator: (value) => ['xs', 'sm', 'md'].includes(value),
    },
});

const variantClasses = computed(() => {
    const baseClasses = 'inline-flex items-center rounded-full text-xs font-medium';

    const sizeClasses = {
        xs: 'px-2 py-0.5 text-xs',
        sm: 'px-2.5 py-0.5 text-xs',
        md: 'px-3 py-1 text-sm',
    };

    const variantClasses = {
        default: 'bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200',
        primary: 'bg-primary-100 dark:bg-primary-900 text-primary-800 dark:text-primary-200',
        secondary: 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200',
        success: 'bg-emerald-500/20 text-emerald-500 hover:bg-emerald-500/20',
        warning: 'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200',
        danger: 'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200',
    };

    return `${sizeClasses[props.size]} ${variantClasses[props.variant]}`;
});
</script>
