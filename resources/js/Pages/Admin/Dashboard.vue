<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    // You can add props here later
});
</script>

<template>

    <Head title="Admin Dashboard" />

    <AdminLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Admin Dashboard</h2>
        </template>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="rounded-xl border text-card-foreground border-border bg-card shadow-sm card-shadow">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    Hello admin
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
