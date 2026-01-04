<script setup>
import { ref } from 'vue'
import axios from 'axios'
import { router } from '@inertiajs/vue3'

const props = defineProps({
    show: Boolean,
    categories: Array,
})

const emit = defineEmits(['close'])

const loading = ref(false)
const category = ref(
    props.categories.length ? props.categories[0].slug : null
)

const generateQuote = async () => {
    loading.value = true

    try {
        await axios.post('/quotes/generate', {
            category: category.value,
        })

        emit('close')
        router.reload({ only: ['quotes'] })
    } finally {
        loading.value = false
    }
}
</script>

<template>
    <div v-if="show" class="fixed inset-0 bg-black/50 flex items-center justify-center z-[9999]">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-md p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                Generate Quote
            </h2>

            <div class="mb-6">
                <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Category</label>
                <v-select
                    :key="show"
                    id="category"
                    :options="categories"
                    label="name"
                    :reduce="category => category.slug"
                    v-model="category"
                    placeholder="Select category"
                    :clearable="false"
                    class="w-full"
                    :append-to-body="true"
                />
            </div>

            <div class="flex justify-end gap-2">
                <button @click="$emit('close')" class="px-3 py-1.5 border rounded-md text-sm text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors duration-200">
                    Cancel
                </button>

                <button @click="generateQuote" class="px-3 py-1.5 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700 transition-colors duration-200" :disabled="loading">
                    {{ loading ? 'Generating…' : 'Generate' }}
                </button>
            </div>
        </div>
    </div>
</template>
