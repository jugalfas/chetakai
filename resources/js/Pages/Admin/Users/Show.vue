<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Head, Link, router, useForm } from "@inertiajs/vue3";
import { ref, computed } from "vue";
import {
    ChevronLeftIcon,
    ArrowRightOnRectangleIcon,
    KeyIcon,
    ArrowPathIcon,
    NoSymbolIcon,
    TrashIcon,
    CheckCircleIcon,
    XMarkIcon,
} from "@heroicons/vue/24/outline";
import StatusConfirmationModal from "@/Components/StatusConfirmationModal.vue";
import PermanentDeleteModal from "@/Components/PermanentDeleteModal.vue";
import ImpersonateConfirmationModal from "@/Components/ImpersonateConfirmationModal.vue";
import ResetOtpConfirmationModal from "@/Components/ResetOtpConfirmationModal.vue";
import ResetPostUsageModal from "@/Components/ResetPostUsageModal.vue";
import ChangePlanModal from "@/Components/ChangePlanModal.vue";
import axios from "axios";

const props = defineProps({
    user: Object,
    contentBreakdown: Array,
    defaultPlan: Object,
    allPlans: Array,
    stats: Object,
    activityLog: Array,
});

const form = useForm({
    note: props.user.internal_notes ?? ''
})

function saveNote() {
    form.post(route('admin.users.note', props.user.id))
}

const getInitials = (user) => {
    return (user.first_name?.[0] || "") + (user.last_name?.[0] || "");
};

