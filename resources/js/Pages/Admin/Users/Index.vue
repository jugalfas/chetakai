<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Head, router, Link } from "@inertiajs/vue3";
import { ref, watch, onMounted, onUnmounted } from "vue";
import {
    TrashIcon,
    MagnifyingGlassIcon,
    UserCircleIcon,
    EllipsisVerticalIcon,
    UserIcon,
    CreditCardIcon,
    EyeIcon,
    NoSymbolIcon,
    CheckCircleIcon,
} from "@heroicons/vue/24/outline";
import TextInput from "@/Components/TextInput.vue";
import DeleteConfirmationModal from "@/Components/DeleteConfirmationModal.vue";
import StatusConfirmationModal from "@/Components/StatusConfirmationModal.vue";

const props = defineProps({
    users: Object,
    filters: Object,
    stats: Array,
    defaultPlan: Object,
});

const search = ref(props.filters?.search || "");
const statusFilter = ref(props.filters?.status || "all");
const planFilter = ref(props.filters?.plan || "all");
const showDeleteModal = ref(false);
const userToDelete = ref(null);
const processing = ref(false);
const openDropdown = ref(null);

const applyFilters = () => {
    router.get(
        route("admin.users.index"),
        { 
            search: search.value,
            status: statusFilter.value,
            plan: planFilter.value,
        },
        { preserveState: true, replace: true }
    );
};

let timeout = null;
watch(search, (value) => {
    if (timeout) clearTimeout(timeout);
    timeout = setTimeout(() => {
        applyFilters();
    }, 300);
});

watch([statusFilter, planFilter], () => {
    applyFilters();
});

const getInitials = (user) => {
    return (user.first_name?.[0] || "") + (user.last_name?.[0] || "");
};

const getAvatarStyle = (index) => {
    const colors = [
        { bg: 'bg-indigo-100', text: 'text-indigo-700' },
        { bg: 'bg-emerald-100', text: 'text-emerald-700' },
        { bg: 'bg-amber-100', text: 'text-amber-700' },
        { bg: 'bg-rose-100', text: 'text-rose-700' },
        { bg: 'bg-sky-100', text: 'text-sky-700' },
    ];
    const color = colors[index % colors.length];
    return `${color.bg} ${color.text}`;
};

const getStatusBadgeClass = (status) => {
    switch (status) {
        case 'active': return 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400';
        case 'suspended': return 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400';
        case 'banned': return 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400';
        default: return 'bg-gray-100 text-gray-700 dark:bg-gray-900/30 dark:text-gray-400';
    }
};

const getPlanBadgeClass = (planType) => {
    switch (planType) {
        case 'free': return 'bg-gray-100 text-gray-700 dark:bg-gray-900/40 dark:text-gray-300';
        case 'paid': return 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400';
        case 'custom': return 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400';
        default: return 'bg-gray-100 text-gray-700 dark:bg-gray-900/40 dark:text-gray-300';
    }
};

const getPostBarColor = (used, limit) => {
    if (!limit) return 'bg-emerald-500';
    const pct = (used / limit) * 100;
    if (pct >= 90) return 'bg-rose-500';
    if (pct >= 70) return 'bg-amber-500';
    return 'bg-emerald-500';
};

const showStatusModal = ref(false);
const statusToChange = ref(null);
const userForStatus = ref(null);

const confirmStatusChange = (user, status) => {
    userForStatus.value = user;
    statusToChange.value = status;
    showStatusModal.value = true;
};

const updateStatus = () => {
    if (!userForStatus.value || !statusToChange.value) return;
    
    router.patch(route('admin.users.update-status', userForStatus.value.id), 
        { status: statusToChange.value }, 
        {
            onBefore: () => processing.value = true,
            onFinish: () => {
                processing.value = false;
                showStatusModal.value = false;
                userForStatus.value = null;
                statusToChange.value = null;
                openDropdown.value = null;
            },
            onSuccess: () => {
                // Success message handled by flash
            }
        }
    );
};

const confirmDelete = (user) => {
    userToDelete.value = user;
    showDeleteModal.value = true;
};

const deleteUser = () => {
    if (userToDelete.value) {
        router.delete(route('admin.users.destroy', userToDelete.value.id), {
            onBefore: () => processing.value = true,
            onFinish: () => processing.value = false,
            onSuccess: () => {
                showDeleteModal.value = false;
                userToDelete.value = null;
            }
        });
    }
};

