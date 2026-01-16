<script setup>
import { Link } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import {
    HomeIcon,
    ChatBubbleLeftRightIcon,
    SparklesIcon,
    TagIcon,
    XMarkIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
    isCollapsed: {
        type: Boolean,
        default: false,
    },
    isMobileOpen: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['close-mobile']);

const navigation = [
    {
        name: 'Dashboard',
        href: route('dashboard'),
        icon: HomeIcon,
        current: route().current('dashboard'),
    },
    {
        name: 'Chat',
        href: route('chat.index'),
        icon: ChatBubbleLeftRightIcon,
        current: route().current('chat.index'),
    },
    {
        name: 'Quotes',
        href: route('quotes.index'),
        icon: SparklesIcon,
        current: route().current('quotes.index'),
    },
    {
        name: 'Categories',
        href: route('categories.index'),
        icon: TagIcon,
        current: route().current('categories.index'),
    },
];
</script>

<template>
    <!-- Desktop sidebar -->
    <div class="hidden lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:flex-col">
        <div :class="[
            'flex grow flex-col overflow-y-auto bg-background shadow-sm border-r border-r-border transition-all duration-300',
            isCollapsed ? 'w-20' : 'w-64'
        ]">
            <!-- Logo -->
            <div class="flex h-16 shrink-0 items-center px-6 border-b border-b-border">
                <Link :href="route('dashboard')" class="flex items-center">
                    <ApplicationLogo
                        :class="[
                            'h-8 w-auto text-white transition-all duration-300',
                            isCollapsed ? 'mx-auto' : ''
                        ]"
                    />
                    <span
                        v-if="!isCollapsed"
                        class="ml-3 text-lg font-semibold text-black dark:text-white"
                    >
                        {{ $page.props.app?.name || 'ChetakAI' }}
                    </span>
                </Link>
            </div>

            <!-- Navigation -->
            <nav class="flex flex-1 flex-col py-6 px-3">
                <div class="px-3 mb-2 text-[10px] font-bold uppercase tracking-widest text-gray-400">Main</div>
                <ul role="list" class="flex flex-1 flex-col gap-y-7 px-3">
                    <li>
                        <ul role="list" class="-mx-2 space-y-1">
                            <li v-for="item in navigation" :key="item.name">
                                <Link
                                    :href="item.href"
                                    :class="[
                                        'flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition-colors cursor-pointer',
                                        item.current
                                            ? 'bg-sidebar-accent text-sidebar-accent-foreground'
                                            : 'text-sidebar-foreground/70 hover:bg-sidebar-accent/50 hover:text-sidebar-foreground',
                                        isCollapsed ? 'justify-center px-2' : ''
                                    ]"
                                >
                                    <component
                                        :is="item.icon"
                                        :class="[
                                            'h-4 w-4 shrink-0',
                                            'text-gray-400 group-hover:text-white'
                                        ]"
                                    />
                                    <span v-if="!isCollapsed">{{ item.name }}</span>
                                </Link>
                            </li>
                        </ul>
                    </li>
                </ul>
                <div class="px-3 mb-2 text-[10px] font-bold uppercase tracking-widest text-gray-400">Account</div>
                <ul role="list" class="-mx-2 space-y-1">
                    <li>
                        <Link
                            :href="route('profile.edit')"
                            :class="[
                                'flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition-colors cursor-pointer',
                                route().current('profile.edit')
                                    ? 'bg-sidebar-accent text-sidebar-accent-foreground'
                                    : 'text-sidebar-foreground/70 hover:bg-sidebar-accent/50 hover:text-sidebar-foreground',
                                isCollapsed ? 'justify-center px-2' : ''
                            ]"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4 shrink-0 text-gray-400 group-hover:text-white">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                            </svg>
                            <span v-if="!isCollapsed">Profile</span>
                        </Link>
                    </li>
                    <li>
                        <Link
                            :href="route('logout')"
                            method="post"
                            as="button"
                            :class="[
                                'inline-flex items-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 hover-elevate active-elevate-2 border border-transparent min-h-9 py-2 w-full justify-start text-sidebar-foreground/60 hover:text-sidebar-foreground hover:bg-sidebar-accent gap-3 px-3',
                                isCollapsed ? 'justify-center px-2' : ''
                            ]"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4 shrink-0 text-gray-400 group-hover:text-white">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                            </svg>
                            <span v-if="!isCollapsed">Sign Out</span>
                        </Link>
                    </li>
                </ul>
            </nav>
        </div>
    </div>

    <!-- Mobile sidebar -->
    <div
        :class="[
            'fixed inset-y-0 z-50 flex w-full flex-col bg-background shadow-lg transition-transform duration-300 lg:hidden',
            isMobileOpen ? 'translate-x-0' : '-translate-x-full'
        ]"
    >
        <div class="flex grow flex-col gap-y-5 overflow-y-auto px-6 pb-4">
            <!-- Mobile header -->
            <div class="flex h-16 shrink-0 items-center justify-between">
                <Link :href="route('dashboard')" class="flex items-center" @click="emit('close-mobile')">
                    <ApplicationLogo class="h-8 w-auto text-white" />
                    <span class="ml-3 text-lg font-semibold text-white">
                        {{ $page.props.app?.name || 'ChetakAI' }}
                    </span>
                </Link>
                <button
                    @click="emit('close-mobile')"
                    class="p-2 rounded-md text-gray-400 hover:text-white hover:bg-[#2D3A4F]"
                >
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
                                <Link
                                    :href="item.href"
                                    @click="emit('close-mobile')"
                                    :class="[
                                        'group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold transition-colors duration-200',
                                        item.current
                                            ? 'bg-sidebar-accent text-sidebar-accent-foreground'
                                            : 'text-sidebar-foreground/70 hover:bg-sidebar-accent/50 hover:text-sidebar-foreground',
                                    ]"
                                >
                                    <component
                                        :is="item.icon"
                                        :class="[
                                            'h-6 w-6 shrink-0',
                                            item.current
                                                ? 'text-white'
                                                : 'text-gray-400 group-hover:text-white'
                                        ]"
                                    />
                                    <span>{{ item.name }}</span>
                                </Link>
                            </li>
                        </ul>
                    </li>
                </ul>
                <div class="px-3 mb-2 text-[10px] font-bold uppercase tracking-widest text-gray-400">Account</div>
                <ul role="list" class="-mx-2 space-y-1">
                    <li>
                        <Link
                            :href="route('profile.edit')"
                            @click="emit('close-mobile')"
                            :class="[
                                'group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold transition-colors duration-200',
                                route().current('profile.edit')
                                    ? 'bg-[#2D3A4F] text-white'
                                    : 'text-gray-400 hover:text-white hover:bg-[#2D3A4F]'
                            ]"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6 shrink-0 text-gray-400 group-hover:text-white">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                            </svg>
                            <span>Profile</span>
                        </Link>
                    </li>
                    <li>
                        <Link
                            :href="route('logout')"
                            method="post"
                            as="button"
                            @click="emit('close-mobile')"
                            :class="[
                                'group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold transition-colors duration-200',
                                'text-gray-400 hover:text-white hover:bg-[#2D3A4F]'
                            ]"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6 shrink-0 text-gray-400 group-hover:text-white">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                            </svg>
                            <span>Sign Out</span>
                        </Link>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</template>
