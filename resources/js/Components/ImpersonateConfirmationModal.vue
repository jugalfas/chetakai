<script setup>
import { 
    UserIcon, 
    XMarkIcon 
} from '@heroicons/vue/24/outline';

const props = defineProps({
    show: Boolean,
    processing: Boolean,
    userName: String,
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
                class="absolute right-4 top-4 text-muted-foreground hover:text-foreground transition-colors p-1"
            >
                <XMarkIcon class="h-5 w-5" />
            </button>

            <div class="flex items-start gap-4">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-indigo-500/10 text-indigo-500 ring-1 ring-indigo-500/20">
                    <UserIcon class="h-6 w-6" />
                </div>
                <div class="flex-1 mt-0">
                    <h3 class="text-xl font-bold text-foreground">Login as {{ userName }}?</h3>
                    <p class="mt-3 text-sm text-muted-foreground leading-relaxed">
                        You will be redirected to the user panel and logged in as this user. All actions you take will be performed as them. A session token valid for 15 minutes will be created.
                    </p>
                </div>
            </div>

            <div class="mt-8 flex items-center justify-end gap-3">
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
                    class="rounded-xl px-6 py-2.5 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm transition-all duration-200 flex items-center justify-center min-w-[140px] disabled:opacity-50 disabled:cursor-not-allowed gap-2"
                >
                    <template v-if="processing">
                        <svg class="animate-spin h-3 w-3 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Redirecting...
                    </template>
                    <template v-else>
                        Continue as this user
                    </template>
                </button>
            </div>
        </div>
    </div>
</template>
