<script setup>
import { router } from '@inertiajs/vue3'
import { ref, watch, nextTick } from 'vue'

const props = defineProps({
    show: Boolean,
    quote: Object,
    categories: Array,
    moods: Array,
})

const emit = defineEmits(['close'])

const form = ref({
    quote: '',
    category: '',
    mood: '',
})

// Refs for the v-select components
const categorySelect = ref(null)
const moodSelect = ref(null)
const modalContentRef = ref(null) // Ref for the modal content div

watch(
    () => props.quote,
    (q) => {
        if (!q) return
        form.value = {
            quote: q.quote,
            category: q.category,
            mood: q.mood,
        }
    },
    { immediate: true }
)

// Function to close other v-select dropdowns
const closeOtherSelects = (currentSelectRef) => {
    if (categorySelect.value && categorySelect.value !== currentSelectRef) {
        categorySelect.value.searchEl.blur()
    }
    if (moodSelect.value && moodSelect.value !== currentSelectRef) {
        moodSelect.value.searchEl.blur()
    }
}

const submit = () => {
    router.put(route('quotes.update', props.quote.id), form.value, {
        preserveScroll: true,
        onSuccess: () => emit('close'),
    })
}

// When the modal is shown, re-initialize vue-select
watch(() => props.show, (newVal) => {
    if (newVal) {
        nextTick(() => {
            // Re-initialization is handled by :key="show" on v-select
            // Ensure dropdownParent is set after modal is visible
        })
    }
})
</script>

<template>
    <div v-if="show" class="fixed inset-0 bg-black/50 flex items-center justify-center z-[9999]">
        <div ref="modalContentRef" class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-lg p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Edit Quote</h2>

            <div class="mb-4">
                <label for="quote" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Quote</label>
                <textarea id="quote" v-model="form.quote" class="w-full border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-md shadow-sm p-2 h-32 resize-y" ></textarea>
            </div>

            <div class="mb-4">
                <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Category</label>
                <v-select
                    ref="categorySelect"
                    :key="show"
                    id="category"
                    :options="categories"
                    label="name"
                    :reduce="category => category.name"
                    v-model="form.category"
                    placeholder="Select category"
                    :clearable="false"
                    class="w-full"
                    style="width: 100% !important"
                    @open="closeOtherSelects('category')"
                    teleport="body"
                />
            </div>

            <div class="mb-6">
                <label for="mood" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Mood</label>
                <v-select
                    ref="moodSelect"
                    :key="show"
                    id="mood"
                    :options="moods"
                    label="name"
                    :reduce="mood => mood.name"
                    v-model="form.mood"
                    placeholder="Select mood"
                    :clearable="false"
                    class="w-full"
                    style="width: 100% !important"
                    @open="closeOtherSelects('mood')"
                    teleport="body"
                />
            </div>

            <div class="flex justify-end gap-2">
                <button class="px-3 py-1.5 border rounded-md text-sm text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors duration-200" @click="$emit('close')">
                    Cancel
                </button>
                <button class="px-3 py-1.5 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700 transition-colors duration-200" @click="submit">
                    Save
                </button>
            </div>
        </div>
    </div>
</template>
