<script setup>
import { Link } from '@inertiajs/vue3';
import { HomeIcon, ChevronRightIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    crumbs: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-2">
            <li>
                <div>
                    <Link :href="route('dashboard')" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                        <HomeIcon class="h-5 w-5 flex-shrink-0" aria-hidden="true" />
                        <span class="sr-only">Home</span>
                    </Link>
                </div>
            </li>
            <li v-for="(crumb, index) in crumbs.slice(1)" :key="crumb.href" class="flex items-center">
                <ChevronRightIcon class="h-5 w-5 flex-shrink-0 text-gray-400" aria-hidden="true" />
                <Link
                    v-if="index < crumbs.slice(1).length - 1"
                    :href="crumb.href"
                    class="ml-2 text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300"
                >
                    {{ crumb.label }}
                </Link>
                <span
                    v-else
                    class="ml-2 text-sm font-medium text-gray-900 dark:text-white"
                    aria-current="page"
                >
                    {{ crumb.label }}
                </span>
            </li>
        </ol>
    </nav>
</template>
