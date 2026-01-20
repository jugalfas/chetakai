<script setup>
import {
    ScrollAreaRoot,
    ScrollAreaViewport,
    ScrollAreaScrollbar,
    ScrollAreaThumb,
} from 'radix-vue'
</script>

<template>
    <ScrollAreaRoot class="h-full w-full overflow-hidden">
        <ScrollAreaViewport class="h-full w-full">
            <slot />
        </ScrollAreaViewport>

        <ScrollAreaScrollbar orientation="vertical"
            class="flex touch-none select-none transition-colors h-full w-2.5 border-l border-l-transparent p-[1px]">
            <ScrollAreaThumb class="relative flex-1 rounded-full bg-border" />
        </ScrollAreaScrollbar>
    </ScrollAreaRoot>
</template>
