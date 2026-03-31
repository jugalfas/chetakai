<script setup>
import { UserIcon, XMarkIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    show: Boolean,
    processing: Boolean,
    userName: String,
    email: String,
});

const emit = defineEmits(['close', 'confirm']);
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
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-primary/10 text-primary ring-1 ring-primary/20">
                    <UserIcon class="h-6 w-6" />
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-lg font-bold text-foreground">Send password reset to {{ userName }}?</h3>
                    <p class="mt-2 text-sm text-muted-foreground leading-relaxed">
                        A one-time OTP will be sent to
                        <span class="text-foreground font-bold">{{ email }}</span>.
                        The OTP expires in <span class="text-foreground font-bold">10 minutes</span>.
                        The user's current password will not change until they complete the reset flow.
                    </p>
                </div>
            </div>

            <!-- Info rows -->
            <div class="mt-5 space-y-2">
                <div class="flex items-center justify-between rounded-xl bg-muted/30 border border-border px-4 py-3">
                    <span class="text-xs font-bold text-muted-foreground">Sending to</span>
                    <span class="text-xs font-bold text-foreground font-mono">{{ email }}</span>
                </div>
                <div class="flex items-center justify-between rounded-xl bg-muted/30 border border-border px-4 py-3">
                    <span class="text-xs font-bold text-muted-foreground">OTP expiry</span>
                    <span class="text-xs font-bold text-foreground">10 minutes</span>
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-6 flex items-center justify-end gap-3">
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
                    @click="emit('confirm')"
                    class="rounded-xl px-6 py-2.5 text-xs font-bold text-white bg-primary hover:bg-primary/90 shadow-sm transition-all duration-200 flex items-center justify-center min-w-[150px] disabled:opacity-50 disabled:cursor-not-allowed gap-2"
                >
                    <template v-if="processing">
                        <svg class="animate-spin h-3 w-3 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Sending...
                    </template>
                    <template v-else>
                        Send reset email
                    </template>
                </button>
            </div>
        </div>
    </div>
</template>
