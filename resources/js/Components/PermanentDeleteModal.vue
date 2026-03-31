<script setup>
import { ref, computed, watch } from 'vue';
import { TrashIcon, XMarkIcon, ExclamationTriangleIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    show: Boolean,
    processing: Boolean,
    userName: String,
    userEmail: String,
});

const emit = defineEmits(['close', 'confirm']);

// Step: 1 = warning overview, 2 = email confirmation
const step = ref(1);
const emailInput = ref('');

const emailMatches = computed(() => emailInput.value.trim() === props.userEmail);

// Reset state whenever modal opens/closes
watch(() => props.show, (val) => {
    if (!val) {
        setTimeout(() => {
            step.value = 1;
            emailInput.value = '';
        }, 300); // wait for close animation
    }
});

const handleClose = () => {
    emit('close');
};

const goToStep2 = () => {
    step.value = 2;
};

const handleConfirm = () => {
    if (!emailMatches.value) return;
    emit('confirm');
};
</script>

<template>
    <Transition
        enter-active-class="transition-opacity duration-200"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition-opacity duration-200"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div v-if="show" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
            <!-- Backdrop -->
            <div
                class="fixed inset-0 bg-black/70 backdrop-blur-sm"
                @click="handleClose"
            ></div>

            <!-- Modal -->
            <Transition
                enter-active-class="transition-all duration-200 ease-out"
                enter-from-class="opacity-0 scale-95 translate-y-2"
                enter-to-class="opacity-100 scale-100 translate-y-0"
                leave-active-class="transition-all duration-150 ease-in"
                leave-from-class="opacity-100 scale-100"
                leave-to-class="opacity-0 scale-95"
                appear
            >
                <div class="relative w-full max-w-lg bg-card border border-border rounded-2xl shadow-2xl overflow-hidden">
                    <!-- Danger stripe at top -->
                    <div class="h-1 w-full bg-gradient-to-r from-rose-600 via-rose-500 to-rose-400"></div>

                    <!-- Close button -->
                    <button
                        @click="handleClose"
                        class="absolute right-4 top-4 text-muted-foreground hover:text-foreground transition-colors p-1 rounded-lg hover:bg-muted z-10"
                    >
                        <XMarkIcon class="h-4 w-4" />
                    </button>

                    <!-- ── STEP 1: Warning overview ── -->
                    <div v-if="step === 1" class="p-6">
                        <div class="flex items-start gap-4">
                            <!-- Icon -->
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-rose-500/10 text-rose-500 ring-1 ring-rose-500/20">
                                <TrashIcon class="h-6 w-6" />
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-lg font-bold text-foreground leading-snug">Permanently delete this account?</h3>
                                <p class="mt-2 text-sm text-muted-foreground leading-relaxed">
                                    This will delete <span class="text-foreground font-semibold">{{ userName }}</span> and all
                                    their data including content, connected accounts, and billing history.
                                    <span class="text-rose-400 font-semibold">This cannot be undone.</span>
                                </p>
                            </div>
                        </div>

                        <!-- What gets deleted list -->
                        <div class="mt-5 rounded-xl border border-rose-500/15 bg-rose-500/5 p-4">
                            <p class="text-[10px] font-bold text-rose-400 uppercase tracking-widest mb-3">What gets deleted</p>
                            <ul class="space-y-2">
                                <li v-for="item in [
                                    'User account row',
                                    'All subscriptions & billing history',
                                    'Post usage & quota logs',
                                    'Connected social accounts',
                                    'All generated content',
                                    'Activity logs',
                                ]" :key="item" class="flex items-center gap-2.5 text-xs text-muted-foreground">
                                    <span class="flex h-4 w-4 shrink-0 items-center justify-center rounded-full bg-rose-500/15 text-rose-500">
                                        <svg class="h-2.5 w-2.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                                        </svg>
                                    </span>
                                    {{ item }}
                                </li>
                            </ul>
                        </div>

                        <!-- Actions -->
                        <div class="mt-6 flex items-center justify-end gap-3">
                            <button
                                type="button"
                                @click="handleClose"
                                class="rounded-xl px-4 py-2.5 text-xs font-bold text-foreground hover:bg-muted transition-all duration-200 border border-border"
                            >
                                Cancel
                            </button>
                            <button
                                type="button"
                                @click="goToStep2"
                                class="rounded-xl px-5 py-2.5 text-xs font-bold text-white bg-rose-600 hover:bg-rose-500 shadow-sm shadow-rose-500/20 transition-all duration-200 flex items-center gap-2"
                            >
                                <ExclamationTriangleIcon class="h-3.5 w-3.5" />
                                Yes, continue
                            </button>
                        </div>
                    </div>

                    <!-- ── STEP 2: Email confirmation ── -->
                    <div v-else-if="step === 2" class="p-6">
                        <!-- Step indicator -->
                        <div class="flex items-center gap-2 mb-5">
                            <span class="flex h-5 w-5 items-center justify-center rounded-full bg-rose-500/10 ring-1 ring-rose-500/20 text-rose-500 text-[10px] font-bold">2</span>
                            <span class="text-[10px] font-bold text-muted-foreground uppercase tracking-widest">Final confirmation</span>
                        </div>

                        <h3 class="text-lg font-bold text-foreground">Type the user's email to confirm</h3>
                        <p class="mt-2 text-sm text-muted-foreground leading-relaxed mb-5">
                            To permanently delete <span class="text-foreground font-semibold">{{ userName }}</span>'s account
                            and all associated data, type their email address below.
                        </p>

                        <!-- Email input -->
                        <div class="space-y-2">
                            <label class="text-[10px] font-bold text-muted-foreground uppercase tracking-widest">
                                Confirm email address
                            </label>
                            <input
                                v-model="emailInput"
                                type="email"
                                :placeholder="userEmail"
                                autocomplete="off"
                                spellcheck="false"
                                class="w-full rounded-xl border px-4 py-3 text-sm font-mono bg-background text-foreground placeholder:text-muted-foreground/40 outline-none transition-all duration-200"
                                :class="emailInput.length > 0
                                    ? emailMatches
                                        ? 'border-emerald-500/50 ring-1 ring-emerald-500/20'
                                        : 'border-rose-500/40 ring-1 ring-rose-500/10'
                                    : 'border-border focus:border-rose-500/40 focus:ring-1 focus:ring-rose-500/10'"
                                @keyup.enter="emailMatches && !processing && handleConfirm()"
                            />
                            <!-- Inline match status -->
                            <p v-if="emailInput.length > 0" class="text-[11px] font-medium transition-colors"
                               :class="emailMatches ? 'text-emerald-500' : 'text-rose-400'">
                                {{ emailMatches ? '✓ Email matches' : '✗ Email does not match' }}
                            </p>
                        </div>

                        <!-- Actions -->
                        <div class="mt-6 flex items-center justify-between">
                            <button
                                type="button"
                                @click="step = 1"
                                class="rounded-xl px-4 py-2.5 text-xs font-bold text-muted-foreground hover:text-foreground hover:bg-muted transition-all duration-200"
                            >
                                ← Back
                            </button>

                            <div class="flex items-center gap-3">
                                <button
                                    type="button"
                                    @click="handleClose"
                                    class="rounded-xl px-4 py-2.5 text-xs font-bold text-foreground hover:bg-muted transition-all duration-200 border border-border"
                                >
                                    Cancel
                                </button>
                                <button
                                    type="button"
                                    :disabled="!emailMatches || processing"
                                    @click="handleConfirm"
                                    class="rounded-xl px-6 py-2.5 text-xs font-bold text-white shadow-sm transition-all duration-200 flex items-center justify-center min-w-[160px] gap-2"
                                    :class="emailMatches && !processing
                                        ? 'bg-rose-600 hover:bg-rose-500 shadow-rose-500/20 cursor-pointer'
                                        : 'bg-rose-600/40 cursor-not-allowed opacity-50'"
                                >
                                    <template v-if="processing">
                                        <svg class="animate-spin h-3 w-3 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Deleting...
                                    </template>
                                    <template v-else>
                                        <TrashIcon class="h-3.5 w-3.5" />
                                        Delete permanently
                                    </template>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </Transition>
        </div>
    </Transition>
</template>
