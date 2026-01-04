<script setup>
import { Link } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import NavLink from '@/Components/NavLink.vue';
import { HomeIcon, ChatBubbleLeftRightIcon, SparklesIcon, TagIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    isCollapsed: {
        type: Boolean,
        default: false,
    },
});
</script>

<template>
    <aside :class="['bg-gray-50 dark:bg-gray-900 shadow-lg flex-shrink-0 h-screen overflow-hidden transition-all duration-300 ease-in-out flex flex-col fixed', isCollapsed ? 'w-20' : 'w-64']">
        <div class="h-16 flex items-center justify-center border-b border-gray-200 dark:border-gray-700">
            <Link :href="route('dashboard')">
                <ApplicationLogo :class="['block h-9 w-auto fill-current text-gray-800 dark:text-white', isCollapsed ? 'hidden' : '']" />
            </Link>
            <span v-if="isCollapsed" class="text-gray-800 dark:text-white text-lg font-semibold">App</span>
        </div>
        <nav class="mt-6 flex flex-col gap-2">
            <NavLink :href="route('dashboard')" :active="route().current('dashboard')" :class="['block px-4 py-3 text-base font-medium rounded-lg mx-2 my-1 transition-colors duration-200 w-full flex items-center', route().current('dashboard') ? 'bg-primary-100 dark:bg-primary-700 text-primary-700 dark:text-primary-100 border-l-4 border-primary-500' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700', isCollapsed ? 'justify-center' : '']">
                <div class="flex items-center" :class="{'justify-center': isCollapsed}">
                    <HomeIcon class="h-5 w-5" :class="route().current('dashboard') ? 'text-primary-700 dark:text-primary-100' : ''" />
                    <span :class="{'ml-3': !isCollapsed}">Dashboard</span>
                </div>
            </NavLink>
            <NavLink :href="route('chat.index')" :active="route().current('chat.index')" :class="['block px-4 py-3 text-base font-medium rounded-lg mx-2 my-1 transition-colors duration-200 w-full flex items-center', route().current('chat.index') ? 'bg-primary-100 dark:bg-primary-700 text-primary-700 dark:text-primary-100 border-l-4 border-primary-500' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700', isCollapsed ? 'justify-center' : '']">
                <div class="flex items-center" :class="{'justify-center': isCollapsed}">
                    <ChatBubbleLeftRightIcon class="h-5 w-5" :class="route().current('chat.index') ? 'text-primary-700 dark:text-primary-100' : ''" />
                    <span :class="{'ml-3': !isCollapsed}">Chat</span>
                </div>
            </NavLink>
            <NavLink :href="route('quotes.index')" :active="route().current('quotes.index')" :class="['block px-4 py-3 text-base font-medium rounded-lg mx-2 my-1 transition-colors duration-200 w-full flex items-center', route().current('quotes.index') ? 'bg-primary-100 dark:bg-primary-700 text-primary-700 dark:text-primary-100 border-l-4 border-primary-500' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700', isCollapsed ? 'justify-center' : '']">
                <div class="flex items-center" :class="{'justify-center': isCollapsed}">
                    <SparklesIcon class="h-5 w-5" :class="route().current('quotes.index') ? 'text-primary-700 dark:text-primary-100' : ''" />
                    <span :class="{'ml-3': !isCollapsed}">Quotes</span>
                </div>
            </NavLink>
            <NavLink :href="route('categories.index')" :active="route().current('categories.index')" :class="['block px-4 py-3 text-base font-medium rounded-lg mx-2 my-1 transition-colors duration-200 w-full flex items-center', route().current('categories.index') ? 'bg-primary-100 dark:bg-primary-700 text-primary-700 dark:text-primary-100 border-l-4 border-primary-500' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700', isCollapsed ? 'justify-center' : '']">
                <div class="flex items-center" :class="{'justify-center': isCollapsed}">
                    <TagIcon class="h-5 w-5" :class="route().current('categories.index') ? 'text-primary-700 dark:text-primary-100' : ''" />
                    <span :class="{'ml-3': !isCollapsed}">Categories</span>
                </div>
            </NavLink>
        </nav>
    </aside>
</template>
