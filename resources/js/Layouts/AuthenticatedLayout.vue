<script setup>
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/vue3';

import Sidebar from '@/Components/Sidebar.vue';
import Topbar from '@/Components/Topbar.vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';
// import DarkModeToggle from '@/Components/DarkModeToggle.vue';

const showingNavigationDropdown = ref(false);
const isSidebarCollapsed = ref(false);

const toggleSidebar = () => {
    isSidebarCollapsed.value = !isSidebarCollapsed.value;
};

const crumbs = [
    { label: 'Dashboard', href: route('dashboard') },
    { label: 'Quotes', href: route('quotes.index') },
];
</script>

<template>
    <div class="h-screen overflow-hidden bg-gray-100 dark:bg-gray-900 flex">
        <Sidebar :isCollapsed="isSidebarCollapsed" />

        <div class="flex-1 flex flex-col h-screen overflow-hidden">
            <Topbar :user-name="$page.props.auth.user.name" @toggle-sidebar="toggleSidebar" />

            <Breadcrumbs :crumbs="crumbs" />



            <!-- Page Content -->
            <main class="flex-1 px-4 py-6 text-base leading-relaxed overflow-y-auto">
                <div class="max-w-7xl mx-auto">
                    <slot />
                </div>
            </main>

            <!-- Footer -->
            <footer class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 p-4 text-center text-sm text-gray-500 dark:text-gray-400">
                &copy; {{ new Date().getFullYear() }} {{ $page.props.app.name }}. All rights reserved.
            </footer>
        </div>
    </div>
</template>
