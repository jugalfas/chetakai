<script setup>
defineProps({
    message: {
        type: String,
    },
});
</script>

<template>
    <div v-if="message" class="mt-1 text-sm text-red-600 dark:text-red-400">
        {{ message }}
    </div>
</template>
