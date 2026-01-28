<script setup>
import { router } from '@inertiajs/vue3'
import { ref, watch, computed } from 'vue'
import { X } from 'lucide-vue-next'
import DateTimePicker from './DateTimePicker.vue'
import { toast } from 'vue-sonner'

const props = defineProps({
    show: Boolean,
    quote: Object,
})

const emit = defineEmits(['close'])

// Initialize with today's date and 8 PM
const scheduledAt = ref(new Date())
scheduledAt.value.setHours(20, 0, 0, 0)

const submit = () => {
    // Format to YYYY-MM-DD HH:mm:ss for backend
    const date = scheduledAt.value
    const year = date.getFullYear()
    const month = String(date.getMonth() + 1).padStart(2, '0')
    const day = String(date.getDate()).padStart(2, '0')
    const hours = String(date.getHours()).padStart(2, '0')
    const minutes = String(date.getMinutes()).padStart(2, '0')
    const seconds = '00'

    const formattedScheduledAt = `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`

    router.post(
        route('quotes.schedule', props.quote.id),
        { scheduled_at: formattedScheduledAt },
        {
            preserveScroll: true,
            onSuccess: () => {
                toast.success('Post scheduled successfully!')
                emit('close')
            },
            onError: (errors) => {
                const errorMessage = errors.error || 'Failed to schedule post. Please try again.'
                toast.error(errorMessage)
            },
        }
    )
}

// Reset form when modal closes
watch(() => props.show, (visible) => {
    if (!visible) {
        const defaultDate = new Date()
        defaultDate.setHours(20, 0, 0, 0)
        scheduledAt.value = defaultDate
    }
})
</script>

<template>
    <div v-if="show">
        <!-- Backdrop -->
        <div class="fixed inset-0 z-50 bg-black/80 data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0"
            aria-hidden="true" @click="$emit('close')"></div>

        <!-- Modal Content -->
        <div role="dialog"
            class="fixed left-[50%] top-[50%] z-50 grid w-full max-w-lg translate-x-[-50%] translate-y-[-50%] gap-4 border p-6 shadow-lg duration-200 data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0 data-[state=closed]:zoom-out-95 data-[state=open]:zoom-in-95 data-[state=closed]:slide-out-to-left-1/2 data-[state=closed]:slide-out-to-top-[48%] data-[state=open]:slide-in-from-left-1/2 data-[state=open]:slide-in-from-top-[48%] sm:rounded-lg bg-[#041C32] border-white/10 text-white sm:max-w-[440px]"
            tabindex="-1" style="pointer-events: auto;">
            <!-- Close button -->
            <button @click="emit('close')"
                class="absolute right-4 top-4 rounded-sm opacity-70 ring-offset-background transition-opacity hover:opacity-100 focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:pointer-events-none data-[state=open]:bg-accent data-[state=open]:text-muted-foreground">
                <X class="w-4 h-4" />
            </button>

            <!-- Header -->
            <div class="flex flex-col space-y-1.5 text-center sm:text-left">
                <h2 class="text-xl font-bold leading-none tracking-tight">Schedule Post</h2>
                <p class="text-sm text-white/60">Pick a date and time to automatically post this quote on Instagram.</p>
            </div>

            <form @submit.prevent="submit" class="space-y-6 mt-4">
                <div class="flex flex-col gap-6">
                    <div class="grid gap-3">
                        <label class="text-sm font-bold text-white/90">Schedule Publication</label>
                        <DateTimePicker v-model="scheduledAt" :min-date="new Date()" />
                    </div>
                </div>

                <!-- ACTIONS -->
                <div class="flex flex-col-reverse sm:flex-row sm:justify-end sm:space-x-2 pt-4">
                    <button type="button" @click="emit('close')"
                        class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring border border-transparent min-h-9 px-4 py-2 hover:bg-white/5">
                        Cancel
                    </button>
                    <button type="submit"
                        class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 border border-transparent min-h-9 px-4 py-2 bg-accent text-white font-bold hover:opacity-90">
                        Schedule
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<style scoped>
/* Remove default date/time picker icons in some browsers to use our custom ones */
input::-webkit-calendar-picker-indicator {
    background: transparent;
    bottom: 0;
    color: transparent;
    cursor: pointer;
    height: auto;
    left: 0;
    position: absolute;
    right: 0;
    top: 0;
    width: auto;
}
</style>
