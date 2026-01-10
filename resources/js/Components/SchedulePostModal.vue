<script setup>
import { router } from '@inertiajs/vue3'
import { ref, watch } from 'vue'

const props = defineProps({
    show: Boolean,
    quote: Object,
})

const emit = defineEmits(['close'])

const form = ref({
    date: '',
    time: '20:00', // default 8 PM
})

const minTime = ref('')

// Utility: today date (YYYY-MM-DD)
const todayDate = () => new Date().toISOString().split('T')[0]

// Update min time logic
const updateMinTime = () => {
    const today = todayDate()

    if (form.value.date === today) {
        const now = new Date()
        now.setMinutes(now.getMinutes() + 5) // buffer
        minTime.value = now.toTimeString().slice(0, 5)

        // Auto-fix invalid selected time
        if (form.value.time < minTime.value) {
            form.value.time = minTime.value
        }
    } else {
        minTime.value = ''
    }
}

// Watch date + modal open
watch(
    () => [form.value.date, props.show],
    updateMinTime,
    { immediate: true }
)

// Reset form when modal closes
watch(() => props.show, (visible) => {
    if (!visible) {
        form.value = {
            date: '',
            time: '20:00',
        }
        minTime.value = ''
    }
})

const submit = () => {
    const scheduledAt = `${form.value.date} ${form.value.time}:00`

    router.post(
        route('quotes.schedule', props.quote.id),
        { scheduled_at: scheduledAt },
        {
            preserveScroll: true,
            onSuccess: () => {
                emit('close')
            },
        }
    )
}
</script>

<template>
    <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
        @click="emit('close')">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg max-w-md w-full mx-4" @click.stop>
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    Schedule Post
                </h3>

                <form @submit.prevent="submit">
                    <!-- DATE -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Date
                        </label>
                        <input v-model="form.date" type="date" required :min="todayDate()"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                    </div>

                    <!-- TIME -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Time
                        </label>
                        <input v-model="form.time" type="time" required :min="minTime"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                    </div>

                    <!-- ACTIONS -->
                    <div class="flex justify-end gap-3">
                        <button type="button" @click="emit('close')"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-md hover:bg-primary-700 focus:ring-4 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">
                            Schedule
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
