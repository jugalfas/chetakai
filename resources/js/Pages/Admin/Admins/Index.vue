<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Head, router, usePage } from "@inertiajs/vue3";
import { ref, watch, onMounted, onUnmounted } from "vue";
import {
    TrashIcon,
    MagnifyingGlassIcon,
    EllipsisVerticalIcon,
    NoSymbolIcon,
    CheckCircleIcon,
    XMarkIcon,
    CheckCircleIcon as CheckCircleIconSolid
} from "@heroicons/vue/24/outline";
import { PlusIcon, ChevronDown, Check } from "lucide-vue-next";
import DeleteConfirmationModal from "@/Components/DeleteConfirmationModal.vue";
import {
    SelectRoot,
    SelectTrigger,
    SelectValue,
    SelectPortal,
    SelectContent,
    SelectViewport,
    SelectItem,
    SelectItemText,
    SelectItemIndicator,
} from "radix-vue";

const selectTriggerClasses = "flex w-full items-center justify-between rounded-lg border border-border px-3 py-2 text-sm bg-background text-foreground focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all";
const selectContentClasses = "z-[150] w-[--radix-select-trigger-width] overflow-hidden rounded-md border bg-card text-popover-foreground shadow-md border-border";
const selectItemClasses = "relative flex w-full cursor-default select-none items-center rounded-sm py-1.5 pl-2 pr-9 text-sm outline-none focus:bg-muted focus:text-accent-foreground data-[state=checked]:bg-primary/10 data-[state=checked]:text-primary transition-colors";

const props = defineProps({
    admins: Object,
    filters: Object,
    roles: Array,
});

const search = ref(props.filters?.search || "");
const processing = ref(false);
const openDropdown = ref(null);
const editingRole = ref(null);
const showDeleteModal = ref(false);
const adminToDelete = ref(null);

const showInviteModal = ref(false);
const inviteForm = ref({
    first_name: '',
    last_name: '',
    email: '',
    role: 'support'
});

const page = usePage();
const flashToast = ref(null);
let flashTimer = null;
const showFlash = (message, type = 'success') => {
    clearTimeout(flashTimer);
    flashToast.value = { message, type };
    flashTimer = setTimeout(() => { flashToast.value = null; }, 4500);
};

watch(() => page.props.flash?.success, (val) => { if (val) showFlash(val, 'success'); }, { immediate: true });
watch(() => page.props.flash?.error, (val) => { if (val) showFlash(val, 'error'); }, { immediate: true });

const applyFilters = () => {
    router.get(
        route("admin.admins.index"),
        { search: search.value },
        { preserveState: true, replace: true }
    );
};

let timeout = null;
watch(search, () => {
    if (timeout) clearTimeout(timeout);
    timeout = setTimeout(applyFilters, 300);
});

const getInitials = (admin) => {
    return (admin.first_name?.[0] || "") + (admin.last_name?.[0] || "");
};

const getAvatarStyle = (index) => {
    const colors = [
        { bg: 'bg-indigo-100', text: 'text-indigo-700' },
        { bg: 'bg-sky-100', text: 'text-sky-700' },
        { bg: 'bg-purple-100', text: 'text-purple-700' },
    ];
    return `${colors[index % colors.length].bg} ${colors[index % colors.length].text}`;
};

const getRoleBadgeClass = (role) => {
    switch (role) {
        case 'admin': return 'bg-rose-100 text-rose-700 border border-rose-200 dark:bg-rose-900/30 dark:text-rose-400 dark:border-rose-800';
        case 'support': return 'bg-sky-100 text-sky-700 border border-sky-200 dark:bg-sky-900/30 dark:text-sky-400 dark:border-sky-800';
        case 'content_manager': return 'bg-emerald-100 text-emerald-700 border border-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-400 dark:border-emerald-800';
        default: return 'bg-gray-100 text-gray-700 border border-gray-200 dark:bg-gray-900/30 dark:text-gray-400 dark:border-gray-800';
    }
};

const getStatusBadgeClass = (status) => {
    return status === 'active' 
        ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400'
        : 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400';
};

const handleOutsideClick = (e) => {
    if (openDropdown.value && !e.target.closest('.relative')) {
        openDropdown.value = null;
    }
    if (editingRole.value && !e.target.closest('.role-cell')) {
        editingRole.value = null;
    }
};

onMounted(() => { window.addEventListener('click', handleOutsideClick); });
onUnmounted(() => { window.removeEventListener('click', handleOutsideClick); });

