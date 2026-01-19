<script setup>
import { defineProps, defineEmits } from 'vue';

const props = defineProps({
    show: Boolean,
    categoryName: String,
    processing: Boolean
});

const emit = defineEmits(['close', 'confirm']);
</script>

<template>
    <div v-if="show">
        <!-- Backdrop -->
        <div class="fixed inset-0 z-50 bg-black/80 data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0" aria-hidden="true" @click="$emit('close')"></div>
        
        <!-- Modal Content -->
        <div role="dialog" 
             class="fixed left-[50%] top-[50%] z-50 grid w-full max-w-lg translate-x-[-50%] translate-y-[-50%] gap-4 border p-6 shadow-lg duration-200 sm:rounded-lg bg-popover border-border text-foreground animate-in fade-in zoom-in-95 slide-in-from-left-1/2 slide-in-from-top-[48%]" 
             tabindex="-1" 
             style="pointer-events: auto;">
            
            <div class="flex flex-col space-y-1.5 text-center sm:text-left">
                <h2 class="text-lg font-semibold leading-none tracking-tight flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-triangle-alert h-5 w-5 text-destructive" aria-hidden="true">
                        <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3"></path>
                        <path d="M12 9v4"></path>
                        <path d="M12 17h.01"></path>
                    </svg>
                    Delete Category
                </h2>
                <p class="text-sm text-muted-foreground">
                    Are you sure you want to delete the <span class="font-bold text-foreground">"{{ categoryName }}"</span> category? This will unassign all quotes currently linked to it.
                </p>
            </div>
            
            <div class="flex flex-col-reverse sm:flex-row sm:justify-end sm:space-x-2 gap-2 sm:gap-0 mt-4">
                <button @click="$emit('close')" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 hover-elevate active-elevate-2 border border-transparent min-h-9 px-4 py-2 hover:bg-accent hover:text-accent-foreground">
                    Cancel
                </button>
                <button @click="$emit('confirm')" :disabled="processing" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 hover-elevate active-elevate-2 bg-destructive text-destructive-foreground shadow-sm border-destructive-border min-h-9 px-4 py-2 font-bold">
                    Delete Category
                </button>
            </div>
            
            <button @click="$emit('close')" type="button" class="absolute right-4 top-4 rounded-sm opacity-70 ring-offset-background transition-opacity hover:opacity-100 focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:pointer-events-none data-[state=open]:bg-accent data-[state=open]:text-muted-foreground">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x h-4 w-4" aria-hidden="true">
                    <path d="M18 6 6 18"></path>
                    <path d="m6 6 12 12"></path>
                </svg>
                <span class="sr-only">Close</span>
            </button>
        </div>
    </div>
</template>
