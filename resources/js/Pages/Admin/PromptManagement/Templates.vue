<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Head } from "@inertiajs/vue3";
</script>

<template>
    <Head title="Templates" />
    <AdminLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-foreground leading-tight">Templates</h2>
        </template>
        <div class="p-6">
            <div class="rounded-lg border border-border bg-card p-6">
                <p class="text-muted-foreground">Blank page</p>
            </div>
        </div>
    </AdminLayout>
    </template>
