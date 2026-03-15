<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Head, router, Link } from "@inertiajs/vue3";
import { ref, watch } from "vue";
import { toast } from "vue-sonner";
import { PencilIcon, TrashIcon } from "@heroicons/vue/24/outline";
import DeleteConfirmationModal from "@/Components/DeleteConfirmationModal.vue";

const props = defineProps({
    promptTemplates: Object,
});

const showDeleteModal = ref(false);
const templateToDelete = ref(null);
const processing = ref(false);

watch(
    () => props.promptTemplates,
    () => {},
    { immediate: true }
);

const confirmDelete = (item) => {
    templateToDelete.value = item;
    showDeleteModal.value = true;
};

const deleteTemplate = () => {
    if (!templateToDelete.value) return;
    router.delete(route("admin.prompt-templates.destroy", templateToDelete.value.id), {
        onBefore: () => (processing.value = true),
        onFinish: () => (processing.value = false),
        onSuccess: () => {
            showDeleteModal.value = false;
            templateToDelete.value = null;
            toast.success("Prompt template deleted successfully.");
        },
    });
};

const toggleStatus = (item) => {
    router.patch(route("admin.prompt-templates.toggle-status", item.id), {}, {
        preserveScroll: true,
        onSuccess: () => {
            toast.success(item.status ? "Prompt template deactivated." : "Prompt template activated.");
        },
    });
};
</script>

<template>
    <Head title="Prompt Templates" />
    <AdminLayout>
        <div class="p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                <h2 class="font-semibold text-xl text-foreground leading-tight">Prompt Templates</h2>
                <Link
                    :href="route('admin.prompt-templates.create')"
                    class="inline-flex items-center justify-center rounded-md bg-primary px-4 py-2 text-sm font-semibold text-primary-foreground hover:bg-primary/90 shadow-sm"
                >
                    Create Prompt Template
                </Link>
            </div>

            <div class="rounded-lg border border-border bg-card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-muted border-b border-border">
                                <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-muted-foreground">
                                    Name
                                </th>
                                <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-muted-foreground">
                                    Platform
                                </th>
                                <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-muted-foreground">
                                    Content Type
                                </th>
                                <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-muted-foreground">
                                    Status
                                </th>
                                <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-muted-foreground text-right">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="item in promptTemplates.data"
                                :key="item.id"
                                class="border-b border-border hover:bg-muted/40 transition-colors"
                            >
                                <td class="px-4 py-3 text-sm font-medium text-foreground">
                                    {{ item.name }}
                                </td>
                                <td class="px-4 py-3 text-sm text-foreground">
                                    {{ item.platform?.name ?? "—" }}
                                </td>
                                <td class="px-4 py-3 text-sm text-foreground">
                                    {{ (item.content_type || item.contentType)?.name ?? "—" }}
                                </td>
                                <td class="px-4 py-3">
                                    <button
                                        type="button"
                                        class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium transition-colors"
                                        :class="item.status
                                            ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300'
                                            : 'bg-muted text-muted-foreground'"
                                        @click="toggleStatus(item)"
                                    >
                                        {{ item.status ? "Active" : "Inactive" }}
                                    </button>
                                </td>
                                <td class="px-4 py-3 text-right space-x-2">
                                    <Link
                                        :href="route('admin.prompt-templates.edit', item.id)"
                                        class="inline-flex items-center gap-1 text-sm text-primary hover:underline"
                                    >
                                        <PencilIcon class="h-4 w-4" />
                                        Edit
                                    </Link>
                                    <button
                                        type="button"
                                        class="inline-flex items-center gap-1 text-sm text-destructive hover:underline"
                                        @click="confirmDelete(item)"
                                    >
                                        <TrashIcon class="h-4 w-4" />
                                        Delete
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="!promptTemplates.data.length">
                                <td
                                    colspan="5"
                                    class="px-4 py-6 text-center text-sm text-muted-foreground italic"
                                >
                                    No prompt templates yet.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div
                    v-if="promptTemplates.links && promptTemplates.links.length > 3"
                    class="px-4 py-3 bg-muted/40 border-t border-border flex justify-center"
                >
                    <nav class="flex items-center gap-1">
                        <Link
                            v-for="(link, k) in promptTemplates.links"
                            :key="k"
                            :href="link.url || '#'"
                            v-html="link.label"
                            class="px-3 py-1 text-sm rounded-md transition-colors"
                            :class="[
                                link.active
                                    ? 'bg-primary text-primary-foreground font-semibold'
                                    : 'text-muted-foreground hover:bg-muted',
                                !link.url ? 'opacity-50 cursor-not-allowed' : '',
                            ]"
                        />
                    </nav>
                </div>
            </div>
        </div>

        <DeleteConfirmationModal
            :show="showDeleteModal"
            :processing="processing"
            title="Delete Prompt Template"
            message="Are you sure you want to delete this prompt template? This action cannot be undone."
            @close="showDeleteModal = false"
            @confirm="deleteTemplate"
        />
    </AdminLayout>
</template>