const dropdownPosition = ref({ top: '0px', left: '0px' })
const toggleDropdown = (event, id) => {
    if (openDropdown.value === id) { openDropdown.value = null; return; }
    const rect = event.currentTarget.getBoundingClientRect();
    dropdownPosition.value = {
        top: rect.bottom + window.scrollY + 'px',
        left: rect.right - 192 + 'px'
    };
    openDropdown.value = id;
};

const toggleStatus = (admin) => {
    if (admin.id === page.props.auth.admin?.id) {
        showFlash("You cannot suspend yourself.", 'error');
        return;
    }
    router.patch(route('admin.admins.update', admin.id), {
        status: admin.status === 'active' ? 'suspended' : 'active'
    }, {
        onBefore: () => processing.value = true,
        onFinish: () => { processing.value = false; openDropdown.value = null; }
    });
};

const updateRole = (admin, newRole) => {
    if (admin.id === page.props.auth.admin?.id && admin.role === 'super_admin' && newRole !== 'super_admin') {
        showFlash("You cannot demote yourself.", 'error');
        return;
    }
    router.patch(route('admin.admins.update', admin.id), { role: newRole }, {
        onBefore: () => processing.value = true,
        onFinish: () => { processing.value = false; editingRole.value = null; }
    });
};

const confirmDelete = (admin) => {
    if (admin.id === page.props.auth.admin?.id) {
        showFlash("You cannot delete yourself.", 'error');
        return;
    }
    adminToDelete.value = admin;
    showDeleteModal.value = true;
};

const deleteAdmin = () => {
    if (adminToDelete.value) {
        router.delete(route('admin.admins.destroy', adminToDelete.value.id), {
            onBefore: () => processing.value = true,
            onFinish: () => processing.value = false,
            onSuccess: () => { showDeleteModal.value = false; adminToDelete.value = null; }
        });
    }
};

const submitInvite = () => {
    router.post(route('admin.admins.store'), inviteForm.value, {
        onBefore: () => processing.value = true,
        onFinish: () => processing.value = false,
        onSuccess: () => {
            showInviteModal.value = false;
            inviteForm.value = { first_name: '', last_name: '', email: '', role: 'support' };
        }
    });
};
</script>

