<script setup>
import { defineProps, defineEmits } from 'vue';

const props = defineProps({
    show: Boolean,
    title: String,
    message: String,
});

const emit = defineEmits(['close', 'confirm']);

const close = () => {
    emit('close');
};

const confirm = () => {
    emit('confirm');
};
</script>

<template>
    <div v-if="show" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-md p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ title }}</h2>
            <p class="text-gray-700 dark:text-gray-300 mb-6">{{ message }}</p>
            <div class="flex justify-end gap-3">
                <button @click="close" class="px-3 py-1.5 rounded-md text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors duration-200 text-sm">
                            Cancel
                        </button>
                        <button @click="confirm" class="px-3 py-1.5 rounded-md bg-red-600 text-white hover:bg-red-700 transition-colors duration-200 text-sm">
                            Delete
                        </button>
            </div>
        </div>
    </div>
</template>