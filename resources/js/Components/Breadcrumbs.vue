<script setup>
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    crumbs: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <nav class="flex items-center text-sm px-4 py-2" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-2">
            <li v-for="(crumb, index) in crumbs" :key="crumb.href" class="flex items-center">
                <Link v-if="index < crumbs.length - 1" :href="crumb.href"
                    class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 transition-colors duration-200">
                    {{ crumb.label }}
                </Link>
                <span v-else class="text-gray-800 dark:text-gray-50 font-semibold">
                    {{ crumb.label }}
                </span>
                <svg v-if="index < crumbs.length - 1" class="flex-shrink-0 h-4 w-4 text-gray-400 dark:text-gray-600 mx-2"
                    fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                    <path d="M5.555 17.776l8-16 .894.448-8 16-.894-.448z" />
                </svg>
            </li>
        </ol>
    </nav>
</template>
