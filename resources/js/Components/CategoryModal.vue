<script setup>
import { useForm } from '@inertiajs/vue3'
import { watch } from 'vue'

const props = defineProps({
    show: Boolean,
    category: Object,
})

const emit = defineEmits(['close'])

const form = useForm({
    name: '',
})

watch(
    () => props.category,
    (val) => {
        form.name = val ? val.name : ''
    },
    { immediate: true }
)

const submit = () => {
    if (props.category) {
        form.put(`/categories/${props.category.id}`, {
            onSuccess: () => emit('close'),
        })
    } else {
        form.post('/categories', {
            onSuccess: () => emit('close'),
        })
    }
}
</script>

<template>
    <div v-if="show" class="fixed inset-0 bg-black/40 flex items-center justify-center">
        <div class="bg-white p-6 rounded w-full max-w-sm">
            <h2 class="text-lg font-semibold mb-4">
                {{ category ? 'Edit Category' : 'Add Category' }}
            </h2>

            <input v-model="form.name" placeholder="Category name" class="w-full border px-2 py-1 rounded" />

            <div class="mt-4 flex justify-end gap-2">
                <button @click="$emit('close')" class="border px-2 py-1 rounded">
                    Cancel
                </button>
                <button @click="submit" class="bg-black text-white px-2 py-1 rounded" :disabled="form.processing">
                    Save
                </button>
            </div>
        </div>
    </div>
</template>
