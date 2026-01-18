<script setup>
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import { Link } from '@inertiajs/vue3';
import { Bars3Icon, UserCircleIcon, Cog6ToothIcon } from '@heroicons/vue/24/outline';
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
    <div
        class="sticky top-0 z-40 flex h-16 shrink-0 items-center gap-x-4 border-b border-b-border bg-background px-4 shadow-sm sm:gap-x-6 sm:px-6 lg:px-8">
        <!-- Mobile menu button -->
        <button @click="$emit('toggleMobileMenu')" class="-m-2.5 p-2.5 text-gray-400 dark:text-gray-300 lg:hidden">
            <Bars3Icon class="h-6 w-6" />
        </button>

        <!-- Desktop sidebar toggle button -->
        <button @click="$emit('toggleSidebar')" class="-m-2.5 p-2.5 text-gray-400 dark:text-gray-300 hidden lg:block">
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
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.248 24.248 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                    </svg>
                    <!-- Notification badge -->
                    <span
                        class="absolute -top-1 -right-1 h-4 w-4 rounded-full bg-red-500 text-xs text-white flex items-center justify-center">
                        3
                    </span>
                </button>

                <!-- Profile dropdown -->
                <Dropdown align="right" width="48" :content-classes="'py-1 rounded-md p-1'">
                    <template #trigger>
                        <button
                            class="inline-flex items-center justify-center whitespace-nowrap text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 hover-elevate active-elevate-2 border border-transparent min-h-9 px-4 py-2 pl-2 pr-1 gap-2 h-9 rounded-full hover:bg-sidebar-accent"
                            type="button" id="radix-_r_3_" aria-haspopup="menu" aria-expanded="false"
                            data-state="closed">
                            <span
                                class="relative flex shrink-0 overflow-hidden rounded-full h-7 w-7 border border-sidebar-border">
                                <img class="aspect-square h-full w-full"
                                    :src="$page.props.auth.user.avatar || 'https://github.com/shadcn.png'">
                            </span>
                            <span class="text-sm font-medium text-sidebar-foreground hidden sm:inline-block">
                                {{ userName }}
                            </span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round"
                                class="lucide lucide-chevron-down h-4 w-4 text-sidebar-foreground/50"
                                aria-hidden="true">
                                <path d="m6 9 6 6 6-6"></path>
                            </svg>
                        </button>
                    </template>

                    <template #content
                        class="z-50 max-h-[var(--radix-dropdown-menu-content-available-height)] overflow-y-auto overflow-x-hidden rounded-md border p-1 shadow-md data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0 data-[state=closed]:zoom-out-95 data-[state=open]:zoom-in-95 data-[side=bottom]:slide-in-from-top-2 data-[side=left]:slide-in-from-right-2 data-[side=right]:slide-in-from-left-2 data-[side=top]:slide-in-from-bottom-2 origin-[--radix-dropdown-menu-content-transform-origin] bg-sidebar border-sidebar-border text-sidebar-foreground">
                        <p class="px-2 py-1.5 text-sm font-semibold">My Account</p>
                        <div role="separator" aria-orientation="horizontal" class="-mx-1 my-1 h-px bg-sidebar-border">
                        </div>
                        <DropdownLink :href="route('profile.edit')"
                            class="relative flex cursor-default select-none items-center gap-2 rounded-sm px-2 py-1.5 text-sm outline-none transition-colors hover:bg-accent hover:text-accent-foreground data-[disabled]:pointer-events-none data-[disabled]:opacity-50 [&>svg]:size-4 [&>svg]:shrink-0 hover:bg-sidebar-accent text-gray-700 dark:text-gray-300">
                            <UserCircleIcon class="mr-2 h-4 w-4" />
                            Profile
                        </DropdownLink>
                        <div role="separator" aria-orientation="horizontal" class="-mx-1 my-1 h-px bg-sidebar-border">
                        </div>
                        <DropdownLink :href="route('logout')" method="post" as="button"
                            class="relative flex cursor-default select-none items-center gap-2 rounded-sm px-2 py-1.5 text-sm outline-none transition-colors focus:bg-accent focus:text-accent-foreground data-[disabled]:pointer-events-none data-[disabled]:opacity-50 [&>svg]:size-4 [&>svg]:shrink-0 text-destructive hover:bg-destructive/10">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-log-out h-4 w-4 mr-2" aria-hidden="true"
                                data-replit-metadata="client/src/components/layout/DashboardLayout.tsx:204:18"
                                data-component-name="LogOut">
                                <path d="m16 17 5-5-5-5"></path>
                                <path d="M21 12H9"></path>
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                            </svg>
                            Log out
                        </DropdownLink>
                    </template>
                </Dropdown>
            </div>
        </div>
    </div>
</template>
