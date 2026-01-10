<script setup>
import { router } from '@inertiajs/vue3'
import { ref, watch, nextTick } from 'vue'

const props = defineProps({
    show: Boolean,
    quote: Object,
    categories: Array,
})

const emit = defineEmits(['close'])

const form = ref({
    quote: '',
    category: '',
    caption: '',
    image: null,
})

// Refs for the v-select components
const categorySelect = ref(null)
const modalContentRef = ref(null) // Ref for the modal content div

watch(
    () => props.quote,
    (q) => {
        if (!q) return
        form.value = {
            quote: q.quote,
            category: q.category,
            caption: q.post?.caption || '',
            image: null,
        }
    },
    { immediate: true }
)

// Function to close other v-select dropdowns
const closeOtherSelects = (currentSelectRef) => {
    if (categorySelect.value && categorySelect.value !== currentSelectRef) {
        categorySelect.value.searchEl.blur()
    }
}

const submit = () => {
    const formData = new FormData()
    formData.append('quote', form.value.quote)
    formData.append('category', form.value.category)
    formData.append('caption', form.value.caption)
    if (form.value.image) {
        formData.append('image', form.value.image)
    }
    formData.append('_method', 'PUT')

    router.post(route('quotes.update', props.quote.id), formData, {
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
                <label for="caption" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Caption</label>
                <textarea id="caption" v-model="form.caption" class="w-full border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-md shadow-sm p-2 h-24 resize-y" ></textarea>
            </div>

            <div class="mb-4">
                <label for="image" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Image</label>
                <input id="image" type="file" @change="form.image = $event.target.files[0]" accept="image/*" class="w-full border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-md shadow-sm p-2" />
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
