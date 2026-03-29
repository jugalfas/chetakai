<script setup>
import { computed } from 'vue';
import { 
    ExclamationTriangleIcon, 
    CheckCircleIcon, 
    NoSymbolIcon,
    ShieldExclamationIcon,
    XMarkIcon
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
    processing: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['close', 'confirm']);

const config = computed(() => {
    switch (props.status) {
        case 'active':
            return {
                title: 'Activate User',
                message: `Are you sure you want to activate ${props.userName}'s account? They will regain full access to the platform.`,
                confirmText: 'Activate Account',
                confirmClass: 'bg-emerald-600 hover:bg-emerald-700 text-white',
                icon: CheckCircleIcon,
                iconClass: 'text-emerald-500 bg-emerald-100 dark:bg-emerald-900/30'
            };
        case 'suspended':
            return {
                title: 'Suspend User',
                message: `Are you sure you want to suspend ${props.userName}'s account? They will be unable to log in until the account is reinstated.`,
                confirmText: 'Suspend Account',
                confirmClass: 'bg-amber-600 hover:bg-amber-700 text-white',
                icon: ShieldExclamationIcon,
                iconClass: 'text-amber-500 bg-amber-100 dark:bg-amber-900/30'
            };
        case 'banned':
            return {
                title: 'Ban User',
                message: `Are you sure you want to permanently ban ${props.userName}? This is a severe action and should only be used for major violations.`,
                confirmText: 'Ban User',
                confirmClass: 'bg-rose-600 hover:bg-rose-700 text-white',
                icon: NoSymbolIcon,
                iconClass: 'text-rose-500 bg-rose-100 dark:bg-rose-900/30'
            };
        default:
            return {
                title: 'Update Status',
                message: `Are you sure you want to update the status for ${props.userName}?`,
                confirmText: 'Confirm',
                confirmClass: 'bg-primary text-primary-foreground',
                icon: ExclamationTriangleIcon,
                iconClass: 'text-amber-500 bg-amber-100 dark:bg-amber-900/30'
            };
    }
});
</script>

<template>
    <div v-if="show" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
        <!-- Backdrop -->
        <div 
            class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity"
            @click="emit('close')"
        ></div>
        
        <!-- Modal -->
        <div class="relative w-full max-w-md transform overflow-hidden rounded-2xl bg-card border border-border p-6 shadow-2xl transition-all animate-in zoom-in-95 duration-200">
            <!-- Close button -->
            <button 
                @click="emit('close')"
                class="absolute right-4 top-4 text-muted-foreground hover:text-foreground transition-colors p-1"
            >
                <XMarkIcon class="h-5 w-5" />
            </button>

            <div class="flex items-start gap-4">
                <div :class="['flex-shrink-0 rounded-full p-3', config.iconClass]">
                    <component :is="config.icon" class="h-6 w-6" aria-hidden="true" />
                </div>
                
                <div class="flex-1">
                    <h3 class="text-xl font-bold text-foreground">{{ config.title }}</h3>
                    <p class="mt-2 text-sm text-muted-foreground leading-relaxed">
                        {{ config.message }}
                    </p>
                </div>
            </div>

            <div class="mt-8 flex items-center justify-end gap-3">
                <button
                    type="button"
                    @click="emit('close')"
                    class="rounded-xl px-4 py-2.5 text-sm font-semibold text-foreground hover:bg-muted transition-all duration-200 border border-border"
                >
                    Cancel
                </button>
                <button
                    type="button"
                    :disabled="processing"
                    @click="emit('confirm')"
                    :class="['rounded-xl px-6 py-2.5 text-sm font-bold shadow-sm transition-all duration-200 flex items-center justify-center min-w-[120px]', config.confirmClass, processing ? 'opacity-50 cursor-not-allowed' : '']"
                >
                    <template v-if="processing">
                        <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
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
