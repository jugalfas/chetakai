<script setup>
import { ref, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import Sidebar from '@/Components/Sidebar.vue';
import Topbar from '@/Components/Topbar.vue';
import { Toaster } from 'vue-sonner';

const isSidebarCollapsed = ref(false);
const isMobileMenuOpen = ref(false);

const toggleSidebar = () => {
    isSidebarCollapsed.value = !isSidebarCollapsed.value;
};

const toggleMobileMenu = () => {
    isMobileMenuOpen.value = !isMobileMenuOpen.value;
};

const userName = computed(() => {
    const admin = usePage().props.auth.admin;
    if (!admin) return '';
    return admin.name || ((admin.first_name || '') + ' ' + (admin.last_name || ''));
});
</script>

<template>
    <div>
        <Toaster
            position="top-right"
            rich-colors
            close-button
        />
        <div class="min-h-screen flex flex-col lg:flex-row bg-background">
            <!-- Mobile backdrop -->
            <div
                v-if="isMobileMenuOpen"
                class="fixed inset-0 z-40 bg-background bg-opacity-75 lg:hidden"
                @click="isMobileMenuOpen = false"
            ></div>

            <!-- Sidebar -->
            <Sidebar
                :isCollapsed="isSidebarCollapsed"
                :isMobileOpen="isMobileMenuOpen"
                is-admin
                @close-mobile="isMobileMenuOpen = false"
            />

            <!-- Main content -->
            <div :class="['flex-1', isSidebarCollapsed ? 'lg:pl-20' : 'lg:pl-64']">
                <!-- Top bar -->
                <Topbar
                    :user-name="userName"
                    :isSidebarCollapsed="isSidebarCollapsed"
                    is-admin
                    @toggle-sidebar="toggleSidebar"
                    @toggle-mobile-menu="toggleMobileMenu"
                />

                <!-- Spacer for fixed header -->
                <div class="h-16"></div>

                <!-- Main content area -->
                <main class="flex-1 p-4 sm:p-6 lg:p-8 bg-background">
                    <div class="mx-auto max-max-w-7xl animate-in fade-in slide-in-from-bottom-2 duration-500">
                        <slot />
                    </div>
                </main>
            </div>
        </div>
    </div>
</template>
