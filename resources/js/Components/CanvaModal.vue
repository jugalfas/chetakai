<script setup>
import { defineProps, defineEmits } from 'vue';

const props = defineProps({
    show: Boolean,
});

const emit = defineEmits(['close']);

const close = () => {
    emit('close');
};
</script>

<template>
    <div v-if="show" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-md p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Design Quote</h2>
            <p class="text-gray-700 dark:text-gray-300 mb-6">This is where the Canva integration would go.</p>
            <div class="flex justify-end">
                <button @click="close" class="px-3 py-1.5 rounded-md text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors duration-200 text-sm">
                    Close
                </button>
            </div>
        </div>
    </div>
</template>
