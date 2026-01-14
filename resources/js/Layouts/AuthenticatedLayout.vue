<script setup>
import { ref, computed } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Sidebar from '@/Components/Sidebar.vue';
import Topbar from '@/Components/Topbar.vue';

const isSidebarCollapsed = ref(false);
const isMobileMenuOpen = ref(false);

const toggleSidebar = () => {
    isSidebarCollapsed.value = !isSidebarCollapsed.value;
};

const toggleMobileMenu = () => {
    isMobileMenuOpen.value = !isMobileMenuOpen.value;
};
</script>

<template>
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
            @close-mobile="isMobileMenuOpen = false"
        />

        <!-- Main content -->
        <div :class="['flex-1', isSidebarCollapsed ? 'lg:pl-20' : 'lg:pl-64']">
            <!-- Top bar -->
            <Topbar
                :user-name="$page.props.auth.user.name"
                :isSidebarCollapsed="isSidebarCollapsed"
                @toggle-sidebar="toggleSidebar"
                @toggle-mobile-menu="toggleMobileMenu"
            />

            <!-- Main content area -->
            <main class="flex-1">
                <div class="py-6">
                    <div class="mx-auto max-w-7xl animate-in fade-in slide-in-from-bottom-2 duration-500">
                        <slot />
                    </div>
                </div>
            </main>
        </div>
    </div>
</template>
