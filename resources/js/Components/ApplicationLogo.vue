<template>
    <div class="flex items-center justify-center">
        <img src="/logo/Chetak-white.png" alt="Logo" :class="`w-${size} h-${size} dark:block hidden`" />
        <img src="/logo/Chetak.png" alt="Logo" :class="`w-${size} h-${size} dark:hidden block`" />
    </div>
</template>

<script setup lang="ts">
const { size } = defineProps<{
    size?: number
}>()
</script>
