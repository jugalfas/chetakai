<template>
    <div class="flex items-center justify-center">
        <img :src="`${basePath}/logo/Chetak-white.png`" alt="Logo" :class="`w-${size} h-${size} dark:block hidden`" />
        <img :src="`${basePath}/logo/Chetak.png`" alt="Logo" :class="`w-${size} h-${size} dark:hidden block`" />
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';

const { size } = defineProps<{
    size?: number
}>()

const basePath = computed(() => {
    return window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1' || window.location.hostname === 'chetakai.test' ? '' : '/public';
});
</script>
