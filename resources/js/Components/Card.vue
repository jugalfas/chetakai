<template>
    <div class="bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-200 dark:ring-gray-700 rounded-lg overflow-hidden">
        <div v-if="$slots.header" class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
            <slot name="header" />
        </div>
        <div class="px-4 py-5 sm:p-6">
            <slot />
        </div>
        <div v-if="$slots.footer" class="bg-gray-50 dark:bg-gray-700/50 px-4 py-3 sm:px-6 border-t border-gray-200 dark:border-gray-700">
            <slot name="footer" />
        </div>
    </div>
</template>
