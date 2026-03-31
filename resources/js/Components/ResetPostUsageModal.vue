<script setup>
import { ref, computed } from 'vue';
import { ArrowPathIcon, XMarkIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    show: Boolean,
    processing: Boolean,
    userName: String,
    usedPosts: {
        type: Number,
        default: 0,
    },
    postLimit: {
        type: Number,
        default: 0,
    },
    planName: {
        type: String,
        default: '',
    },
});

const emit = defineEmits(['close', 'confirm']);

const reason = ref('');

const planLabel = computed(() => {
    if (!props.postLimit) return '∞';
    return props.postLimit + ' posts';
});

const planDetail = computed(() => props.planName ? ` (${props.planName})` : '');

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
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-amber-500/10 text-amber-400 ring-1 ring-amber-500/20">
                    <ArrowPathIcon class="h-6 w-6" />
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-lg font-bold text-foreground">Reset post usage for {{ userName }}?</h3>
                    <p class="mt-2 text-sm text-muted-foreground leading-relaxed">
                        This will reset their used post count to
                        <span class="text-foreground font-bold">0</span>
                        for the current billing cycle. Their full plan quota will be immediately restored.
                        This action is logged and cannot be undone.
                    </p>
                </div>
            </div>

            <!-- Info rows -->
            <div class="mt-5 space-y-2">
                <div class="flex items-center justify-between rounded-xl bg-muted/30 border border-border px-4 py-3">
                    <span class="text-xs font-bold text-muted-foreground">Current usage</span>
                    <span class="text-xs font-bold text-foreground">
                        {{ usedPosts }} of {{ postLimit > 0 ? postLimit : '∞' }} posts used
                    </span>
                </div>
                <div class="flex items-center justify-between rounded-xl bg-muted/30 border border-border px-4 py-3">
                    <span class="text-xs font-bold text-muted-foreground">Plan limit</span>
                    <span class="text-xs font-bold text-foreground">{{ planLabel }}{{ planDetail }}</span>
                </div>
                <div class="flex items-center justify-between rounded-xl bg-muted/30 border border-border px-4 py-3">
                    <span class="text-xs font-bold text-muted-foreground">After reset</span>
                    <span class="text-xs font-bold text-emerald-400">
                        0 of {{ postLimit > 0 ? postLimit : '∞' }} posts used
                    </span>
                </div>
            </div>

            <!-- Reason input -->
            <div class="mt-4">
                <label class="text-xs font-bold text-muted-foreground block mb-2">Reason for reset (internal)</label>
                <textarea
                    v-model="reason"
                    rows="2"
                    placeholder="e.g. Incorrectly counted due to failed publish"
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
                    class="rounded-xl px-6 py-2.5 text-xs font-bold text-white bg-amber-500 hover:bg-amber-400 shadow-sm transition-all duration-200 flex items-center justify-center min-w-[130px] disabled:opacity-50 disabled:cursor-not-allowed gap-2"
                >
                    <template v-if="processing">
                        <svg class="animate-spin h-3 w-3 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Resetting...
                    </template>
                    <template v-else>
                        Reset usage
                    </template>
                </button>
            </div>
        </div>
    </div>
</template>
