<script setup>
defineProps({
    value: {
        type: String,
    },
    required: {
        type: Boolean,
        default: false,
    },
});
</script>

<template>
    <label class="peer-disabled:cursor-not-allowed peer-disabled:opacity-70 text-xs font-bold uppercase tracking-wider text-muted-foreground">
        <span v-if="value">{{ value }}</span>
        <span v-else><slot /></span>
        <span v-if="required" class="text-red-500 ml-1">*</span>
    </label>
</template>
