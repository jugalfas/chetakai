<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import { toast } from "vue-sonner";
import PromptTemplateForm from "./Form.vue";

const props = defineProps({
    platforms: Array,
    contentTypes: Array,
});

const submit = (form) => {
    form.post(route("admin.prompt-templates.store"), {
        onSuccess: () => {
            toast.success("Prompt template created successfully.");
        },
    });
};
</script>

<template>
    <Head title="Create Prompt Template" />
    <AdminLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div class="space-y-1">
                    <h2 class="font-bold text-2xl text-foreground tracking-tight">Create Prompt Template</h2>
                    <p class="text-sm text-muted-foreground">Define a new template for generating AI content.</p>
                </div>
                <Link
                    :href="route('admin.prompt-templates.index')"
                    class="inline-flex items-center justify-center rounded-md border border-input bg-background px-4 py-2 text-sm font-semibold text-foreground hover:bg-muted transition-colors shadow-sm"
                >
                    Back to List
                </Link>
            </div>
        </template>

        <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-5xl mx-auto">
            <PromptTemplateForm
                :platforms="platforms"
                :contentTypes="contentTypes"
                @submit="submit"
            />
        </div>
    </AdminLayout>
</template>
