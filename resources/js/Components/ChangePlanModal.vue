<script setup>
import { ref, computed, watch } from 'vue';
import {
    XMarkIcon,
    ArrowRightIcon,
    InformationCircleIcon
} from '@heroicons/vue/24/outline';
import { CheckIcon } from '@heroicons/vue/20/solid';
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
import { ChevronDown, Check } from "lucide-vue-next";

const props = defineProps({
    show: Boolean,
    user: Object,
    currentPlan: Object,
    allPlans: Array,
    stats: Object,
    processing: Boolean,
});

const emit = defineEmits(['close', 'confirm']);

const selectedPlanId = ref(props.currentPlan?.id || null);
const billingCycle = ref('monthly');
const effectiveDate = ref('immediate');
const reason = ref('');
const notifyUser = ref(true);
const resetUsage = ref(false);

const selectedPlan = computed(() => {
    return props.allPlans.find(p => p.id === selectedPlanId.value);
});

const isDowngrade = computed(() => {
    if (!props.currentPlan || !selectedPlan.value) return false;
    
    // Logic for downgrade: Pro -> Free, Biz -> Pro/Free etc.
    const weights = { 'free': 0, 'pro': 1, 'paid': 1, 'biz': 2, 'custom': 3 };
    const currentWeight = weights[props.currentPlan.type] || 0;
    const selectedWeight = weights[selectedPlan.value.type] || 0;
    
    return selectedWeight < currentWeight;
});

const confirm = () => {
    if (!selectedPlanId.value || !reason.value) return;
    
    emit('confirm', {
        plan_id: selectedPlanId.value,
        billing_cycle: billingCycle.value,
        effective_date: effectiveDate.value,
        reason: reason.value,
        notify_user: notifyUser.value,
        reset_usage: resetUsage.value,
    });
};

watch(() => props.show, (newVal) => {
    if (newVal) {
        selectedPlanId.value = props.currentPlan?.id || null;
        reason.value = '';
        resetUsage.value = false;
    }
});

</script>

