<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import DeleteConfirmationModal from "@/Components/DeleteConfirmationModal.vue";
import { Head, router } from "@inertiajs/vue3";
import { ref } from "vue";

const props = defineProps({
    items: {
        type: Array,
        default: () => [],
    },
});

const title = ref("");
const editingId = ref(null);
const processing = ref(false);

const showDeleteConfirmModal = ref(false);
const itemToDelete = ref(null);

const resetForm = () => {
    title.value = "";
    editingId.value = null;
};

const submit = () => {
    processing.value = true;
    router.post(
        route("admin.prompts.store"),
        {
            resource: "categories",
            id: editingId.value,
            title: title.value,
        },
        {
            preserveScroll: true,
            onFinish: () => (processing.value = false),
            onSuccess: () => resetForm(),
        }
    );
};

const startEdit = (item) => {
    editingId.value = item.id;
    title.value = item.name ?? item.name;
};

const confirmDeleteItem = (item) => {
    itemToDelete.value = item;
    showDeleteConfirmModal.value = true;
};

const deleteItem = () => {
    if (!itemToDelete.value) return;

    router.delete(route("admin.prompts.destroy"), {
        data: {
            resource: "categories",
            id: itemToDelete.value.id,
        },
        preserveScroll: true,
        onBefore: () => (processing.value = true),
        onFinish: () => (processing.value = false),
        onSuccess: () => {
            showDeleteConfirmModal.value = false;
            itemToDelete.value = null;
        },
    });
};
</script>

<template>
    <Head title="Categories" />
    <AdminLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-foreground leading-tight">Categories</h2>
        </template>

        <div class="p-6">
            <div class="rounded-lg border border-border bg-card p-6 space-y-6">
                <form class="flex flex-col sm:flex-row gap-3 items-stretch sm:items-end" @submit.prevent="submit">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-muted-foreground mb-1">
                            Title
                        </label>
                        <input
                            v-model="title"
                            type="text"
                            class="w-full rounded-md border border-border bg-background px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary"
                            placeholder="e.g. Marketing"
                            required
                        />
                    </div>
                    <button
                        type="submit"
                        class="inline-flex items-center justify-center rounded-md bg-primary px-4 py-2 text-sm font-semibold text-primary-foreground hover:bg-primary/90 disabled:opacity-50"
                        :disabled="processing || !title"
                    >
                        {{ editingId ? "Update" : "Add" }}
                    </button>
                </form>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-muted border-b border-border">
                                <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-muted-foreground">
                                    ID
                                </th>
                                <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-muted-foreground">
                                    Title
                                </th>
                                <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-muted-foreground">
                                    User
                                </th>
                                <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-muted-foreground text-right">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="item in items"
                                :key="item.id"
                                class="border-b border-border hover:bg-muted/40 transition-colors"
                            >
                                <td class="px-4 py-3 text-sm text-foreground">
                                    {{ item.id }}
                                </td>
                                <td class="px-4 py-3 text-sm text-foreground">
                                    {{ item.name ?? item.name }}
                                </td>
                                <td class="px-4 py-3 text-sm text-muted-foreground">
                                    {{ item.user_id ?? '—' }}
                                </td>
                                <td class="px-4 py-3 text-sm text-right space-x-2">
                                    <button
                                        type="button"
                                        class="text-xs text-primary hover:underline"
                                        @click="startEdit(item)"
                                    >
                                        Edit
                                    </button>
                                    <button
                                        type="button"
                                        class="text-xs text-destructive hover:underline"
                                        @click="confirmDeleteItem(item)"
                                    >
                                        Delete
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="!items.length">
                                <td
                                    colspan="4"
                                    class="px-4 py-6 text-center text-sm text-muted-foreground italic"
                                >
                                    No categories yet.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <DeleteConfirmationModal
            :show="showDeleteConfirmModal"
            :processing="processing"
            title="Delete Category"
            message="Are you sure you want to delete this category? This action cannot be undone."
            @close="showDeleteConfirmModal = false"
            @confirm="deleteItem"
        />
    </AdminLayout>
</template>
