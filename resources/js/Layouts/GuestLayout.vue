<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
import { SunIcon, MoonIcon } from '@heroicons/vue/24/outline';
import { ref } from 'vue';

const isDarkMode = ref(false);

const toggleDarkMode = () => {
    isDarkMode.value = !isDarkMode.value;
    applyTheme();
};

const applyTheme = () => {
    if (isDarkMode.value) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
};
</script>

<template>
    <div
        class="min-h-screen bg-background flex flex-col items-center justify-center p-4 transition-colors duration-300">
        <div class="mb-8 text-foreground text-center">
            <Link href="/">
                <ApplicationLogo class="h-20 w-20 fill-current text-gray-500" />
            </Link>
        </div>

        <slot />

        <button class="absolute bottom-6 right-4 border rounded-full p-2" @click="toggleDarkMode">
            <SunIcon v-if="isDarkMode" class="h-6 w-6" />
            <MoonIcon v-else class="h-6 w-6" />
        </button>
    </div>
</template>
