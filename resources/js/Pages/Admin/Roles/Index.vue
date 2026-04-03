<script setup>
import { ref, computed, watch } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { CheckCircleIcon, XMarkIcon } from '@heroicons/vue/24/outline';
import { CheckCircleIcon as CheckCircleIconSolid } from '@heroicons/vue/24/solid';

const props = defineProps({
    roles: Array,
    adminCounts: Object,
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

const modules = [
  { label: 'Dashboard', perms: [
    { key: 'dashboard.view', label: 'View dashboard' }
  ]},
  { label: 'Users', perms: [
    { key: 'users.view', label: 'View users list' },
    { key: 'users.create', label: 'Create user' },
    { key: 'users.edit', label: 'Edit user details' },
    { key: 'users.suspend', label: 'Suspend / ban user' },
    { key: 'users.impersonate', label: 'Login as user' },
    { key: 'users.change_plan', label: 'Change user plan' },
    { key: 'users.reset_usage', label: 'Reset post usage' },
    { key: 'users.reset_otp', label: 'Send OTP reset' },
    { key: 'users.delete', label: 'Delete user permanently' },
  ]},
  { label: 'Subscription plans', perms: [
    { key: 'plans.view', label: 'View plans' },
    { key: 'plans.create', label: 'Create plan' },
    { key: 'plans.edit', label: 'Edit plan' },
    { key: 'plans.delete', label: 'Delete plan' },
  ]},
  { label: 'Prompt templates', perms: [
    { key: 'prompts.view', label: 'View prompt templates' },
    { key: 'prompts.create', label: 'Create prompt template' },
    { key: 'prompts.edit', label: 'Edit prompt template' },
    { key: 'prompts.delete', label: 'Delete prompt template' },
  ]},
  { label: 'Content types', perms: [
    { key: 'content_types.view', label: 'View content types' },
    { key: 'content_types.manage', label: 'Create / edit / delete content types' },
  ]},
  { label: 'Platforms & integrations', perms: [
    { key: 'platforms.view', label: 'View platforms' },
    { key: 'platforms.manage', label: 'Manage platform integrations' },
  ]},
  { label: 'Email templates', perms: [
    { key: 'email_templates.view', label: 'View email templates' },
    { key: 'email_templates.manage', label: 'Edit & toggle email templates' },
  ]},
  { label: 'Audit logs', perms: [
    { key: 'audit_logs.view', label: 'View audit logs' },
    { key: 'audit_logs.export', label: 'Export audit logs' },
  ]},
  { label: 'Platform settings', perms: [
    { key: 'settings.view', label: 'View platform settings' },
    { key: 'settings.manage', label: 'Edit platform settings' },
  ]},
  { label: 'Admin users', perms: [
    { key: 'admin_users.view', label: 'View admin users' },
    { key: 'admin_users.create', label: 'Invite new admin' },
    { key: 'admin_users.edit', label: 'Edit admin role & status' },
  ]},
  { label: 'Roles & permissions', perms: [
    { key: 'roles.manage', label: 'Manage role permissions' },
  ]},
];

const lockedPerms = {
  admin: ['admin_users.view','admin_users.create','admin_users.edit','roles.manage'],
  content_manager: [],
  support: [],
};

const currentRole = ref(null);
const currentPerms = ref({});
const isDirty = ref(false);
const processing = ref(false);

const selectRole = (roleKey) => {
    const roleObj = props.roles.find(r => r.name === roleKey);
    currentRole.value = roleObj;
    isDirty.value = false;
    
    currentPerms.value = {};
    if (roleObj) {
        roleObj.permissions.forEach(p => {
            currentPerms.value[p.name] = true;
        });
    }
};

const onToggle = (key) => {
    if (currentRole.value.name === 'super_admin') return;
    
    const roleLocks = lockedPerms[currentRole.value.name] || [];
    if (roleLocks.includes(key)) return;

    currentPerms.value[key] = !currentPerms.value[key];
    isDirty.value = true;
};

const savePerms = () => {
    if (!currentRole.value || currentRole.value.name === 'super_admin') return;

    const savedKeys = Object.keys(currentPerms.value).filter(k => currentPerms.value[k]);

    router.put(route('admin.roles.update', currentRole.value.id), {
        permissions: savedKeys
    }, {
        preserveScroll: true,
        onBefore: () => { processing.value = true; },
        onFinish: () => { 
            processing.value = false; 
            isDirty.value = false; 
        }
    });
};

const formatRoleName = (name) => {
    return name.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
};

const getRoleDesc = (name) => {
    switch (name) {
        case 'super_admin': return 'Full platform access. Cannot be restricted.';
        case 'admin': return 'Full access except admin users and role management.';
        case 'content_manager': return 'Manages prompts and content types only.';
        case 'support': return 'User management and troubleshooting only.';
        default: return 'No description';
    }
};

const getCount = (name) => {
    const c = props.adminCounts[name] || 0;
    return c + (c === 1 ? ' user' : ' users');
};
</script>

<template>
    <Head title="Roles & Permissions" />

    <AdminLayout>
        <div class="px-6 py-6 max-w-[1200px] mx-auto">
            <div class="mb-8">
                <h1 class="text-2xl font-bold tracking-tight text-foreground">Roles & permissions</h1>
                <p class="text-sm text-muted-foreground mt-1">Select a role below to view and manage its permissions</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <!-- Fallback iterators based on existing generic roles array -->
                <div 
                    v-for="role in roles" 
                    :key="role.id"
                    role="button"
                    tabindex="0"
                    @click="selectRole(role.name)"
                    class="bg-card border border-border rounded-xl p-4 cursor-pointer transition-all hover:border-primary hover:shadow-md"
                    :class="[
                        currentRole?.name === role.name ? 'ring-2 ring-primary border-transparent' : '',
                        role.name === 'super_admin' ? 'cursor-not-allowed opacity-90' : ''
                    ]"
                >
                    <div class="w-10 h-10 rounded-full flex items-center justify-center mb-3"
                         :class="{
                           'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400': role.name === 'super_admin',
                           'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400': role.name === 'admin',
                           'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400': role.name === 'content_manager',
                           'bg-sky-100 text-sky-700 dark:bg-sky-900/30 dark:text-sky-400': role.name === 'support',
                           'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-400': !['super_admin','admin','content_manager','support'].includes(role.name)
                         }"
                    >
                        <!-- Icons based on role type -->
                        <svg v-if="role.name === 'super_admin'" class="w-5 h-5" viewBox="0 0 16 16" fill="none"><path d="M8 2l1.5 3.5L13 6l-2.5 2.5.5 3.5L8 10.5 5 12l.5-3.5L3 6l3.5-.5z" stroke="currentColor" stroke-width="1.2" stroke-linejoin="round" fill="none"/></svg>
                        <svg v-else-if="role.name === 'admin'" class="w-5 h-5" viewBox="0 0 16 16" fill="none"><circle cx="8" cy="5.5" r="2.5" stroke="currentColor" stroke-width="1.2"/><path d="M3 13c0-3.3 2.2-5 5-5s5 1.7 5 5" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" fill="none"/></svg>
                        <svg v-else-if="role.name === 'content_manager'" class="w-5 h-5" viewBox="0 0 16 16" fill="none"><rect x="3" y="2" width="10" height="12" rx="1.5" stroke="currentColor" stroke-width="1.2"/><path d="M5.5 6h5M5.5 8.5h3" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/></svg>
                        <svg v-else class="w-5 h-5" viewBox="0 0 16 16" fill="none"><circle cx="8" cy="8" r="5.5" stroke="currentColor" stroke-width="1.2"/><path d="M8 5v3.5M8 10.5v.5" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/></svg>
                    </div>

                    <div class="text-sm font-semibold text-foreground mb-1">{{ formatRoleName(role.name) }}</div>
                    <div class="text-xs text-muted-foreground leading-tight mb-3 h-8">{{ getRoleDesc(role.name) }}</div>
                    
                    <div class="flex items-center justify-between mb-1">
                        <div class="text-xs font-medium text-muted-foreground">{{ getCount(role.name) }}</div>
                    </div>
                    
                    <span 
                        class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider"
                        :class="role.name === 'super_admin' ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400' : 'bg-muted text-muted-foreground'"
                    >
                        {{ role.name === 'super_admin' ? 'All permissions' : 'Configurable' }}
                    </span>
                    
                    <div v-if="role.name === 'super_admin'" class="text-[10px] text-muted-foreground mt-2 flex items-center gap-1">
                        <svg class="w-3 h-3" viewBox="0 0 10 10" fill="none"><rect x="2" y="4" width="6" height="5" rx="1" stroke="currentColor" stroke-width="1"/><path d="M3.5 4V3a1.5 1.5 0 013 0v1" stroke="currentColor" stroke-width="1"/></svg>
                        Locked — not editable
                    </div>
                </div>
            </div>

            <div v-if="currentRole?.name === 'super_admin'" class="bg-amber-500/10 border border-amber-500/20 rounded-lg p-3 text-xs text-amber-600 mb-6 font-medium">
                Super admin always has all permissions and cannot be restricted. This is enforced at the code level.
            </div>

            <div class="bg-card border border-border rounded-xl overflow-hidden shadow-sm">
                <div class="p-5 border-b border-border flex items-center justify-between bg-muted/20">
                    <div>
                        <div class="text-sm font-semibold text-foreground tracking-tight">
                            {{ currentRole ? `${formatRoleName(currentRole.name)} — permission set` : 'Select a role above to manage its permissions' }}
                        </div>
                        <div class="text-[13px] text-muted-foreground mt-1">
                            {{ currentRole ? (currentRole.name === 'super_admin' ? 'This role always has full access and cannot be edited' : 'Toggle permissions on or off. Greyed permissions are locked for this role.') : 'Click any role card to load its permission set' }}
                        </div>
                    </div>
                    <div>
                        <button 
                            @click="savePerms"
                            class="px-4 py-2 bg-foreground text-background text-[13px] font-bold rounded-lg shadow-sm hover:opacity-90 disabled:opacity-50 disabled:cursor-not-allowed transition-all"
                            :disabled="!currentRole || currentRole.name === 'super_admin' || (!isDirty && !processing) || processing"
                        >
                            {{ processing ? 'Saving...' : 'Save changes' }}
                        </button>
                    </div>
                </div>

                <div v-if="!currentRole" class="p-16 text-center text-sm text-muted-foreground opacity-70">
                    No role selected
                </div>

                <div v-else class="divide-y divide-border">
                    <div v-for="mod in modules" :key="mod.label" class="permissions-group">
                        <div class="px-5 py-2.5 bg-muted/40 flex items-center gap-2 border-b border-border shadow-inner">
                            <span class="text-[13px] font-bold uppercase tracking-widest text-muted-foreground/80">{{ mod.label }}</span>
                        </div>
                        
                        <div class="divide-y divide-border">
                            <div 
                                v-for="p in mod.perms" 
                                :key="p.key" 
                                class="flex items-center justify-between px-6 py-3.5 hover:bg-muted/10 transition-colors"
                            >
                                <div class="flex-1">
                                    <div class="text-[13px] font-semibold text-foreground">{{ p.label }}</div>
                                    <div class="text-[11px] font-mono text-muted-foreground mt-0.5 opacity-80">{{ p.key }}</div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div v-if="currentRole.name === 'super_admin' || (lockedPerms[currentRole.name] || []).includes(p.key)" 
                                         class="text-[10px] font-bold uppercase tracking-widest text-muted-foreground bg-muted px-2 py-1 rounded shadow-inner">
                                        {{ currentRole.name === 'super_admin' ? 'Always on' : 'Locked off' }}
                                    </div>
                                    
                                    <label class="relative inline-flex items-center cursor-pointer" :class="{ 'cursor-not-allowed opacity-50': currentRole.name === 'super_admin' || (lockedPerms[currentRole.name] || []).includes(p.key) }">
                                        <input 
                                            type="checkbox" 
                                            class="sr-only peer" 
                                            :checked="currentRole.name === 'super_admin' ? true : !!currentPerms[p.key]"
                                            :disabled="currentRole.name === 'super_admin' || (lockedPerms[currentRole.name] || []).includes(p.key)"
                                            @change="onToggle(p.key)"
                                        >
                                        <div class="w-9 h-5 bg-muted peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-primary border border-border/50"></div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Toast Feedback -->
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
                        <XMarkIcon v-else class="h-4 w-4" />
                    </span>
                    <p class="text-sm font-semibold text-foreground">{{ flashToast.message }}</p>
                    <button @click="flashToast = null" class="ml-2 text-muted-foreground hover:text-foreground transition-colors">
                        <XMarkIcon class="h-4 w-4" />
                    </button>
                </div>
            </Transition>
        </div>
    </AdminLayout>
</template>
