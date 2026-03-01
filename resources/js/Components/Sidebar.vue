<script setup>
import { computed } from 'vue';
import { usePage, Link } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import {
    XMarkIcon
} from '@heroicons/vue/24/outline';

const page = usePage();

const props = defineProps({
    isCollapsed: {
        type: Boolean,
        default: false,
    },
    isMobileOpen: {
        type: Boolean,
        default: false,
    },
    isAdmin: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['close-mobile']);

const logoutRoute = computed(() => {
    return props.isAdmin ? route('admin.logout') : route('logout');
});

const navigation = computed(() => {
    if (props.isAdmin) {
        return [
            {   
                name: 'Dashboard',
                href: route('admin.dashboard'),
                icon: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" class="h-4 w-4"><rect width="7" height="9" x="3" y="3" rx="1"></rect><rect width="7" height="5" x="14" y="3" rx="1"></rect><rect width="7" height="9" x="14" y="12" rx="1"></rect><rect width="7" height="5" x="3" y="16" rx="1"></rect></svg>',
                current: route().current('admin.dashboard'),
            },
            {
                name: 'Users',
                href: route('admin.users.index'),
                icon: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" class="h-4 w-4"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>',
                current: route().current('admin.users.*'),
            },
            {
                name: 'Messages',
                href: route('admin.contacts.index'),
                icon: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" class="h-4 w-4"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>',
                current: route().current('admin.contacts.*'),
                count: page.props.auth.unreadMessagesCount,
            },
        ];
    }

    return [
        {
            name: 'Dashboard',
            href: route('dashboard'),
            icon: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" class="h-4 w-4"><rect width="7" height="9" x="3" y="3" rx="1"></rect><rect width="7" height="5" x="14" y="3" rx="1"></rect><rect width="7" height="9" x="14" y="12" rx="1"></rect><rect width="7" height="5" x="3" y="16" rx="1"></rect></svg>',
            current: route().current('dashboard'),
        },
        {
            name: 'Chat',
            href: route('chat.index'),
            icon: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" class="h-4 w-4"><path d="M22 17a2 2 0 0 1-2 2H6.828a2 2 0 0 0-1.414.586l-2.202 2.202A.71.71 0 0 1 2 21.286V5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2z"></path></svg>',
            current: route().current('chat.index'),
        },
        {
            name: 'Posts',
            href: route('posts.index'),
            icon: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" class="h-4 w-4"><path d="M16 3a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2 1 1 0 0 1 1 1v1a2 2 0 0 1-2 2 1 1 0 0 0-1 1v2a1 1 0 0 0 1 1 6 6 0 0 0 6-6V5a2 2 0 0 0-2-2z"></path><path d="M5 3a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2 1 1 0 0 1 1 1v1a2 2 0 0 1-2 2 1 1 0 0 0-1 1v2a1 1 0 0 0 1 1 6 6 0 0 0 6-6V5a2 2 0 0 0-2-2z"></path></svg>',
            current: route().current('posts.index'),
        },
        {
            name: 'Prompts',
            href: route('prompts.index'),
            icon: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" class="h-4 w-4"><circle cx="12" cy="12" r="10"></circle><path d="M8 12h8"></path><path d="M12 8v8"></path></svg>',
            current: route().current('prompts.*'),
        }
    ];
});
</script>

<template>
    <!-- Desktop sidebar -->
    <div class="hidden lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:flex-col">
        <div :class="[
            'flex grow flex-col overflow-y-auto bg-background shadow-sm border-r border-r-border transition-all duration-300',
            isCollapsed ? 'w-20' : 'w-64'
        ]">
            <!-- Logo -->
            <div class="h-16 flex shrink-0 items-center px-4 border-b border-b-border">
                <Link :href="isAdmin ? route('admin.dashboard') : route('dashboard')" class="flex items-center">
                    <ApplicationLogo :class="[
                        'h-8 w-auto text-white transition-all duration-300',
                        isCollapsed ? 'mx-auto' : ''
                    ]"  :size="8" />
                    <span v-if="!isCollapsed" class="ml-3 text-lg font-semibold text-black dark:text-white">
                        {{ $page.props.app?.name || 'ChetakAI' }}
                    </span>
                </Link>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 py-6 px-3 space-y-6 overflow-y-auto bg-sidebar">
                <div role="list" class="space-y-1">
                    <div class="px-3 mb-2 text-[10px] font-bold uppercase tracking-widest text-sidebar-foreground/40">Main</div>
                    <a v-for="item in navigation" :key="item.name">
                        <Link :href="item.href" :class="[
                            'flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition-colors cursor-pointer relative',
                            item.current
                                ? 'bg-sidebar-accent text-sidebar-accent-foreground'
                                : 'text-sidebar-foreground/70 hover:bg-sidebar-accent/50 hover:text-sidebar-foreground',
                            isCollapsed ? 'justify-center px-2' : ''
                        ]">
                            <div class="relative">
                                <span v-html="item.icon"></span>
                                <span v-if="isCollapsed && item.count > 0" class="absolute -top-1 -right-1 flex h-2 w-2">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-primary"></span>
                                </span>
                            </div>
                            <span v-if="!isCollapsed">{{ item.name }}</span>
                            <span v-if="!isCollapsed && item.count > 0" class="ml-auto inline-flex h-5 w-5 items-center justify-center rounded-full bg-primary text-[10px] font-bold text-primary-foreground">
                                {{ item.count }}
                            </span>
                        </Link>
                    </a>
                </div>
                <div v-if="!isAdmin" role="list" class="space-y-1">
                    <div class="px-3 mb-2 text-[10px] font-bold uppercase tracking-widest text-sidebar-foreground/40">Account</div>
                    <a>
                        <Link :href="route('profile.edit')" :class="[
                            'flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition-colors cursor-pointer',
                            route().current('profile.edit')
                                ? 'bg-sidebar-accent text-sidebar-accent-foreground'
                                : 'text-sidebar-foreground/70 hover:bg-sidebar-accent/50 hover:text-sidebar-foreground',
                            isCollapsed ? 'justify-center px-2' : ''
                        ]">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user h-4 w-4" aria-hidden="true" data-replit-metadata="client/src/components/layout/DashboardLayout.tsx:50:8" data-component-name="Icon"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                            <span v-if="!isCollapsed">Profile</span>
                        </Link>
                    </a>
                </div>
            </nav>
            <div class="p-4 border-t border-sidebar-border mt-auto">
                <a>
                    <Link :href="logoutRoute" method="post" as="button" :class="[
                        'inline-flex items-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 hover-elevate active-elevate-2 border border-transparent min-h-9 py-2 w-full justify-start text-sidebar-foreground/60 hover:text-sidebar-foreground hover:bg-sidebar-accent gap-3 px-3',
                        isCollapsed ? 'justify-center px-2' : ''
                    ]">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-log-out h-4 w-4" aria-hidden="true" data-replit-metadata="client/src/components/layout/DashboardLayout.tsx:132:10" data-component-name="LogOut">
                            <path d="m16 17 5-5-5-5"></path>
                            <path d="M21 12H9"></path>
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                        </svg>
                        <span v-if="!isCollapsed">Sign Out</span>
                    </Link>
                </a>
            </div>
        </div>
    </div>

    <!-- Mobile sidebar -->
    <div :class="[
        'fixed inset-y-0 z-50 flex w-full flex-col bg-background shadow-lg transition-transform duration-300 lg:hidden',
        isMobileOpen ? 'translate-x-0' : '-translate-x-full'
    ]">
        <div class="flex grow flex-col gap-y-5 overflow-y-auto px-6 pb-4">
            <!-- Mobile header -->
            <div class="flex h-16 shrink-0 items-center justify-between">
                <Link :href="isAdmin ? route('admin.dashboard') : route('dashboard')" class="flex items-center" @click="emit('close-mobile')">
                    <ApplicationLogo class="h-8 w-auto text-white" :size="8" />
                    <span class="ml-3 text-lg font-semibold text-white">
                        {{ $page.props.app?.name || 'ChetakAI' }}
                    </span>
                </Link>
                <button @click="emit('close-mobile')"
                    class="p-2 rounded-md text-gray-400 hover:text-white hover:bg-[#2D3A4F]">
                    <XMarkIcon class="h-6 w-6" />
                </button>
            </div>

            <!-- Mobile navigation -->
            <nav class="flex flex-1 flex-col">
                <div class="px-3 mb-2 text-[10px] font-bold uppercase tracking-widest text-gray-400">Main</div>
                <ul role="list" class="flex flex-1 flex-col gap-y-7">
                    <li>
                        <ul role="list" class="-mx-2 space-y-1">
                            <li v-for="item in navigation" :key="item.name">
                                <Link :href="item.href" @click="emit('close-mobile')" :class="[
                                    'group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold transition-colors duration-200',
                                    item.current
                                        ? 'bg-sidebar-accent text-sidebar-accent-foreground'
                                        : 'text-sidebar-foreground/70 hover:bg-sidebar-accent/50 hover:text-sidebar-foreground',
                                ]">
                                    <span :class="[
                                        'h-6 w-6 shrink-0',
                                        item.current
                                            ? 'text-white'
                                            : 'text-gray-400 group-hover:text-white'
                                    ]" v-html="item.icon"></span>
                                    <span>{{ item.name }}</span>
                                    <span v-if="item.count > 0" class="ml-auto inline-flex h-5 w-5 items-center justify-center rounded-full bg-primary text-[10px] font-bold text-primary-foreground">
                                        {{ item.count }}
                                    </span>
                                </Link>
                            </li>
                        </ul>
                    </li>
                </ul>
                <div class="px-3 mb-2 text-[10px] font-bold uppercase tracking-widest text-gray-400">Account</div>
                <ul role="list" class="-mx-2 space-y-1">
                    <li>
                        <Link :href="route('profile.edit')" @click="emit('close-mobile')" :class="[
                            'group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold transition-colors duration-200',
                            route().current('profile.edit')
                                ? 'bg-[#2D3A4F] text-white'
                                : 'text-gray-400 hover:text-white hover:bg-[#2D3A4F]'
                        ]">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user h-4 w-4" aria-hidden="true" data-replit-metadata="client/src/components/layout/DashboardLayout.tsx:50:8" data-component-name="Icon"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                            <span>Profile</span>
                        </Link>
                    </li>
                    <li>
                        <Link :href="logoutRoute" method="post" as="button" @click="emit('close-mobile')" :class="[
                            'group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold transition-colors duration-200',
                            'text-gray-400 hover:text-white hover:bg-[#2D3A4F]'
                        ]">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="h-6 w-6 shrink-0 text-gray-400 group-hover:text-white">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                            </svg>
                            <span>Sign Out</span>
                        </Link>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</template>
