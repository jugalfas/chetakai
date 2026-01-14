<script setup>
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import { Link } from '@inertiajs/vue3';
import { Bars3Icon, UserCircleIcon } from '@heroicons/vue/24/outline';
import DarkModeToggle from '@/Components/DarkModeToggle.vue';

const props = defineProps({
    userName: String,
    isSidebarCollapsed: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['toggleSidebar', 'toggleMobileMenu']);
</script>

<template>
    <div class="sticky top-0 z-40 flex h-16 shrink-0 items-center gap-x-4 border-b border-b-border bg-background px-4 shadow-sm sm:gap-x-6 sm:px-6 lg:px-8">
        <!-- Mobile menu button -->
        <button
            @click="$emit('toggleMobileMenu')"
            class="-m-2.5 p-2.5 text-gray-400 dark:text-gray-300 lg:hidden"
        >
            <Bars3Icon class="h-6 w-6" />
        </button>

        <!-- Desktop sidebar toggle button -->
        <button
            @click="$emit('toggleSidebar')"
            class="-m-2.5 p-2.5 text-gray-400 dark:text-gray-300 hidden lg:block"
        >
            <Bars3Icon class="h-6 w-6" />
        </button>

        <!-- Separator -->
        <div class="h-6 w-px bg-border lg:hidden" aria-hidden="true" />

        <div class="flex flex-1 gap-x-4 self-stretch lg:gap-x-6">
            <div class="flex flex-1"></div>
            <div class="flex items-center gap-x-4 lg:gap-x-6">
                <!-- Dark mode toggle -->
                <DarkModeToggle />

                <!-- Notifications -->
                <button class="relative p-2 text-gray-400 hover:text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.248 24.248 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                    </svg>
                    <!-- Notification badge -->
                    <span class="absolute -top-1 -right-1 h-4 w-4 rounded-full bg-red-500 text-xs text-white flex items-center justify-center">
                        3
                    </span>
                </button>

                <!-- Profile dropdown -->
                <Dropdown align="right" width="48">
                    <template #trigger>
                        <button class="flex items-center gap-x-2 rounded-full bg-[#2D3A4F] p-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                            <UserCircleIcon class="h-8 w-8 text-gray-400" />
                            <span class="hidden lg:block text-sm font-semibold text-white">
                                {{ userName }}
                            </span>
                            <svg class="hidden lg:block h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </template>

                    <template #content>
                        <DropdownLink :href="route('profile.edit')" class="text-gray-400 hover:text-white hover:bg-[#2D3A4F]">
                            <UserCircleIcon class="mr-2 h-4 w-4" />
                            Profile
                        </DropdownLink>
                        <DropdownLink :href="route('logout')" method="post" as="button" class="text-gray-400 hover:text-white hover:bg-[#2D3A4F]">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mr-2 h-4 w-4 shrink-0 text-gray-400 group-hover:text-white">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                            </svg>
                            Log Out
                        </DropdownLink>
                    </template>
                </Dropdown>
            </div>
        </div>
    </div>
</template>
