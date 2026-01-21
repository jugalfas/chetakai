<script setup>
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import { Bars3Icon, UserCircleIcon } from '@heroicons/vue/24/outline';
import { 
    Bell, 
    CheckCircle2, 
    AlertCircle, 
    Quote, 
    Clock, 
    Settings2,
    ChevronDown,
    LogOut,
    CheckCheck
} from 'lucide-vue-next';
import DarkModeToggle from '@/Components/DarkModeToggle.vue';
import ScrollArea from './ScrollArea.vue';
import { computed } from 'vue';

const props = defineProps({
    userName: String,
    isSidebarCollapsed: {
        type: Boolean,
        default: false,
    },
    isAdmin: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['toggleSidebar', 'toggleMobileMenu']);

const page = usePage();
const notifications = computed(() => page.props.auth.notifications || []);
const unreadCount = computed(() => page.props.auth.unreadNotificationsCount || 0);

const formatTime = (dateString) => {
    const date = new Date(dateString);
    const now = new Date();
    const diffInSeconds = Math.floor((now - date) / 1000);
    
    if (diffInSeconds < 60) return 'Just now';
    
    const diffInMinutes = Math.floor(diffInSeconds / 60);
    if (diffInMinutes < 60) return `${diffInMinutes}m ago`;
    
    const diffInHours = Math.floor(diffInMinutes / 60);
    if (diffInHours < 24) return `${diffInHours}h ago`;
    
    const diffInDays = Math.floor(diffInHours / 24);
    if (diffInDays < 7) return `${diffInDays}d ago`;
    
    return date.toLocaleDateString();
};

const getNotificationIcon = (notification) => {
    const type = notification.type;
    const title = notification.data.title || '';
    
    if (type.includes('PostPublished') || title.includes('Post Published')) {
        return { icon: CheckCircle2, color: 'text-emerald-500', bg: 'bg-emerald-500/10' };
    }
    if (type.includes('SubscriptionUpdate') || title.includes('Subscription Update')) {
        return { icon: AlertCircle, color: 'text-accent', bg: 'bg-accent/10' };
    }
    if (type.includes('CategorySuggestion') || title.includes('Category Suggestion')) {
        return { icon: Quote, color: 'text-primary', bg: 'bg-primary/10' };
    }
    
    return { icon: Bell, color: 'text-blue-500', bg: 'bg-blue-500/10' };
};

const markAllAsRead = () => {
    router.post(route('notifications.mark-as-read'), {}, {
        preserveScroll: true,
        onSuccess: () => {
            // Notifications will be automatically removed via the shared props
        }
    });
};

const user = computed(() => {
    return props.isAdmin ? page.props.auth.admin : page.props.auth.user;
});

const logoutRoute = computed(() => {
    return props.isAdmin ? route('admin.logout') : route('logout');
});

const profileRoute = computed(() => {
    return props.isAdmin ? '#' : route('profile.edit');
});
</script>

<template>
    <div class="fixed top-0 right-0 z-40 flex h-16 shrink-0 items-center gap-x-4 border-b border-b-border bg-background/80 backdrop-blur-md px-4 shadow-sm transition-[left] duration-300 left-0 sm:gap-x-6 sm:px-6 lg:px-8"
        :class="[isSidebarCollapsed ? 'lg:left-20' : 'lg:left-64']">
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

                <!-- Notifications (only for regular users for now) -->
                <Dropdown v-if="!isAdmin" align="right" width="80"
                    content-classes="z-50 border text-popover-foreground outline-none data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0 data-[state=closed]:zoom-out-95 data-[state=open]:zoom-in-95 data-[side=bottom]:slide-in-from-top-2 data-[side=left]:slide-in-from-right-2 data-[side=right]:slide-in-from-left-2 data-[side=top]:slide-in-from-bottom-2 origin-[--radix-popover-content-transform-origin] w-80 p-0 bg-sidebar border-sidebar-border shadow-2xl overflow-hidden rounded-xl">
                    <template #trigger>
                        <button class="relative p-2 text-gray-400 hover:text-white transition-colors group">
                            <Bell class="w-6 h-6 group-hover:text-white transition-colors" />
                            <!-- Notification badge -->
                            <span v-if="unreadCount > 0"
                                class="absolute top-1.5 right-1.5 h-4 w-4 rounded-full bg-red-500 text-[10px] font-bold text-white flex items-center justify-center animate-pulse shadow-sm">
                                {{ unreadCount > 9 ? '9+' : unreadCount }}
                            </span>
                        </button>
                    </template>

                    <template #content>
                        <div class="p-4 border-b border-sidebar-border bg-sidebar-accent/30">
                            <div class="flex items-center justify-between">
                                <h3 class="font-bold text-sm text-foreground">Notifications</h3>
                                <span v-if="unreadCount > 0"
                                    class="text-[10px] font-bold uppercase tracking-wider text-accent bg-accent/10 px-2 py-0.5 rounded-full">
                                    {{ unreadCount }} New
                                </span>
                            </div>
                        </div>

                        <div class="relative h-[300px]">
                            <ScrollArea
                                class="h-full w-full rounded-[inherit] overflow-y-auto custom-scrollbar"
                            >
                                <div style="min-width: 100%; display: table;">
                                    <div v-if="notifications.length > 0" class="flex flex-col">
                                        <div v-for="notification in notifications" :key="notification.id" 
                                            class="p-4 hover:bg-sidebar-accent/50 transition-colors cursor-pointer border-b border-sidebar-border/50"
                                            :class="{ 'bg-sidebar-accent/20': !notification.read_at }">
                                            <div class="flex gap-3">
                                                <div :class="['h-8 w-8 rounded-full flex items-center justify-center shrink-0', getNotificationIcon(notification).bg]">
                                                    <component :is="getNotificationIcon(notification).icon" 
                                                        :class="['h-4 w-4', getNotificationIcon(notification).color]" />
                                                </div>
                                                <div class="space-y-1">
                                                    <p class="text-sm font-medium text-foreground leading-none">
                                                        {{ notification.data.title }}
                                                    </p>
                                                    <p class="text-xs text-muted-foreground line-clamp-2">
                                                        {{ notification.data.message }}
                                                    </p>
                                                    <div class="flex items-center gap-1 text-[10px] text-muted-foreground pt-1">
                                                        <Clock class="h-3 w-3" />
                                                        <span>{{ formatTime(notification.created_at) }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Empty State -->
                                    <div v-else class="h-full flex flex-col items-center justify-center p-8 text-center space-y-4">
                                        <div class="h-16 w-16 rounded-full bg-sidebar-accent/50 flex items-center justify-center">
                                            <Bell class="h-8 w-8 text-muted-foreground/40" />
                                        </div>
                                        <div class="space-y-1">
                                            <p class="text-sm font-bold text-foreground">No new notifications</p>
                                            <p class="text-xs text-muted-foreground leading-relaxed">
                                                When you have new activity or updates, they'll appear here.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </ScrollArea>
                        </div>

                        <div v-if="notifications.length > 0" class="p-3 border-t border-sidebar-border text-center bg-sidebar-accent/10">
                            <button
                                @click="markAllAsRead"
                                class="inline-flex items-center justify-center gap-2 whitespace-nowrap transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 hover-elevate active-elevate-2 border border-transparent min-h-8 rounded-md px-3 text-xs font-bold text-accent hover:text-accent hover:bg-accent/5 w-full uppercase tracking-widest">
                                <CheckCheck class="h-3 w-3" />
                                Mark as read
                            </button>
                        </div>
                    </template>
                </Dropdown>


                <!-- Profile dropdown -->
                <Dropdown align="right" width="48"
                    :content-classes="'py-1 rounded-md p-1 bg-sidebar border border-sidebar-border'">
                    <template #trigger>
                        <button
                            class="inline-flex items-center justify-center whitespace-nowrap text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 hover-elevate active-elevate-2 border border-transparent min-h-9 px-4 py-2 pl-2 pr-1 gap-2 h-9 rounded-full hover:bg-sidebar-accent"
                            type="button" id="radix-_r_3_" aria-haspopup="menu" aria-expanded="false"
                            data-state="closed">
                            <span
                                class="relative flex shrink-0 overflow-hidden rounded-full h-7 w-7 border border-sidebar-border">
                                <img class="aspect-square h-full w-full"
                                    :src="user?.avatar || 'https://github.com/shadcn.png'">
                            </span>
                            <span class="text-sm font-medium text-sidebar-foreground hidden sm:inline-block">
                                {{ userName }}
                            </span>
                            <ChevronDown class="h-4 w-4 text-sidebar-foreground/50" />
                        </button>
                    </template>

                    <template #content
                        class="z-50 max-h-[var(--radix-dropdown-menu-content-available-height)] overflow-y-auto overflow-x-hidden rounded-md border p-1 shadow-md data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0 data-[state=closed]:zoom-out-95 data-[state=open]:zoom-in-95 data-[side=bottom]:slide-in-from-top-2 data-[side=left]:slide-in-from-right-2 data-[side=right]:slide-in-from-left-2 data-[side=top]:slide-in-from-bottom-2 origin-[--radix-dropdown-menu-content-transform-origin] bg-sidebar border-sidebar-border text-sidebar-foreground">
                        <p class="px-2 py-1.5 text-sm font-semibold">My Account</p>
                        <div role="separator" aria-orientation="horizontal" class="-mx-1 my-1 h-px bg-sidebar-border">
                        </div>
                        <DropdownLink v-if="!isAdmin" :href="profileRoute"
                            class="relative flex cursor-default select-none items-center gap-2 rounded-sm px-2 py-1.5 text-sm outline-none transition-colors hover:bg-accent hover:text-accent-foreground data-[disabled]:pointer-events-none data-[disabled]:opacity-50 [&>svg]:size-4 [&>svg]:shrink-0 hover:bg-sidebar-accent text-gray-700 dark:text-gray-300">
                            <UserCircleIcon class="mr-2 h-4 w-4" />
                            Profile
                        </DropdownLink>
                        <div v-if="!isAdmin" role="separator" aria-orientation="horizontal" class="-mx-1 my-1 h-px bg-sidebar-border">
                        </div>
                        <DropdownLink :href="logoutRoute" method="post" as="button"
                            class="relative flex cursor-default select-none items-center gap-2 rounded-sm px-2 py-1.5 text-sm outline-none transition-colors focus:bg-accent focus:text-accent-foreground data-[disabled]:pointer-events-none data-[disabled]:opacity-50 [&>svg]:size-4 [&>svg]:shrink-0 text-destructive hover:bg-destructive/10">
                            <LogOut class="h-4 w-4 mr-2" />
                            Log out
                        </DropdownLink>
                    </template>
                </Dropdown>
            </div>
        </div>
    </div>
</template>
