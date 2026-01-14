<script setup>
import { onMounted, ref } from 'vue';

const model = defineModel({
    type: String,
    required: true,
});

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineProps({
    placeholder: String,
    disabled: {
        type: Boolean,
        default: false,
    },
    readonly: {
        type: Boolean,
        default: false,
    },
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input
        ref="input"
        v-model="model"
        :placeholder="placeholder"
        :disabled="disabled"
        :readonly="readonly"
        class="block w-full rounded-lg border-0 py-2.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-500 sm:text-sm sm:leading-6 dark:bg-gray-800 dark:text-gray-100 dark:ring-gray-600 dark:placeholder:text-gray-500 dark:focus:ring-primary-400"
    />
</template>
