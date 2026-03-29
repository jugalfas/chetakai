<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Head, router, Link } from "@inertiajs/vue3";
import { ref, watch } from "vue";
import { toast } from "vue-sonner";
import { PencilSquareIcon, TrashIcon, MagnifyingGlassIcon, PlusIcon } from "@heroicons/vue/24/outline";
import DeleteConfirmationModal from "@/Components/DeleteConfirmationModal.vue";

const props = defineProps({
    promptTemplates: Object,
    filters: Object,
});

const search = ref(props.filters.search || "");
const showDeleteModal = ref(false);
const templateToDelete = ref(null);
const processing = ref(false);

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

let timeout = null;
watch(
    search,
    (value) => {
        if (timeout) clearTimeout(timeout);
        timeout = setTimeout(() => {
            router.get(
                route("admin.prompt-templates.index"),
                { search: value },
                { preserveState: true, replace: true }
            );
        }, 300);
    }
);

const getStatusClass = (status) => {
    return status
        ? "bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400"
        : "bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400";
};

const getScopeClass = (scope) => {
    return scope === 'system'
        ? "bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400"
        : "bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400";
};
</script>

<template>
    <Head title="Prompt Templates" />
    <AdminLayout>
        <div class="p-6">
            <!-- Header section -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight text-foreground">Prompt Templates</h1>
                    <p class="mt-1 text-sm text-muted-foreground">Manage AI prompt templates for different platforms and content types.</p>
                </div>
                <Link
                    :href="route('admin.prompt-templates.create')"
                    class="inline-flex items-center justify-center rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-primary-foreground shadow-sm hover:bg-primary/90 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary transition-all duration-200"
                >
                    <PlusIcon class="mr-2 h-4 w-4" />
                    New Template
                </Link>
            </div>

            <!-- Filters -->
            <div class="mb-6 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="relative w-full max-w-sm">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <MagnifyingGlassIcon class="h-4 w-4 text-muted-foreground" aria-hidden="true" />
                    </div>
                    <input
                        v-model="search"
                        type="text"
                        class="block w-full rounded-lg border-0 py-2.5 pl-10 text-sm text-foreground ring-1 ring-inset ring-border placeholder:text-muted-foreground focus:ring-2 focus:ring-inset focus:ring-primary bg-background shadow-sm transition-all"
                        placeholder="Search templates..."
                    />
                </div>
            </div>

            <!-- Table -->
            <div class="rounded-xl border border-border bg-card shadow-sm overflow-hidden">
                <div class="overflow-x-auto min-h-[400px]">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-muted/50 border-b border-border">
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-muted-foreground">Name</th>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-muted-foreground">Platform</th>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-muted-foreground">Content Type</th>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-muted-foreground text-center">Scope</th>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-muted-foreground text-center">Version</th>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-muted-foreground text-center">Status</th>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-muted-foreground text-right border-l border-border/50">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            <tr
                                v-for="item in promptTemplates.data"
                                :key="item.id"
                                class="hover:bg-muted/30 transition-colors group"
                            >
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-semibold text-foreground group-hover:text-primary transition-colors">{{ item.name }}</span>
                                        <span v-if="item.is_default" class="text-[10px] text-primary font-bold uppercase mt-1">Default Template</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-muted-foreground">
                                    {{ item.platform?.name ?? 'Generic' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-muted-foreground">
                                    {{ item.content_type?.name ?? 'Unspecified' }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset"
                                        :class="getScopeClass(item.scope)"
                                    >
                                        {{ item.scope }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center text-sm font-mono font-medium text-foreground">
                                    v{{ item.version }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <button
                                        type="button"
                                        class="inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-bold uppercase tracking-tight transition-all duration-200"
                                        :class="getStatusClass(item.status)"
                                        @click="toggleStatus(item)"
                                    >
                                        {{ item.status ? "Active" : "Disabled" }}
                                    </button>
                                </td>
                                <td class="px-6 py-4 text-right border-l border-border/50">
                                    <div class="flex items-center justify-end space-x-3">
                                        <Link
                                            :href="route('admin.prompt-templates.edit', item.id)"
                                            class="inline-flex items-center text-muted-foreground hover:text-primary transition-colors"
                                            title="Edit Template"
                                        >
                                            <PencilSquareIcon class="h-5 w-5" />
                                        </Link>
                                        <button
                                            type="button"
                                            class="inline-flex items-center text-muted-foreground hover:text-destructive transition-colors"
                                            @click="confirmDelete(item)"
                                            title="Delete Template"
                                        >
                                            <TrashIcon class="h-5 w-5" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="!promptTemplates.data.length">
                                <td
                                    colspan="7"
                                    class="px-6 py-20 text-center"
                                >
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="rounded-full bg-muted p-3 mb-4">
                                            <MagnifyingGlassIcon class="h-6 w-6 text-muted-foreground" />
                                        </div>
                                        <p class="text-lg font-semibold text-foreground">No templates found</p>
                                        <p class="text-sm text-muted-foreground mt-1">Try adjusting your search filters or create a new template.</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div
                    v-if="promptTemplates.links && promptTemplates.links.length > 3"
                    class="px-6 py-4 border-t border-border bg-muted/20 flex flex-col sm:flex-row items-center justify-between gap-4"
                >
                    <p class="text-xs text-muted-foreground font-medium">
                        Showing <span class="text-foreground">{{ promptTemplates.from }}</span> to <span class="text-foreground">{{ promptTemplates.to }}</span> of <span class="text-foreground">{{ promptTemplates.total }}</span> entries
                    </p>
                    <nav class="flex items-center gap-1.5">
                        <Link
                            v-for="(link, k) in promptTemplates.links"
                            :key="k"
                            :href="link.url || '#'"
                            v-html="link.label"
                            class="px-3.5 py-1.5 text-xs font-semibold rounded-lg transition-all duration-200 border border-transparent shadow-sm"
                            :class="[
                                link.active
                                    ? 'bg-primary text-primary-foreground'
                                    : link.url
                                        ? 'bg-background text-foreground border-border hover:bg-muted'
                                        : 'bg-background text-muted-foreground opacity-50 cursor-not-allowed border-border',
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
            message="Are you sure you want to delete this prompt template? This action cannot be undone and will affect any automations using this template."
            @close="showDeleteModal = false"
            @confirm="deleteTemplate"
        />
    </AdminLayout>
</template>
