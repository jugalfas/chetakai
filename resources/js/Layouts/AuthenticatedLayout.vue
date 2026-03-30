<script setup>
import { ref, computed } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Sidebar from '@/Components/Sidebar.vue';
import Topbar from '@/Components/Topbar.vue';
import { Toaster } from 'vue-sonner';
import { UserIcon } from '@heroicons/vue/24/outline';
import { Link } from '@inertiajs/vue3';

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
    <div>
        <!-- Impersonation Banner -->
        <div 
            v-if="$page.props.auth.impersonating?.user_name" 
            class="fixed top-0 left-0 right-0 z-[60] bg-indigo-600 text-white px-4 py-2 flex items-center justify-center gap-4 text-xs sm:text-sm font-medium shadow-lg animate-in slide-in-from-top duration-300"
        >
            <div class="flex items-center gap-2">
                <UserIcon class="w-4 h-4 opacity-75 shrink-0" />
                <span>You are viewing as <strong>{{ $page.props.auth.impersonating.user_name }}</strong></span>
            </div>
            <Link 
                :href="route('impersonate.logout')" 
                method="post" 
                as="button"
                class="px-3 py-1 bg-white/20 hover:bg-white/30 rounded-lg transition-colors border border-white/10 whitespace-nowrap font-bold uppercase tracking-widest text-[10px]"
            >
                Exit
            </Link>
        </div>

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
                @close-mobile="isMobileMenuOpen = false"
            />

            <!-- Main content -->
            <div :class="['flex-1 flex flex-col min-h-screen', isSidebarCollapsed ? 'lg:pl-20' : 'lg:pl-64']">
                <!-- Top bar with dynamic top spacing -->
                <Topbar
                    :user-name="$page.props.auth.user.first_name + ' ' + $page.props.auth.user.last_name"
                    :isSidebarCollapsed="isSidebarCollapsed"
                    :style="{ top: $page.props.auth.impersonating?.user_name ? '46px' : '0' }"
                    @toggle-sidebar="toggleSidebar"
                    @toggle-mobile-menu="toggleMobileMenu"
                />

                <!-- Spacer for fixed header + banner -->
                <div :class="[$page.props.auth.impersonating?.user_name ? 'h-[104px]' : 'h-16']"></div>

                <!-- Main content area -->
                <main class="flex-1 p-4 sm:p-6 lg:p-8 bg-background">
                    <div class="mx-auto max-w-7xl animate-in fade-in slide-in-from-bottom-2 duration-500">
                        <slot />
                    </div>
                </main>
            </div>
        </div>
    </div>
</template>