<template>
    <Head title="Admin Users" />

    <AdminLayout>
        <div class="p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight text-foreground">Admin Users</h1>
                    <p class="mt-1 text-sm text-muted-foreground">Manage administrative access and roles.</p>
                </div>
                <button
                    @click="showInviteModal = true"
                    class="inline-flex items-center px-4 py-2 bg-primary text-primary-foreground rounded-lg hover:bg-primary/90 transition-colors text-sm font-medium"
                >
                    <PlusIcon class="w-4 h-4 mr-2" />
                    Invite Admin
                </button>
            </div>

            <div class="mb-6 flex flex-col items-start gap-4">
                <div class="relative w-full sm:w-96">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <MagnifyingGlassIcon class="h-4 w-4 text-muted-foreground" />
                    </div>
                    <input
                        v-model="search"
                        type="text"
                        class="block w-full rounded-lg border-0 py-2.5 pl-10 text-sm text-foreground ring-1 ring-inset ring-border placeholder:text-muted-foreground focus:ring-2 focus:ring-inset focus:ring-primary bg-background shadow-sm transition-all"
                        placeholder="Search admins..."
                    />
                </div>
            </div>

            <div class="bg-card rounded-xl shadow-sm border border-border">
                <div class="overflow-x-auto overflow-y-visible min-h-[400px]">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-muted/50 border-b border-border relative z-10">
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-muted-foreground">User</th>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-muted-foreground w-48">Role</th>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-muted-foreground">Status</th>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-muted-foreground">Last Login</th>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-muted-foreground text-right"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            <tr 
                                v-for="(admin, index) in admins.data" 
                                :key="admin.id" 
                                class="hover:bg-muted/30 transition-colors group"
                                :class="{ 'relative z-50': openDropdown === admin.id || editingRole === admin.id }"
                            >
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div 
                                            class="h-10 w-10 rounded-full flex items-center justify-center font-bold text-sm"
                                            :class="getAvatarStyle(index)"
                                        >
                                            {{ getInitials(admin) }}
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="text-sm font-semibold text-foreground group-hover:text-primary transition-colors">
                                                {{ admin.first_name }} {{ admin.last_name }} 
                                                <span v-if="admin.id === page.props.auth.admin?.id" class="text-[10px] ml-1 px-1.5 py-0.5 bg-muted rounded text-muted-foreground">You</span>
                                            </span>
                                            <span class="text-[11px] text-muted-foreground">{{ admin.email }}</span>
                                        </div>
                                    </div>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap role-cell relative" @click.stop>
                                    <button 
                                        @click="editingRole = editingRole === admin.id ? null : admin.id"
                                        class="inline-flex items-center gap-1 hover:opacity-80 transition-opacity"
                                        title="Click to edit role"
                                    >
                                        <span 
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider shadow-sm"
                                            :class="getRoleBadgeClass(admin.role)"
                                        >
                                            {{ admin.role ? admin.role.replace('_', ' ') : 'None' }}
                                        </span>
                                    </button>

                                    <!-- Inline Role Edit Dropdown -->
                                    <div 
                                        v-if="editingRole === admin.id"
                                        class="absolute left-6 mt-1 w-48 bg-card border border-border rounded-xl shadow-lg z-[9999] overflow-hidden"
                                    >
                                        <div class="p-1">
                                            <button 
                                                v-for="r in roles" 
                                                :key="r"
                                                @click="updateRole(admin, r)"
                                                class="w-full text-left px-3 py-2 text-xs font-medium rounded-lg hover:bg-muted transition-colors capitalize flex items-center justify-between"
                                                :class="{ 'text-primary bg-primary/5': admin.role === r }"
                                            >
                                                {{ r.replace('_', ' ') }}
                                                <CheckCircleIconSolid v-if="admin.role === r" class="h-4 w-4" />
                                            </button>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span 
                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider"
                                        :class="getStatusBadgeClass(admin.status)"
                                    >
                                        {{ admin.status }}
                                    </span>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-muted-foreground">
                                    {{ admin.last_login_at ? new Date(admin.last_login_at).toLocaleDateString() : 'Never' }}
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                    <div class="relative">
                                        <button 
                                            @click.stop="toggleDropdown($event, admin.id)"
                                            class="p-2 text-muted-foreground hover:text-foreground transition-colors rounded-lg border border-transparent hover:border-border bg-transparent hover:bg-muted/50"
                                        >
                                            <EllipsisVerticalIcon class="h-5 w-5" />
                                        </button>
                                        
                                        <Teleport to="body">
                                            <div 
                                                v-if="openDropdown === admin.id"
                                                class="absolute right-0 w-48 bg-card border border-border rounded-xl shadow-lg z-[9999] overflow-hidden"
                                                :style="dropdownPosition"
                                            >
                                                <div class="py-1">
                                                    <button 
                                                        :disabled="admin.id === page.props.auth.admin?.id"
                                                        class="w-full flex items-center px-4 py-2.5 text-xs transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                                        :class="admin.status === 'active' ? 'text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/20' : 'text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/20'"
                                                        @click="toggleStatus(admin)"
                                                    >
                                                        <template v-if="admin.status === 'active'">
                                                            <NoSymbolIcon class="h-4 w-4 mr-3" /> Suspend
                                                        </template>
                                                        <template v-else>
                                                            <CheckCircleIcon class="h-4 w-4 mr-3" /> Activate
                                                        </template>
                                                    </button>
                                                    
                                                    <div class="h-px bg-border my-1"></div>

                                                    <button 
                                                        :disabled="admin.id === page.props.auth.admin?.id"
                                                        @click="confirmDelete(admin)"
                                                        class="w-full flex items-center px-4 py-2.5 text-xs text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/20 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                                    >
                                                        <TrashIcon class="h-4 w-4 mr-3" /> Delete
                                                    </button>
                                                </div>
                                            </div>
                                        </Teleport>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="admins.data.length === 0">
                                <td colspan="5" class="px-6 py-20 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="rounded-full bg-muted p-3 mb-4">
                                            <MagnifyingGlassIcon class="h-6 w-6 text-muted-foreground" />
                                        </div>
                                        <p class="text-lg font-semibold text-foreground">No admins found</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="admins.links.length > 3" class="px-6 py-4 border-t border-border bg-muted/20 flex items-center justify-end">
                    <nav class="flex items-center gap-1.5">
                        <template v-for="(link, k) in admins.links" :key="k">
                            <Link
                                v-if="link.url"
                                :href="link.url"
                                v-html="link.label"
                                class="px-3.5 py-1.5 text-xs font-semibold rounded-lg transition-all shadow-sm border border-border hover:bg-muted"
                                :class="link.active ? 'bg-primary text-primary-foreground border-transparent' : 'bg-background text-foreground'"
                            />
                            <span v-else v-html="link.label" class="px-3.5 py-1.5 text-xs font-semibold rounded-lg opacity-50 border border-transparent"></span>
                        </template>
                    </nav>
                </div>
            </div>
        </div>

        <DeleteConfirmationModal
            :show="showDeleteModal"
            :processing="processing"
            title="Delete Admin"
            message="Are you sure you want to delete this admin account? This action cannot be undone."
            @close="showDeleteModal = false"
            @confirm="deleteAdmin"
        />

        <!-- Invite Modal -->
        <div v-if="showInviteModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-background/80 backdrop-blur-sm">
            <div class="bg-card w-full max-w-md rounded-2xl shadow-xl border border-border overflow-hidden">
                <div class="px-6 py-4 border-b border-border flex items-center justify-between">
                    <h3 class="text-lg font-bold">Invite Admin User</h3>
                    <button @click="showInviteModal = false" class="text-muted-foreground hover:text-foreground">
                        <XMarkIcon class="h-5 w-5" />
                    </button>
                </div>
                <div class="p-6">
                    <form @submit.prevent="submitInvite" class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-muted-foreground mb-1">First Name</label>
                                <input v-model="inviteForm.first_name" type="text" required class="w-full rounded-lg text-sm border-border bg-background focus:ring-primary focus:border-primary" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-muted-foreground mb-1">Last Name</label>
                                <input v-model="inviteForm.last_name" type="text" required class="w-full rounded-lg text-sm border-border bg-background focus:ring-primary focus:border-primary" />
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-muted-foreground mb-1">Email</label>
                            <input v-model="inviteForm.email" type="email" required class="w-full rounded-lg text-sm border-border bg-background focus:ring-primary focus:border-primary" />
                            <p class="text-[10px] text-muted-foreground mt-1">An invitation email will be sent automatically.</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-muted-foreground mb-1">Role</label>
                            <SelectRoot v-model="inviteForm.role" required>
                                <SelectTrigger :class="selectTriggerClasses">
                                    <SelectValue placeholder="Select role" />
                                    <ChevronDown class="h-3 w-3 opacity-50" />
                                </SelectTrigger>
                                <SelectPortal>
                                    <SelectContent :class="selectContentClasses" position="popper" :side-offset="5">
                                        <SelectViewport class="p-1">
                                            <SelectItem v-for="r in roles" :key="r" :value="r" :class="selectItemClasses">
                                                <SelectItemText class="capitalize">{{ r.replace('_', ' ') }}</SelectItemText>
                                                <SelectItemIndicator class="absolute right-3">
                                                    <Check class="h-3 w-3 stroke-[3px]" />
                                                </SelectItemIndicator>
                                            </SelectItem>
                                        </SelectViewport>
                                    </SelectContent>
                                </SelectPortal>
                            </SelectRoot>
                        </div>
                        
                        <div class="pt-4 flex justify-end gap-3">
                            <button type="button" @click="showInviteModal = false" class="px-4 py-2 text-sm font-medium border border-border rounded-lg text-foreground hover:bg-muted transition-colors">
                                Cancel
                            </button>
                            <button type="submit" :disabled="processing" class="px-4 py-2 text-sm font-medium bg-primary text-primary-foreground rounded-lg hover:bg-primary/90 transition-colors disabled:opacity-50">
                                Send Invite
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Toast -->
        <Transition
            enter-active-class="transition-all duration-300 ease-out"
            enter-from-class="opacity-0 translate-y-2"
            enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition-all duration-200 ease-in"
            leave-from-class="opacity-100 translate-y-0"
            leave-to-class="opacity-0 translate-y-2"
        >
            <div
                v-if="flashToast"
                class="fixed bottom-6 right-6 z-[200] flex items-center gap-3 rounded-2xl border px-5 py-3.5 shadow-2xl bg-card"
                :class="flashToast.type === 'error' ? 'border-rose-500/20' : 'border-emerald-500/20'"
            >
                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full"
                    :class="flashToast.type === 'error' ? 'bg-rose-500/10 text-rose-500' : 'bg-emerald-500/10 text-emerald-500'">
                    <CheckCircleIconSolid v-if="flashToast.type !== 'error'" class="h-4 w-4" />
                    <NoSymbolIcon v-else class="h-4 w-4" />
                </span>
                <p class="text-sm font-semibold text-foreground">{{ flashToast.message }}</p>
                <button @click="flashToast = null" class="ml-2 text-muted-foreground hover:text-foreground transition-colors">
                    <XMarkIcon class="h-4 w-4" />
                </button>
            </div>
        </Transition>
    </AdminLayout>
</template>