const handleOutsideClick = (e) => {
    if (openDropdown.value && !e.target.closest('.relative')) {
        openDropdown.value = null;
    }
};

onMounted(() => {
    window.addEventListener('click', handleOutsideClick);
});

onUnmounted(() => {
    window.removeEventListener('click', handleOutsideClick);
});

const dropdownPosition = ref({ top: '0px', left: '0px' })

const toggleDropdown = (event, userId) => {
    if (openDropdown.value === userId) {
        openDropdown.value = null
        return
    }

    const rect = event.currentTarget.getBoundingClientRect()

    dropdownPosition.value = {
        top: rect.bottom + window.scrollY + 'px',
        left: rect.right - 192 + 'px'
    }

    openDropdown.value = userId
}
</script>

<template>
    <Head title="User Management" />

    <AdminLayout>
        <div class="p-6">
            <!-- Header -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight text-foreground">Users</h1>
                    <p class="mt-1 text-sm text-muted-foreground">All registered accounts and their subscription status.</p>
                </div>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div
                    v-for="stat in stats"
                    :key="stat.label"
                    class="bg-card border border-border rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow"
                >
                    <p class="text-sm font-medium text-muted-foreground mb-2">{{ stat.label }}</p>
                    <h4 class="text-3xl font-bold text-foreground mb-1">{{ stat.value }}</h4>
                    <p class="text-xs text-muted-foreground">{{ stat.subtext }}</p>
                </div>
            </div>

            <!-- Toolbar -->
            <div class="mb-6 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
                <div class="flex flex-col sm:flex-row items-center gap-4 w-full lg:w-auto">
                    <div class="relative w-full sm:w-96">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <MagnifyingGlassIcon class="h-4 w-4 text-muted-foreground" />
                        </div>
                        <input
                            v-model="search"
                            type="text"
                            class="block w-full rounded-lg border-0 py-2.5 pl-10 text-sm text-foreground ring-1 ring-inset ring-border placeholder:text-muted-foreground focus:ring-2 focus:ring-inset focus:ring-primary bg-background shadow-sm transition-all"
                            placeholder="Search by name, email..."
                        />
                    </div>
                    
                    <div class="flex items-center gap-2 overflow-x-auto pb-1 sm:pb-0 w-full sm:w-auto no-scrollbar">
                        <span class="text-xs font-semibold text-muted-foreground uppercase tracking-wider whitespace-nowrap">Status:</span>
                        <div class="flex items-center gap-1.5 p-1 rounded-full border border-border bg-muted/20">
                            <button
                                v-for="s in ['all', 'active', 'suspended', 'banned']"
                                :key="s"
                                @click="statusFilter = s"
                                class="px-3 py-1 text-[11px] font-bold rounded-full transition-all duration-200 capitalize"
                                :class="statusFilter === s 
                                    ? 'bg-background text-foreground shadow-sm' 
                                    : 'text-muted-foreground hover:text-foreground'"
                            >
                                {{ s }}
                            </button>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2 overflow-x-auto pb-1 lg:pb-0 w-full lg:w-auto no-scrollbar">
                    <span class="text-xs font-semibold text-muted-foreground uppercase tracking-wider whitespace-nowrap">Plan:</span>
                    <div class="flex items-center gap-1.5 p-1 rounded-full border border-border bg-muted/20">
                        <button
                            v-for="p in ['all', 'free', 'paid', 'custom']"
                            :key="p"
                            @click="planFilter = p"
                            class="px-3 py-1 text-[11px] font-bold rounded-full transition-all duration-200 capitalize"
                            :class="planFilter === p 
                                ? 'bg-background text-foreground shadow-sm' 
                                : 'text-muted-foreground hover:text-foreground'"
                        >
                            {{ p }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-card rounded-xl shadow-sm border border-border">
                <div class="overflow-x-auto overflow-y-visible min-h-[400px]">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-muted/50 border-b border-border relative z-10">
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-muted-foreground">User</th>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-muted-foreground">Plan</th>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-muted-foreground">Status</th>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-muted-foreground">Posts used</th>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-muted-foreground text-center">Onboarded</th>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-muted-foreground">Joined</th>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-muted-foreground">Last active</th>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-muted-foreground text-right"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            <tr 
                                v-for="(user, index) in users.data" 
                                :key="user.id" 
                                class="hover:bg-muted/30 transition-colors group"
                                :class="{ 'relative z-50': openDropdown === user.id }"
                            >
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div 
                                            class="h-10 w-10 rounded-full flex items-center justify-center font-bold text-sm"
                                            :class="getAvatarStyle(index)"
                                        >
                                            {{ getInitials(user) }}
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="text-sm font-semibold text-foreground group-hover:text-primary transition-colors">
                                                {{ user.first_name }} {{ user.last_name }}
                                            </span>
                                            <span class="text-[11px] text-muted-foreground">{{ user.email }}</span>
                                            <span class="text-[10px] text-muted-foreground/70">{{ user.timezone }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span 
                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider"
                                        :class="getPlanBadgeClass(user.subscriptions?.[0]?.plan?.type || props.defaultPlan?.type || 'free')"
                                    >
                                        {{ user.subscriptions?.[0]?.plan?.name || props.defaultPlan?.name || 'Free' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span 
                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider"
                                        :class="getStatusBadgeClass(user.status)"
                                    >
                                        {{ user.status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col gap-1.5 w-full max-w-[120px]">
                                        <div class="h-1.5 w-full bg-muted rounded-full overflow-hidden">
                                            <div 
                                                class="h-full transition-all duration-500 rounded-full"
                                                :style="{ width: (user.subscriptions?.[0]?.plan?.post_limit_count || props.defaultPlan?.post_limit_count) ? Math.min((user.generated_contents_count || 0) / (user.subscriptions?.[0]?.plan?.post_limit_count || props.defaultPlan?.post_limit_count) * 100, 100) + '%' : '100%' }"
                                                :class="getPostBarColor(user.generated_contents_count || 0, user.subscriptions?.[0]?.plan?.post_limit_count || props.defaultPlan?.post_limit_count)"
                                            ></div>
                                        </div>
                                        <span class="text-[10px] text-muted-foreground text-right w-full">
                                            {{ user.generated_contents_count || 0 }} / {{ user.subscriptions?.[0]?.plan?.post_limit_count || props.defaultPlan?.post_limit_count || '∞' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <span 
                                            class="h-2 w-2 rounded-full"
                                            :class="user.onboarding_completed_at ? 'bg-emerald-500' : 'bg-muted-foreground/30'"
                                        ></span>
                                        <span class="text-[11px] text-muted-foreground">{{ user.onboarding_completed_at ? 'Done' : 'Pending' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-muted-foreground">
                                    {{ new Date(user.created_at).toLocaleDateString() }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-muted-foreground">
                                    {{ user.last_active_at ? new Date(user.last_active_at).toLocaleDateString() : 'Never' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                    <div class="relative">
                                        <button 
                                            @click.stop="toggleDropdown($event, user.id)"
                                            class="p-2 text-muted-foreground hover:text-foreground transition-colors rounded-lg border border-transparent hover:border-border bg-transparent hover:bg-muted/50"
                                        >
                                            <EllipsisVerticalIcon class="h-5 w-5" />
                                        </button>
                                        
                                        <!-- Dropdown Menu -->
                                        <Teleport to="body">
                                            <div 
                                                v-if="openDropdown === user.id"
                                                class="absolute right-0 w-48 bg-card border border-border rounded-xl shadow-lg z-[9999] overflow-hidden"
                                                :style="dropdownPosition"
                                            >
                                                <div class="py-1">
                                                    <Link 
                                                        :href="route('admin.users.show', user.id)"
                                                        class="w-full flex items-center px-4 py-2.5 text-xs text-foreground hover:bg-muted transition-colors"
                                                    >
                                                        <EyeIcon class="h-4 w-4 mr-3 text-muted-foreground" />
                                                        View detail
                                                    </Link>
                                                    <button class="w-full flex items-center px-4 py-2.5 text-xs text-foreground hover:bg-muted transition-colors">
                                                        <CreditCardIcon class="h-4 w-4 mr-3 text-muted-foreground" />
                                                        Change plan
                                                    </button>
                                                    <button class="w-full flex items-center px-4 py-2.5 text-xs text-foreground hover:bg-muted transition-colors">
                                                        <UserIcon class="h-4 w-4 mr-3 text-muted-foreground" />
                                                        Login as user
                                                    </button>
                                                    <div class="h-px bg-border my-1"></div>
                                                    
                                                    <!-- Status Actions -->
                                                    <template v-if="user.status === 'active'">
                                                        <button 
                                                            class="w-full flex items-center px-4 py-2.5 text-xs text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/20 transition-colors"
                                                            @click="confirmStatusChange(user, 'suspended')"
                                                        >
                                                            <NoSymbolIcon class="h-4 w-4 mr-3" />
                                                            Suspend Account
                                                        </button>
                                                        <button 
                                                            class="w-full flex items-center px-4 py-2.5 text-xs text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/20 transition-colors"
                                                            @click="confirmStatusChange(user, 'banned')"
                                                        >
                                                            <NoSymbolIcon class="h-4 w-4 mr-3" />
                                                            Ban User
                                                        </button>
                                                    </template>

                                                    <template v-else-if="user.status === 'suspended'">
                                                        <button 
                                                            class="w-full flex items-center px-4 py-2.5 text-xs text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 transition-colors"
                                                            @click="confirmStatusChange(user, 'active')"
                                                        >
                                                            <CheckCircleIcon class="h-4 w-4 mr-3" />
                                                            Activate Account
                                                        </button>
                                                        <button 
                                                            class="w-full flex items-center px-4 py-2.5 text-xs text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/20 transition-colors"
                                                            @click="confirmStatusChange(user, 'banned')"
                                                        >
                                                            <NoSymbolIcon class="h-4 w-4 mr-3" />
                                                            Ban User
                                                        </button>
                                                    </template>

                                                    <template v-else-if="user.status === 'banned'">
                                                        <button 
                                                            class="w-full flex items-center px-4 py-2.5 text-xs text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 transition-colors"
                                                            @click="confirmStatusChange(user, 'active')"
                                                        >
                                                            <CheckCircleIcon class="h-4 w-4 mr-3" />
                                                            Activate Account
                                                        </button>
                                                        <button 
                                                            class="w-full flex items-center px-4 py-2.5 text-xs text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/20 transition-colors"
                                                            @click="confirmStatusChange(user, 'suspended')"
                                                        >
                                                            <NoSymbolIcon class="h-4 w-4 mr-3" />
                                                            Suspend Account
                                                        </button>
                                                    </template>

                                                    <button 
                                                        @click="confirmDelete(user)"
                                                        class="w-full flex items-center px-4 py-2.5 text-xs text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/20 transition-colors"
                                                    >
                                                        <TrashIcon class="h-4 w-4 mr-3" />
                                                        Delete Account
                                                    </button>
                                                </div>
                                            </div>
                                        </Teleport>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="users.data.length === 0">
                                <td colspan="8" class="px-6 py-20 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="rounded-full bg-muted p-3 mb-4">
                                            <MagnifyingGlassIcon class="h-6 w-6 text-muted-foreground" />
                                        </div>
                                        <p class="text-lg font-semibold text-foreground">No users found</p>
                                        <p class="text-sm text-muted-foreground mt-1">Try adjusting your filters or search terms.</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div 
                    v-if="users.links.length > 3" 
                    class="px-6 py-4 border-t border-border bg-muted/20 flex flex-col sm:flex-row items-center justify-between gap-4"
                >
                    <p class="text-xs text-muted-foreground font-medium">
                        Showing <span class="text-foreground">{{ users.from }}</span> to <span class="text-foreground">{{ users.to }}</span> of <span class="text-foreground">{{ users.total }}</span> entries
                    </p>
                    <nav class="flex items-center gap-1.5">
                        <Link
                            v-for="(link, k) in users.links"
                            :key="k"
                            :href="link.url || '#'"
                            v-html="link.label"
                            class="px-3.5 py-1.5 text-xs font-semibold rounded-lg transition-all duration-200 border border-transparent shadow-sm"
                            :class="[
                                link.active 
                                    ? 'bg-primary text-primary-foreground' 
                                    : link.url
                                        ? 'bg-background text-foreground border-border hover:bg-muted'
                                        : 'bg-background text-muted-foreground opacity-50 cursor-not-allowed border-border',
                            ]"
                        />
                    </nav>
                </div>
            </div>
        </div>

        <DeleteConfirmationModal
            :show="showDeleteModal"
            :processing="processing"
            title="Delete User"
            message="Are you sure you want to delete this user? This action cannot be undone."
            @close="showDeleteModal = false"
            @confirm="deleteUser"
        />

        <StatusConfirmationModal
            v-if="userForStatus"
            :show="showStatusModal"
            :status="statusToChange"
            :user-name="userForStatus.first_name + ' ' + userForStatus.last_name"
            :processing="processing"
            @close="showStatusModal = false"
            @confirm="updateStatus"
        />
    </AdminLayout>
</template>