<template>
    <div v-if="show" class="fixed inset-0 z-[100] flex items-center justify-center p-4 overflow-y-auto">
        <!-- Backdrop -->
        <div 
            class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity"
            @click="emit('close')"
        ></div>
        
        <!-- Modal -->
        <div class="relative w-full max-w-2xl transform overflow-hidden rounded-2xl bg-card border border-border shadow-2xl transition-all animate-in zoom-in-95 duration-200 flex flex-col">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-border flex items-center justify-between shrink-0">
                <div>
                    <h3 class="text-lg font-bold text-foreground">
                        Change subscription plan
                    </h3>
                    <p class="text-xs text-muted-foreground mt-1">
                        {{ user.first_name }} {{ user.last_name }} • {{ user.email }} • USR-{{ String(user.id).padStart(5, '0') }}
                    </p>
                </div>
                <button
                    type="button"
                    class="rounded-lg p-2 text-muted-foreground hover:bg-muted transition-colors"
                    @click="emit('close')"
                >
                    <XMarkIcon class="h-5 w-5" aria-hidden="true" />
                </button>
            </div>

            <div class="overflow-y-auto max-h-[70vh]">
                <!-- Comparison -->
                <div class="p-6 bg-muted/20 border-b border-border">
                    <div class="flex items-center justify-between gap-4">
                        <div class="flex-1">
                            <p class="text-[10px] font-bold text-muted-foreground uppercase tracking-widest mb-2">Current plan</p>
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-bold text-foreground">{{ currentPlan?.name || 'No Plan' }}</span>
                                <span v-if="currentPlan" class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider bg-muted text-muted-foreground border border-border">
                                    {{ currentPlan.type }}
                                </span>
                            </div>
                            <p class="text-[11px] text-muted-foreground mt-1">
                                ${{ currentPlan?.monthly_price || 0 }} • {{ currentPlan?.post_limit_count || '∞' }} posts lifetime • {{ currentPlan?.max_social_accounts || '∞' }} account
                            </p>
                        </div>
                        
                        <ArrowRightIcon class="w-5 h-5 text-muted-foreground/30" />

                        <div class="flex-1 text-right">
                            <p class="text-[10px] font-bold text-muted-foreground uppercase tracking-widest mb-2">Changing to</p>
                            <div v-if="selectedPlan" class="flex items-center justify-end gap-2 text-primary">
                                <span class="text-sm font-bold">{{ selectedPlan.name }}</span>
                            </div>
                            <div v-else>
                                <span class="text-sm font-bold text-muted-foreground italic">Not selected</span>
                            </div>
                            <p v-if="selectedPlan" class="text-[11px] text-muted-foreground mt-1">
                                ${{ selectedPlan.monthly_price }}/mo • {{ selectedPlan.post_limit_count || '∞' }} posts/month • {{ selectedPlan.max_social_accounts }} accounts
                            </p>
                            <p v-else class="text-[11px] text-muted-foreground mt-1">—</p>
                        </div>
                    </div>
                </div>

                <!-- Plan Selection Grid -->
                <div class="p-6 space-y-6">
                    <div>
                        <p class="text-xs font-bold text-muted-foreground mb-4">Select new plan</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div 
                                v-for="plan in allPlans" 
                                :key="plan.id"
                                @click="selectedPlanId = plan.id"
                                class="relative group cursor-pointer rounded-xl border p-4 transition-all"
                                :class="[
                                    selectedPlanId === plan.id 
                                        ? 'bg-primary/5 border-primary shadow-[0_0_0_1px_rgba(var(--primary),0.3)] ring-1 ring-primary/20' 
                                        : 'bg-card border-border hover:bg-muted/30'
                                ]"
                            >
                                <div class="flex items-start justify-between">
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <p class="text-sm font-bold text-foreground">{{ plan.name }}</p>
                                            <span v-if="plan.id === currentPlan?.id" class="px-1.5 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider bg-muted text-muted-foreground">Current</span>
                                            <span v-else-if="plan.badge_label" class="px-1.5 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider bg-indigo-500/10 text-indigo-400 ring-1 ring-indigo-500/20">{{ plan.badge_label }}</span>
                                        </div>
                                        <p class="text-xs font-medium text-muted-foreground mt-1">
                                            {{ plan.monthly_price > 0 ? '$' + plan.monthly_price : '$0' }}
                                        </p>
                                        <div class="mt-3 space-y-1 text-[10px] text-muted-foreground font-medium">
                                            <p>{{ plan.post_limit_count || 'Unlimited' }} posts / month</p>
                                            <p>{{ plan.max_social_accounts }} social accounts</p>
                                            <p>{{ plan.type === 'free' ? 'Captions only' : 'All content types' }}</p>
                                        </div>
                                    </div>
                                    <div v-if="selectedPlanId === plan.id" class="bg-primary text-primary-foreground rounded-full p-1">
                                        <CheckIcon class="w-3 h-3" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Messages/Warnings -->
                    <div v-if="isDowngrade" class="p-4 rounded-xl bg-rose-500/5 border border-rose-500/20 flex gap-3">
                        <InformationCircleIcon class="w-5 h-5 text-rose-500 shrink-0" />
                        <div>
                            <p class="text-xs font-bold text-rose-600">Downgrade Warning</p>
                            <p class="text-[11px] text-rose-600/80 mt-1">
                                This user currently has {{ user.social_accounts?.length || 0 }} social accounts and {{ stats.posts_this_month }} posts this month. 
                                Downgrading to <b>{{ selectedPlan.name }}</b> may restrict their usage or break existing automations.
                            </p>

                            <!-- Reset Usage Toggle -->
                            <div class="flex items-center gap-3 mt-4 px-4 py-3 bg-rose-500/10 border border-rose-500/10 rounded-xl cursor-pointer select-none" @click="resetUsage = !resetUsage">
                                <div 
                                    class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
                                    :class="resetUsage ? 'bg-rose-500' : 'bg-muted'"
                                >
                                    <span
                                        aria-hidden="true"
                                        :class="resetUsage ? 'translate-x-4' : 'translate-x-0'"
                                        class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                    />
                                </div>
                                <div>
                                    <span class="text-xs font-bold text-rose-600 block">Reset current month's usage stats</span>
                                    <span class="text-[10px] text-rose-600/70">Wipe post counts so they start from 0 on the new plan.</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-if="selectedPlan && !isDowngrade && selectedPlan.id !== currentPlan?.id" class="p-4 rounded-xl bg-emerald-500/5 border border-emerald-500/20 flex gap-3">
                        <InformationCircleIcon class="w-5 h-5 text-emerald-500 shrink-0" />
                        <div>
                            <p class="text-xs font-bold text-emerald-600">Upgrade takes effect immediately</p>
                            <p class="text-[11px] text-emerald-600/80 mt-1">
                                The user will get access to {{ selectedPlan.name }} features instantly. Post usage counter for this month is preserved.
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label class="text-[10px] font-bold text-muted-foreground uppercase tracking-widest mb-2 block">Billing cycle</label>
                            <SelectRoot v-model="billingCycle">
                                <SelectTrigger
                                    class="flex h-11 w-full items-center justify-between rounded-xl border px-4 py-2 text-sm shadow-sm transition-colors focus:outline-none focus:border-primary focus:ring-0 bg-background border-border text-foreground capitalize"
                                >
                                    <SelectValue placeholder="Select cycle" />
                                    <ChevronDown class="h-4 w-4 opacity-50" />
                                </SelectTrigger>

                                <SelectPortal>
                                    <SelectContent
                                        class="z-[110] w-[--radix-select-trigger-width] overflow-hidden rounded-xl border bg-popover text-popover-foreground shadow-md data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0 data-[state=closed]:zoom-out-95 data-[state=open]:zoom-in-95 data-[side=bottom]:slide-in-from-top-2 data-[side=left]:slide-in-from-right-2 data-[side=right]:slide-in-from-left-2 data-[side=top]:slide-in-from-bottom-2 bg-[#041C32] border-sidebar-border"
                                        position="popper"
                                        :side-offset="5"
                                    >
                                        <SelectViewport class="p-1">
                                            <SelectItem
                                                v-for="option in [
                                                    { value: 'monthly', label: 'Monthly' },
                                                    { value: 'annual', label: 'Annual' },
                                                    { value: 'manual', label: 'Manual (no Stripe charge)' }
                                                ]"
                                                :key="option.value"
                                                :value="option.value"
                                                class="relative flex w-full cursor-default select-none items-center rounded-lg py-2 pl-3 pr-9 text-xs outline-none focus:bg-accent focus:text-accent-foreground data-[state=checked]:bg-[#092644] data-[state=checked]:text-white data-[disabled]:pointer-events-none data-[disabled]:opacity-50 hover:bg-muted/20 transition-colors capitalize"
                                            >
                                                <SelectItemText>{{ option.label }}</SelectItemText>
                                                <SelectItemIndicator class="absolute right-3 flex h-4 w-4 items-center justify-center">
                                                    <Check class="h-4 w-4 stroke-[3px]" />
                                                </SelectItemIndicator>
                                            </SelectItem>
                                        </SelectViewport>
                                    </SelectContent>
                                </SelectPortal>
                            </SelectRoot>
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-muted-foreground uppercase tracking-widest mb-2 block">Effective date</label>
                            <SelectRoot v-model="effectiveDate">
                                <SelectTrigger
                                    class="flex h-11 w-full items-center justify-between rounded-xl border px-4 py-2 text-sm shadow-sm transition-colors focus:outline-none focus:border-primary focus:ring-0 bg-background border-border text-foreground capitalize"
                                >
                                    <SelectValue placeholder="Select date" />
                                    <ChevronDown class="h-4 w-4 opacity-50" />
                                </SelectTrigger>

                                <SelectPortal>
                                    <SelectContent
                                        class="z-[110] w-[--radix-select-trigger-width] overflow-hidden rounded-xl border bg-popover text-popover-foreground shadow-md data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0 data-[state=closed]:zoom-out-95 data-[state=open]:zoom-in-95 data-[side=bottom]:slide-in-from-top-2 data-[side=left]:slide-in-from-right-2 data-[side=right]:slide-in-from-left-2 data-[side=top]:slide-in-from-bottom-2 bg-[#041C32] border-sidebar-border"
                                        position="popper"
                                        :side-offset="5"
                                    >
                                        <SelectViewport class="p-1">
                                            <SelectItem
                                                v-for="option in [
                                                    { value: 'immediate', label: 'Today (immediate)' },
                                                    { value: 'period_end', label: 'At end of current period' }
                                                ]"
                                                :key="option.value"
                                                :value="option.value"
                                                class="relative flex w-full cursor-default select-none items-center rounded-lg py-2 pl-3 pr-9 text-xs outline-none focus:bg-accent focus:text-accent-foreground data-[state=checked]:bg-[#092644] data-[state=checked]:text-white data-[disabled]:pointer-events-none data-[disabled]:opacity-50 hover:bg-muted/20 transition-colors capitalize"
                                            >
                                                <SelectItemText>{{ option.label }}</SelectItemText>
                                                <SelectItemIndicator class="absolute right-3 flex h-4 w-4 items-center justify-center">
                                                    <Check class="h-4 w-4 stroke-[3px]" />
                                                </SelectItemIndicator>
                                            </SelectItem>
                                        </SelectViewport>
                                    </SelectContent>
                                </SelectPortal>
                            </SelectRoot>
                        </div>
                    </div>

                    <div>
                        <label class="text-[10px] font-bold text-muted-foreground uppercase tracking-widest mb-2 block">Reason for change *</label>
                        <textarea 
                            v-model="reason"
                            placeholder="e.g. Upgraded as beta tester reward / Admin correction" 
                            class="w-full bg-card border border-border rounded-xl p-3 text-sm text-foreground placeholder:text-muted-foreground/50 focus:ring-1 focus:ring-primary outline-none transition-all min-h-[100px] resize-none"
                        ></textarea>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="px-6 py-4 bg-muted/20 border-t border-border flex flex-col sm:flex-row items-center justify-between gap-4 shrink-0">
                <div class="flex items-center gap-3 cursor-pointer select-none" @click="notifyUser = !notifyUser">
                    <div 
                        class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
                        :class="notifyUser ? 'bg-primary' : 'bg-muted'"
                    >
                        <span
                            aria-hidden="true"
                            :class="notifyUser ? 'translate-x-4' : 'translate-x-0'"
                            class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                        />
                    </div>
                    <span class="text-sm font-medium text-foreground">Notify user by email</span>
                </div>
                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <button
                        type="button"
                        class="flex-1 sm:flex-none px-6 py-2.5 text-sm font-bold text-foreground hover:bg-muted border border-border rounded-xl transition-colors"
                        @click="emit('close')"
                    >
                        Cancel
                    </button>
                    <button
                        type="button"
                        :disabled="processing || !selectedPlanId || !reason"
                        class="flex-1 sm:flex-none px-8 py-2.5 bg-primary text-primary-foreground text-sm font-bold rounded-xl hover:bg-primary/90 transition-all disabled:opacity-50 disabled:grayscale flex items-center justify-center gap-2"
                        @click="confirm"
                    >
                        <template v-if="processing">
                            <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Processing
                        </template>
                        <template v-else>
                            Confirm change
                        </template>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
