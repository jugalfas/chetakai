<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Head, router, Link } from "@inertiajs/vue3";
import { ref, watch } from "vue";
import { toast } from "vue-sonner";
import { PencilIcon, TrashIcon, PlusIcon } from "lucide-vue-next";
import DeleteConfirmationModal from "@/Components/DeleteConfirmationModal.vue";

const props = defineProps({
    subscriptionPlans: {
        type: Array,
        default: () => [],
    },
    stats: {
        type: Array,
        default: () => [],
    },
    filters: {
        type: Object,
        default: () => ({ status: 'all' }),
    },
});

const statusFilter = ref(props.filters.status || "all");

watch(statusFilter, (value) => {
    router.get(
        route("admin.subscription-plans.index"),
        { status: value },
        { preserveState: true, replace: true }
    );
});

const showDeleteModal = ref(false);
const planToDelete = ref(null);
const processing = ref(false);

const confirmDelete = (plan) => {
    planToDelete.value = plan;
    showDeleteModal.value = true;
};

const deletePlan = () => {
    if (!planToDelete.value) return;
    router.delete(route("admin.subscription-plans.destroy", planToDelete.value.id), {
        onBefore: () => (processing.value = true),
        onFinish: () => (processing.value = false),
        onSuccess: () => {
            showDeleteModal.value = false;
            planToDelete.value = null;
            toast.success("Plan deleted successfully");
        },
    });
};

// The stats are now coming from props
</script>

<template>
    <Head title="Subscription Plans" />
    <AdminLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-foreground leading-tight">Subscription Plans</h2>
                <Link
                    :href="route('admin.subscription-plans.create')"
                    class="inline-flex items-center px-4 py-2 bg-primary text-primary-foreground rounded-lg hover:bg-primary/90 transition-colors text-sm font-medium"
                >
                    <PlusIcon class="w-4 h-4 mr-2" />
                    Create Plan
                </Link>
            </div>
        </template>

        <div class="p-6">
            <div class="max-w-7xl mx-auto">
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div
                        v-for="stat in props.stats"
                        :key="stat.label"
                        class="bg-card border border-border rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow"
                    >
                        <p class="text-sm font-medium text-muted-foreground mb-2">{{ stat.label }}</p>
                        <h4 class="text-3xl font-bold text-foreground mb-1">{{ stat.value }}</h4>
                        <p class="text-xs text-muted-foreground">{{ stat.subtext }}</p>
                    </div>
                </div>

                <div class="bg-card rounded-xl shadow-sm border border-border p-6 font-josefin">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-6">
                            <h3 class="text-lg font-semibold text-foreground">Plan List</h3>
                            
                            <!-- Filter -->
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">Filter:</span>
                                <div class="flex items-center gap-1.5 bg-muted/30 p-1 rounded-full border border-border">
                                    <button
                                        @click="statusFilter = 'all'"
                                        class="px-4 py-1.5 text-xs font-bold rounded-full transition-all duration-200"
                                        :class="statusFilter === 'all' 
                                            ? 'bg-background text-foreground shadow-sm' 
                                            : 'text-muted-foreground hover:text-foreground'"
                                    >
                                        All plans
                                    </button>
                                    <button
                                        @click="statusFilter = 'active'"
                                        class="px-4 py-1.5 text-xs font-bold rounded-full transition-all duration-200"
                                        :class="statusFilter === 'active' 
                                            ? 'bg-background text-foreground shadow-sm' 
                                            : 'text-muted-foreground hover:text-foreground'"
                                    >
                                        Active
                                    </button>
                                    <button
                                        @click="statusFilter = 'inactive'"
                                        class="px-4 py-1.5 text-xs font-bold rounded-full transition-all duration-200"
                                        :class="statusFilter === 'inactive' 
                                            ? 'bg-background text-foreground shadow-sm' 
                                            : 'text-muted-foreground hover:text-foreground'"
                                    >
                                        Inactive
                                    </button>
                                </div>
                            </div>
                        </div>

                        <Link
                            :href="route('admin.subscription-plans.create')"
                            class="inline-flex items-center px-4 py-2 bg-primary text-primary-foreground rounded-lg hover:bg-primary/90 transition-colors"
                        >
                            <PlusIcon class="w-4 h-4 mr-2" />
                            Create Plan
                        </Link>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-border">
                                    <th class="px-4 py-3 text-xs font-medium text-muted-foreground uppercase">Plan name</th>
                                    <th class="px-4 py-3 text-xs font-medium text-muted-foreground uppercase">Price</th>
                                    <th class="px-4 py-3 text-xs font-medium text-muted-foreground uppercase">Post limit</th>
                                    <th class="px-4 py-3 text-xs font-medium text-muted-foreground uppercase">Social accounts</th>
                                    <th class="px-4 py-3 text-xs font-medium text-muted-foreground uppercase">Users</th>
                                    <th class="px-4 py-3 text-xs font-medium text-muted-foreground uppercase">Revenue</th>
                                    <th class="px-4 py-3 text-xs font-medium text-muted-foreground uppercase">Status</th>
                                    <th class="px-4 py-3 text-xs font-medium text-muted-foreground uppercase text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="plan in subscriptionPlans"
                                    :key="plan.id"
                                    class="border-b border-border hover:bg-muted/30 transition-colors"
                                >
                                    <td class="px-4 py-3 text-sm text-foreground font-medium flex flex-col">
                                        {{ plan.name }}
                                        <span class="text-[10px] text-muted-foreground font-normal uppercase">{{ plan.badge_label || (plan.monthly_price > 0 ? 'Pro' : 'Free') }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-foreground">
                                        ${{ plan.monthly_price }}
                                        <div class="text-[10px] text-muted-foreground">/ month</div>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-foreground">
                                        {{ plan.post_limit_count ?? 'Unlimited' }}
                                        <div class="text-[10px] text-muted-foreground">posts / mo</div>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-foreground">
                                        {{ plan.max_social_accounts }} accounts
                                    </td>
                                    <td class="px-4 py-3 text-sm text-foreground">
                                        {{ plan.users_count ?? 0 }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-foreground font-semibold">
                                        ${{ plan.revenue ?? '0.00' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        <span
                                            :class="{
                                                'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400': plan.status,
                                                'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400': !plan.status,
                                            }"
                                            class="px-2 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider"
                                        >
                                            {{ plan.status ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <Link
                                                :href="route('admin.subscription-plans.edit', plan.id)"
                                                class="p-2 text-muted-foreground hover:text-primary hover:bg-primary/10 rounded-lg transition-colors"
                                            >
                                                <PencilIcon class="w-4 h-4" />
                                            </Link>
                                            <button
                                                @click="confirmDelete(plan)"
                                                class="p-2 text-muted-foreground hover:text-destructive hover:bg-destructive/10 rounded-lg transition-colors"
                                            >
                                                <TrashIcon class="w-4 h-4" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="subscriptionPlans.length === 0">
                                    <td colspan="8" class="px-4 py-8 text-center text-muted-foreground italic">
                                        No subscription plans found.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <DeleteConfirmationModal
            :show="showDeleteModal"
            :processing="processing"
            title="Delete Subscription Plan"
            message="Are you sure you want to delete this subscription plan? This will remove the plan from the system and may affect existing subscribers."
            @close="showDeleteModal = false"
            @confirm="deletePlan"
        />
    </AdminLayout>
</template>