const formatDate = (dateString, includeTime = false) => {
    if (!dateString) return "Never";
    const date = new Date(dateString);
    if (includeTime) {
        return date.toLocaleString('en-US', { 
            day: 'numeric', 
            month: 'short', 
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }
    return date.toLocaleDateString('en-US', { 
        day: 'numeric', 
        month: 'short', 
        year: 'numeric' 
    });
};

const getStatusBadgeClass = (status) => {
    switch (status) {
        case 'active': return 'bg-emerald-500/10 text-emerald-500 ring-1 ring-emerald-500/20';
        case 'suspended': return 'bg-amber-500/10 text-amber-500 ring-1 ring-amber-500/20';
        case 'banned': return 'bg-rose-500/10 text-rose-500 ring-1 ring-rose-500/20';
        default: return 'bg-muted/10 text-muted-foreground ring-1 ring-muted/20';
    }
};

const getPlanBadgeClass = (planType) => {
    switch (planType) {
        case 'free': return 'badge-free';
        case 'pro':
        case 'paid': return 'badge-pro';
        case 'biz':
        case 'custom': return 'badge-biz';
        default: return 'badge-free';
    }
};

// Modals state
const showStatusModal = ref(false);
const statusToChange = ref(null);
const showDeleteModal = ref(false);
const showImpersonateModal = ref(false);
const showResetOtpModal = ref(false);
const showPlanModal = ref(false);
const showResetPostUsageModal = ref(false);
const processing = ref(false);
const updatePlanProcessing = ref(false);
const resetPostUsageProcessing = ref(false);

// Toast state
const toast = ref(null); // { message, type }
let toastTimer = null;
const showToast = (message, type = 'success') => {
    clearTimeout(toastTimer);
    toast.value = { message, type };
    toastTimer = setTimeout(() => { toast.value = null; }, 4000);
};

const confirmStatusChange = (status) => {
    statusToChange.value = status;
    showStatusModal.value = true;
};

const updateStatus = () => {
    if (!statusToChange.value) return;
    
    router.patch(route('admin.users.update-status', props.user.id), 
        { status: statusToChange.value }, 
        {
            onBefore: () => processing.value = true,
            onFinish: () => {
                processing.value = false;
                showStatusModal.value = false;
                statusToChange.value = null;
            }
        }
    );
};

const confirmDelete = () => {
    showDeleteModal.value = true;
};

const deleteUser = () => {
    router.delete(route('admin.users.destroy', props.user.id), {
        onBefore: () => processing.value = true,
        onFinish: () => processing.value = false,
        onError: () => {
            processing.value = false;
            showToast('Failed to delete account. Please try again.', 'error');
        }
    });
};


const confirmImpersonate = () => {
    showImpersonateModal.value = true;
};

const impersonateUser = async () => {
    try {
        processing.value = true;
        const response = await axios.post(route('admin.users.impersonate', props.user.id));
        if (response.data.url) {
            window.open(response.data.url, '_blank');
            showImpersonateModal.value = false;
        }
    } catch (error) {
        console.error('Impersonation failed:', error);
    } finally {
        processing.value = false;
    }
};

const confirmResetOtp = () => {
    showResetOtpModal.value = true;
};

const resetOtp = () => {
    router.post(route('admin.users.reset-otp', props.user.id), {}, {
        onBefore: () => processing.value = true,
        onFinish: () => processing.value = false,
        onSuccess: () => {
            showResetOtpModal.value = false;
        }
    });
};

const handleChangePlan = (data) => {
    router.post(route('admin.users.change-subscription', props.user.id), data, {
        onBefore: () => updatePlanProcessing.value = true,
        onFinish: () => {
            updatePlanProcessing.value = false;
            showPlanModal.value = false;
            router.visit(route('admin.users.show', props.user.id));
        },
        preserveScroll: true
    });
};

const resetPostUsage = () => {
    router.post(route('admin.users.reset-post-usage', props.user.id), {}, {
        onBefore: () => resetPostUsageProcessing.value = true,
        onFinish: () => {
            resetPostUsageProcessing.value = false;
            showResetPostUsageModal.value = false;
        },
        onSuccess: () => {
            showToast('Post usage reset successfully');
            router.reload({ only: ['stats'] });
        },
        preserveScroll: true,
    });
};

const activeSubscription = props.user.subscriptions?.find(s => s.status === 'active');
const currentPlan = activeSubscription?.plan || props.defaultPlan;
const postLimit = currentPlan?.post_limit_count || 0;
const usedPosts = props.stats.posts_this_month || 0;
const progressWidth = postLimit > 0 ? Math.min((usedPosts / postLimit) * 100, 100) : 100;

</script>

<template>
    <Head :title="`${user.first_name} ${user.last_name} - User Detail`" />

    <AdminLayout>
        <div class="max-w-7xl mx-auto space-y-6">
            <!-- Breadcrumbs -->
            <div class="flex items-center gap-2 text-sm">
                <Link :href="route('admin.users.index')" class="text-muted-foreground hover:text-foreground transition-colors flex items-center gap-1">
                    <ChevronLeftIcon class="w-4 h-4" />
                    Users
                </Link>
                <span class="text-muted-foreground/50">/</span>
                <span class="text-foreground font-medium">{{ user.first_name }} {{ user.last_name }}</span>
            </div>

            <!-- Header Info -->
            <div class="bg-card border border-border rounded-2xl p-6 shadow-sm overflow-hidden relative group">
                <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                    <UserCircleIcon class="w-32 h-32" />
                </div>
                
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 relative z-10">
                    <div class="flex items-center gap-5">
                        <div class="h-20 w-20 rounded-2xl bg-primary/10 flex items-center justify-center text-primary text-2xl font-bold ring-1 ring-primary/20">
                            {{ getInitials(user) }}
                        </div>
                        <div>
                            <div class="flex items-center gap-3 flex-wrap">
                                <h1 class="text-2xl font-bold text-foreground">
                                    {{ user.first_name }} {{ user.last_name }}
                                </h1>
                                <div class="flex items-center gap-2">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider" :class="getStatusBadgeClass(user.status)">{{ user.status }}</span>
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-indigo-500/10 text-indigo-400 ring-1 ring-indigo-500/20">{{ currentPlan.name }}</span>
                                    <span v-if="user.email_verified_at" class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-500/10 text-emerald-400 ring-1 ring-emerald-500/20">Verified</span>
                                </div>
                            </div>
                            <p class="text-muted-foreground mt-1">{{ user.email }}</p>
                            <div class="flex items-center gap-4 mt-3 text-xs text-muted-foreground font-medium">
                                <span class="flex items-center gap-1">
                                    <span class="h-1.5 w-1.5 rounded-full bg-muted-foreground/30"></span>
                                    ID: USR-{{ String(user.id).padStart(5, '0') }}
                                </span>
                                <span class="flex items-center gap-1">
                                    <span class="h-1.5 w-1.5 rounded-full bg-muted-foreground/30"></span>
                                    {{ user.timezone }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-row md:flex-col items-end gap-x-12 gap-4">
                        <div class="text-right">
                            <p class="text-[10px] text-muted-foreground uppercase tracking-widest font-bold mb-1">Joined</p>
                            <p class="text-sm font-semibold text-foreground whitespace-nowrap">{{ formatDate(user.created_at) }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] text-muted-foreground uppercase tracking-widest font-bold mb-1">Last Active</p>
                            <p class="text-sm font-semibold text-emerald-400 whitespace-nowrap">{{ formatDate(user.last_active_at) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- General Information -->
                    <div class="bg-card border border-border rounded-2xl overflow-hidden shadow-sm">
                        <div class="px-6 py-4 border-b border-border bg-muted/30">
                            <h3 class="text-sm font-bold text-foreground">General Information</h3>
                        </div>
                        <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-x-12 gap-y-10">
                            <div v-for="(val, label) in { 
                                'First Name': user.first_name, 
                                'Last Name': user.last_name,
                                'Email Address': user.email,
                                'Email Verified At': formatDate(user.email_verified_at, true),
                                'Timezone': user.timezone,
                                'Account Status': user.status,
                                'Onboarding': user.onboarding_completed_at ? 'Completed on ' + formatDate(user.onboarding_completed_at) : 'Pending'
                            }" :key="label" class="space-y-1 mb-4">
                                <p class="text-[10px] font-bold text-muted-foreground uppercase tracking-widest mb-1">{{ label }}</p>
                                <p class="text-sm text-foreground font-medium" :class="{ 'capitalize': label === 'Account Status' }">
                                    {{ val }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Usage Stats -->
                    <div class="bg-card border border-border rounded-2xl p-6 shadow-sm">
                        <h3 class="text-sm font-bold text-foreground mb-6">Content Usage</h3>
                        <div class="grid grid-cols-3 gap-6 mb-8">
                            <div v-for="stat in [
                                { label: 'This Month', val: stats.posts_this_month, sub: 'of ' + (postLimit || '∞') + ' limit' },
                                { label: 'Total Posts', val: stats.total_posts_ever, sub: 'since ' + formatDate(user.created_at) },
                                { label: 'Success Rate', val: (stats.total_posts_ever > 0 ? Math.round((stats.published_count / stats.total_posts_ever) * 100) : 0) + '%', sub: stats.published_count + ' published' }
                            ]" :key="stat.label" class="bg-muted/30 rounded-xl p-4 ring-1 ring-border shadow-inner">
                                <p class="text-[10px] font-bold text-muted-foreground uppercase tracking-widest mb-1">{{ stat.label }}</p>
                                <p class="text-2xl font-bold text-foreground">{{ stat.val }}</p>
                                <p class="text-[10px] text-muted-foreground mt-1">{{ stat.sub }}</p>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <div class="flex justify-between items-end">
                                <p class="text-xs font-bold text-muted-foreground">Monthly Quota</p>
                                <p class="text-xs font-bold text-foreground">{{ usedPosts }} / {{ postLimit || '∞' }} Used</p>
                            </div>
                            <div class="h-2 w-full bg-muted rounded-full overflow-hidden ring-1 ring-border shadow-inner">
                                <div 
                                    class="h-full bg-primary transition-all duration-1000 ease-out rounded-full shadow-[0_0_10px_rgba(var(--primary),0.5)]" 
                                    :style="{ width: progressWidth + '%' }"
                                    :class="{ 'bg-rose-500': progressWidth > 90, 'bg-amber-500': progressWidth > 70 }"
                                ></div>
                            </div>
                        </div>

                        <div class="mt-8">
                            <p class="text-xs font-bold text-muted-foreground mb-4">Content Breakdown</p>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                                <div v-for="item in contentBreakdown" :key="item.title" class="p-3 rounded-xl bg-muted/20 border border-border text-center hover:bg-muted/30 transition-colors">
                                    <p class="text-lg font-bold text-foreground">{{ item.count }}</p>
                                    <p class="text-[10px] font-bold text-muted-foreground uppercase tracking-wider">{{ item.title }}</p>
                                </div>
                                <div v-if="contentBreakdown.length === 0" class="col-span-full py-6 text-center text-muted-foreground italic text-sm">
                                    No content types activity found.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div class="bg-card border border-border rounded-2xl shadow-sm overflow-hidden text-sm">
                        <div class="px-6 py-4 border-b border-border bg-muted/30 flex justify-between items-center">
                            <h3 class="text-sm font-bold text-foreground">Recent Activity</h3>
                            <Link href="#" class="text-primary text-[11px] font-bold uppercase tracking-widest hover:underline">View All</Link>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="text-left text-[10px] font-bold text-muted-foreground uppercase tracking-widest bg-muted/20 border-b border-border">
                                        <th class="px-6 py-3">Type</th>
                                        <th class="px-6 py-3">Description</th>
                                        <th class="px-6 py-3">Platform</th>
                                        <th class="px-6 py-3 text-right">Date & Time</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-border">
                                    <tr v-for="log in activityLog" :key="log.id" class="hover:bg-muted/10 transition-colors group">
                                        <td class="px-6 py-4">
                                            <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider shadow-sm border border-transparent" :class="{
                                                'bg-emerald-500/10 text-emerald-500 border-emerald-500/20': log.type === 'Published' || log.type === 'Generated',
                                                'bg-rose-500/10 text-rose-500 border-rose-500/20': log.type === 'Failed',
                                                'bg-blue-500/10 text-blue-500 border-blue-500/20': log.type === 'Login',
                                                'bg-amber-500/10 text-amber-500 border-amber-500/20': log.type === 'Plan change' || log.type === 'Upgrade',
                                                'bg-muted-foreground/10 text-muted-foreground border-muted-foreground/20': !['Published', 'Generated', 'Failed', 'Login', 'Plan change', 'Upgrade'].includes(log.type)
                                            }">{{ log.type }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-foreground font-medium group-hover:text-primary transition-colors">{{ log.description }}</td>
                                        <td class="px-6 py-4 text-muted-foreground">{{ log.platform || '—' }}</td>
                                        <td class="px-6 py-4 text-right text-muted-foreground font-mono text-[11px]">{{ formatDate(log.created_at, true) }}</td>
                                    </tr>
                                    <tr v-if="activityLog?.length === 0">
                                        <td colspan="4" class="px-6 py-12 text-center text-muted-foreground italic">No recent activities found.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-6">
                    <!-- Subscription Card -->
                    <div class="bg-card border border-border rounded-2xl p-6 shadow-sm shadow-indigo-500/5">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-sm font-bold text-foreground">Subscription</h3>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider" 
                                :class="activeSubscription ? 'bg-emerald-500/10 text-emerald-500' : 'bg-rose-500/10 text-rose-500'">
                                {{ activeSubscription ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                        
                        <div class="mb-6">
                            <p class="text-xl font-bold text-foreground">{{ currentPlan.name }}</p>
                            <p class="text-sm text-muted-foreground">{{ currentPlan.price > 0 ? '$' + currentPlan.price + ' / month' : 'Free plan' }}</p>
                        </div>

                        <div class="space-y-3 mb-6">
                            <div v-for="item in [
                                { l: 'Started', v: formatDate(activeSubscription?.created_at) },
                                { l: 'Renews', v: formatDate(activeSubscription?.ends_at) },
                                { l: 'Trial', v: activeSubscription?.trial_ends_at ? 'Used' : 'No' },
                                { l: 'Customer ID', v: activeSubscription?.stripe_id || '—', mono: true }
                            ]" :key="item.l" class="flex justify-between items-center text-xs">
                                <span class="text-muted-foreground font-medium">{{ item.l }}</span>
                                <span class="text-foreground font-bold" :class="{ 'font-mono text-[10px]': item.mono }">{{ item.v }}</span>
                            </div>
                        </div>

                        <button 
                            @click="showPlanModal = true"
                            class="w-full py-2.5 rounded-xl bg-background border border-border hover:bg-muted text-xs font-bold text-foreground transition-all flex items-center justify-center gap-2 ring-offset-background focus:outline-none focus:ring-2 focus:ring-primary"
                        >
                            <ArrowPathIcon class="w-4 h-4" />
                            Change Plan
                        </button>
                    </div>

                    <!-- Admin Actions -->
                    <div class="bg-card border border-border rounded-2xl p-6 shadow-sm">
                        <h3 class="text-sm font-bold text-foreground mb-6">Admin Actions</h3>
                        <div class="space-y-2">
                            <button @click="confirmImpersonate" class="w-full group flex items-center gap-3 px-4 py-2.5 rounded-xl border border-border hover:bg-muted text-xs font-bold text-foreground transition-all">
                                <ArrowRightOnRectangleIcon class="w-4 h-4 text-muted-foreground group-hover:text-primary transition-colors" />
                                Login as this user
                            </button>
                            <button @click="confirmResetOtp" class="w-full group flex items-center gap-3 px-4 py-2.5 rounded-xl border border-border hover:bg-muted text-xs font-bold text-foreground transition-all">
                                <KeyIcon class="w-4 h-4 text-muted-foreground group-hover:text-primary transition-colors" />
                                Reset password / OTP
                            </button>
                            <button @click="showResetPostUsageModal = true" class="w-full group flex items-center gap-3 px-4 py-2.5 rounded-xl border border-border hover:bg-muted text-xs font-bold text-foreground transition-all">
                                <ArrowPathIcon class="w-4 h-4 text-muted-foreground group-hover:text-primary transition-colors" />
                                Reset post usage
                            </button>
                            
                            <div class="h-px bg-border my-2"></div>

                            <template v-if="user.status === 'active'">
                                <button @click="confirmStatusChange('suspended')" class="w-full group flex items-center gap-3 px-4 py-2.5 rounded-xl border border-amber-500/20 bg-amber-500/5 hover:bg-amber-500/10 text-xs font-bold text-amber-500 transition-all">
                                    <NoSymbolIcon class="w-4 h-4" />
                                    Suspend account
                                </button>
                                <button @click="confirmStatusChange('banned')" class="w-full group flex items-center gap-3 px-4 py-2.5 rounded-xl border-rose-500/20 bg-rose-500/5 hover:bg-rose-500/10 text-xs font-bold text-rose-500 transition-all">
                                    <NoSymbolIcon class="w-4 h-4" />
                                    Ban account
                                </button>
                            </template>
                            <template v-else>
                                <button @click="confirmStatusChange('active')" class="w-full group flex items-center gap-3 px-4 py-2.5 rounded-xl border-emerald-500/20 bg-emerald-500/5 hover:bg-emerald-500/10 text-xs font-bold text-emerald-500 transition-all">
                                    <CheckCircleIcon class="w-4 h-4" />
                                    Activate account
                                </button>
                            </template>

                            <button @click="confirmDelete" class="w-full group flex items-center justify-center gap-3 px-4 py-2.5 rounded-xl border border-rose-500/20 bg-rose-500/5 hover:bg-rose-500/10 text-xs font-bold text-rose-500 transition-all">
                                <TrashIcon class="w-4 h-4" />
                                Delete Account Permanently
                            </button>
                        </div>
                    </div>

                    <!-- Internal Notes -->
                    <div class="bg-card border border-border rounded-2xl p-6 shadow-sm">
                        <h3 class="text-sm font-bold text-foreground mb-4">Internal Notes</h3>
                        <textarea v-model="form.note" class="w-full bg-muted/20 border border-border rounded-xl p-3 text-xs text-foreground placeholder:text-muted-foreground/50 focus:ring-1 focus:ring-primary focus:border-primary outline-none transition-all min-h-[100px] resize-none" placeholder="Add admin notes about this user — hidden from user…"></textarea>
                        <button @click="saveNote"
                            :disabled="form.processing" class="w-full mt-3 py-2 bg-foreground text-background rounded-xl text-xs font-bold hover:bg-foreground/90 transition-all">
                            Save Note
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modals -->
        <StatusConfirmationModal
            :show="showStatusModal"
            :status="statusToChange"
            :user-name="user.first_name + ' ' + user.last_name"
            :current-status="user.status"
            :processing="processing"
            @close="showStatusModal = false"
            @confirm="updateStatus"
        />

        <PermanentDeleteModal
            :show="showDeleteModal"
            :processing="processing"
            :user-name="user.first_name + ' ' + user.last_name"
            :user-email="user.email"
            @close="showDeleteModal = false"
            @confirm="deleteUser"
        />

        <ImpersonateConfirmationModal
            :show="showImpersonateModal"
            :processing="processing"
            :user-name="user.first_name + ' ' + user.last_name"
            :user-id="user.id"
            @close="showImpersonateModal = false"
            @confirm="impersonateUser"
        />

        <ResetOtpConfirmationModal
            :show="showResetOtpModal"
            :processing="processing"
            :user-name="user.first_name + ' ' + user.last_name"
            :email="user.email"
            @close="showResetOtpModal = false"
            @confirm="resetOtp"
        />

        <ChangePlanModal
            :show="showPlanModal"
            :user="user"
            :current-plan="currentPlan"
            :all-plans="allPlans"
            :stats="stats"
            :processing="updatePlanProcessing"
            @close="showPlanModal = false"
            @confirm="handleChangePlan"
        />

        <ResetPostUsageModal
            :show="showResetPostUsageModal"
            :processing="resetPostUsageProcessing"
            :user-name="user.first_name + ' ' + user.last_name"
            :used-posts="usedPosts"
            :post-limit="postLimit"
            @close="showResetPostUsageModal = false"
            @confirm="resetPostUsage"
        />

        <!-- Toast notification -->
        <Transition
            enter-active-class="transition-all duration-300 ease-out"
            enter-from-class="opacity-0 translate-y-2"
            enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition-all duration-200 ease-in"
            leave-from-class="opacity-100 translate-y-0"
            leave-to-class="opacity-0 translate-y-2"
        >
            <div
                v-if="toast"
                class="fixed bottom-6 right-6 z-[200] flex items-center gap-3 rounded-2xl border border-emerald-500/20 bg-card px-5 py-3.5 shadow-2xl"
            >
                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-emerald-500/10 text-emerald-500">
                    <CheckCircleIcon class="h-4 w-4" />
                </span>
                <p class="text-sm font-semibold text-foreground">{{ toast.message }}</p>
                <button @click="toast = null" class="ml-2 text-muted-foreground hover:text-foreground transition-colors">
                    <XMarkIcon class="h-4 w-4" />
                </button>
            </div>
        </Transition>
    </AdminLayout>
</template>

<style scoped>
/* No raw CSS needed, everything handled by Tailwind for better theme integration */
::-webkit-scrollbar {
  width: 5px;
}
::-webkit-scrollbar-track {
  background: transparent;
}
::-webkit-scrollbar-thumb {
  background: rgba(var(--primary), 0.1);
  border-radius: 10px;
}
::-webkit-scrollbar-thumb:hover {
  background: rgba(var(--primary), 0.2);
}
</style>

