<script setup>
import { ref, computed } from 'vue';
import {
    CheckCircleIcon,
    NoSymbolIcon,
    ShieldExclamationIcon,
    XMarkIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    show: Boolean,
    status: {
        type: String,
        required: true,
    },
    userName: {
        type: String,
        required: true,
    },
    currentStatus: {
        type: String,
        default: '',
    },
    processing: {
        type: Boolean,
        default: false,
    },
});

console.log(props.currentStatus)
const emit = defineEmits(['close', 'confirm']);

const reason = ref('');

const config = computed(() => {
    switch (props.status) {
        case 'active':
            return {
                title: `Activate ${props.userName}'s account?`,
                description: `This account is currently <strong>${capitalize(props.currentStatus || 'suspended')}</strong>. Activating it will restore full access immediately. This action is logged with your admin ID and the reason you provide below.`,
                afterLabel: 'After activation',
                afterValue: 'Active',
                afterClass: 'text-emerald-400',
                confirmText: 'Activate account',
                confirmClass: 'bg-emerald-600 hover:bg-emerald-500 text-white',
                icon: CheckCircleIcon,
                iconBg: 'bg-emerald-500/10 text-emerald-400 ring-emerald-500/20',
                reasonLabel: 'Reason for activation',
                reasonPlaceholder: 'e.g. Ban was applied in error — reviewed and cleared by support team',
            };
        case 'suspended':
            return {
                title: `Suspend ${props.userName}'s account?`,
                description: `This account is currently <strong>Active</strong>. Suspending it will immediately block login access. This action is logged with your admin ID and the reason you provide below.`,
                afterLabel: 'After suspension',
                afterValue: 'Suspended',
                afterClass: 'text-amber-400',
                confirmText: 'Suspend account',
                confirmClass: 'bg-amber-500 hover:bg-amber-400 text-white',
                icon: ShieldExclamationIcon,
                iconBg: 'bg-amber-500/10 text-amber-400 ring-amber-500/20',
                reasonLabel: 'Reason for suspension',
                reasonPlaceholder: 'e.g. Violated terms of service — repeated spam',
            };
        case 'banned':
            return {
                title: `Ban ${props.userName}'s account?`,
                description: `This is a severe and permanent action. The account will be immediately blocked and flagged. This action is logged with your admin ID and the reason you provide below.`,
                afterLabel: 'After ban',
                afterValue: 'Banned',
                afterClass: 'text-rose-400',
                confirmText: 'Ban account',
                confirmClass: 'bg-rose-600 hover:bg-rose-500 text-white',
                icon: NoSymbolIcon,
                iconBg: 'bg-rose-500/10 text-rose-400 ring-rose-500/20',
                reasonLabel: 'Reason for ban',
                reasonPlaceholder: 'e.g. Fraudulent activity confirmed — legal hold applied',
            };
        default:
            return {
                title: `Update status for ${props.userName}?`,
                description: 'This will update the user account status.',
                afterLabel: 'New status',
                afterValue: props.status,
                afterClass: 'text-foreground',
                confirmText: 'Confirm',
                confirmClass: 'bg-primary text-primary-foreground',
                icon: CheckCircleIcon,
                iconBg: 'bg-primary/10 text-primary ring-primary/20',
                reasonLabel: 'Reason (internal)',
                reasonPlaceholder: 'Provide a reason...',
            };
    }
});

const capitalize = (s) => s ? s.charAt(0).toUpperCase() + s.slice(1) : '';

const handleConfirm = () => {
    emit('confirm', { reason: reason.value });
};
</script>

<template>
    <div v-if="show" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
        <!-- Backdrop -->
        <div
            class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity"
            @click="emit('close')"
        ></div>

        <!-- Modal -->
        <div class="relative w-full max-w-lg transform overflow-hidden rounded-2xl bg-card border border-border p-6 shadow-2xl transition-all animate-in zoom-in-95 duration-200">
            <!-- Close button -->
            <button
                @click="emit('close')"
                class="absolute right-4 top-4 text-muted-foreground hover:text-foreground transition-colors p-1 rounded-lg hover:bg-muted"
            >
                <XMarkIcon class="h-4 w-4" />
            </button>

            <div class="flex items-start gap-4">
                <!-- Icon -->
                <div :class="['flex h-12 w-12 shrink-0 items-center justify-center rounded-xl ring-1', config.iconBg]">
                    <component :is="config.icon" class="h-6 w-6" />
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-lg font-bold text-foreground">{{ config.title }}</h3>
                    <p class="mt-2 text-sm text-muted-foreground leading-relaxed" v-html="config.description"></p>
                </div>
            </div>

            <!-- Status rows -->
            <div class="mt-5 space-y-2">
                <div class="flex items-center justify-between rounded-xl bg-muted/30 border border-border px-4 py-3">
                    <span class="text-xs font-bold text-muted-foreground">Current status</span>
                    <span class="text-xs font-bold capitalize"
                        :class="{
                            'text-rose-400': currentStatus === 'banned',
                            'text-amber-400': currentStatus === 'suspended',
                            'text-emerald-400': currentStatus === 'active',
                            'text-muted-foreground': !currentStatus,
                        }"
                    >{{ capitalize(currentStatus || '—') }}</span>
                </div>
                <div class="flex items-center justify-between rounded-xl bg-muted/30 border border-border px-4 py-3">
                    <span class="text-xs font-bold text-muted-foreground">{{ config.afterLabel }}</span>
                    <span class="text-xs font-bold" :class="config.afterClass">{{ config.afterValue }}</span>
                </div>
            </div>

            <!-- Reason textarea -->
            <div class="mt-4">
                <label class="text-xs font-bold text-muted-foreground block mb-2">
                    {{ config.reasonLabel }} <span class="text-rose-400">*</span>
                </label>
                <textarea
                    v-model="reason"
                    rows="3"
                    :placeholder="config.reasonPlaceholder"
                    class="w-full rounded-xl border border-border bg-background px-4 py-3 text-sm text-foreground placeholder:text-muted-foreground/40 outline-none focus:border-primary/40 focus:ring-1 focus:ring-primary/20 transition-all resize-none"
                ></textarea>
            </div>

            <!-- Actions -->
            <div class="mt-5 flex items-center justify-end gap-3">
                <button
                    type="button"
                    @click="emit('close')"
                    class="rounded-xl px-4 py-2.5 text-xs font-bold text-foreground hover:bg-muted transition-all duration-200 border border-border"
                >
                    Cancel
                </button>
                <button
                    type="button"
                    :disabled="processing"
                    @click="handleConfirm"
                    :class="['rounded-xl px-6 py-2.5 text-xs font-bold shadow-sm transition-all duration-200 flex items-center justify-center min-w-[140px] gap-2 disabled:opacity-50 disabled:cursor-not-allowed', config.confirmClass]"
                >
                    <template v-if="processing">
                        <svg class="animate-spin h-3 w-3 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Processing...
                    </template>
                    <template v-else>
                        {{ config.confirmText }}
                    </template>
                </button>
            </div>
        </div>
    </div>
</template>
