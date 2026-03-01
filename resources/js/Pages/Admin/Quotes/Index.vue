<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import DeleteConfirmationModal from "@/Components/DeleteConfirmationModal.vue";
import { Head, router, Link } from "@inertiajs/vue3";
import { ref, watch, computed } from "vue";
import {
    TrashIcon,
    MagnifyingGlassIcon,
    FunnelIcon,
    PencilIcon,
} from "@heroicons/vue/24/outline";
import { toast } from 'vue-sonner';
import EditPostModal from "@/Components/EditPostModal.vue";

const props = defineProps({
    posts: Object,
    filters: Object,
    categories: Array,
    statusCounts: Object,
    allCategoriesCount: Number,
});

const search = ref(props.filters?.search || "");
const currentStatus = ref(props.filters?.status || "all");
const currentCategory = ref(props.filters?.category || "");
const currentSort = ref(props.filters?.sort || "created_at");
const showFiltersDropdown = ref(false);

const showDeleteModal = ref(false);
const showEditModal = ref(false);
const postToDelete = ref(null);
const postToEdit = ref(null);
const processing = ref(false);

// Server-side filtering
const updateFilters = () => {
    router.get(
        route("admin.posts.index"),
        {
            search: search.value,
            status: currentStatus.value,
            category: currentCategory.value,
            sort: currentSort.value,
        },
        {
            preserveState: true,
            replace: true,
        }
    );
};

watch([search, currentStatus, currentCategory, currentSort], () => {
    updateFilters();
});

const openEdit = (post) => {
    postToEdit.value = post;
    showEditModal.value = true;
};

const openDelete = (post) => {
    postToDelete.value = post;
    showDeleteModal.value = true;
};

const deletePost = () => {
    if (postToDelete.value) {
        router.delete(route('admin.posts.destroy', postToDelete.value.id), {
            onBefore: () => processing.value = true,
            onFinish: () => processing.value = false,
            onSuccess: () => {
                showDeleteModal.value = false;
                postToDelete.value = null;
                toast.success('Post deleted successfully');
            }
        });
    }
};

const filterDescription = computed(() => {
    if (currentCategory.value) {
        return `in the ${currentCategory.value} category`;
    } else if (currentStatus.value !== 'all') {
        return `${currentStatus.value} posts`;
    } else {
        return '';
    }
});

const clearFilters = () => {
    search.value = '';
    currentStatus.value = 'all';
    currentCategory.value = '';
    currentSort.value = 'created_at';
};
</script>

<template>
    <Head title="Manage Posts" />

    <AdminLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Manage Posts</h2>
        </template>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Header Section -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h1 class="text-3xl font-bold tracking-tight text-foreground">Posts</h1>
                        <p class="text-muted-foreground mt-1">
                            Manage and organize all posts in the system.
                        </p>
                    </div>
                </div>

            <!-- Filters Section -->
            <div class="flex flex-col lg:flex-row gap-4 items-start lg:items-center justify-between">
                <div class="flex bg-muted p-1 rounded-lg border border-border overflow-x-auto max-w-full">
                    <button v-for="(label, status) in { all: 'All', posted: 'Published', scheduled: 'Scheduled', draft: 'Draft' }" 
                        :key="status"
                        @click="currentStatus = status" 
                        :class="[
                            'px-4 py-1.5 rounded-md text-sm font-medium transition-all whitespace-nowrap',
                            currentStatus === status
                                ? 'bg-background text-foreground shadow-sm'
                                : 'text-muted-foreground hover:text-foreground'
                        ]">
                        {{ label }}
                        <span class="opacity-50 ml-1">({{ status === 'posted' ? statusCounts.published : (status === 'draft' ? statusCounts.draft : statusCounts[status]) }})</span>
                    </button>
                </div>

                <div class="relative flex items-center gap-2 w-full lg:w-auto">
                    <div class="relative flex-1 lg:w-64">
                        <MagnifyingGlassIcon class="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground" aria-hidden="true" />
                        <input v-model="search"
                            class="flex w-full rounded-md border px-3 py-1 text-base shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50 md:text-sm pl-9 bg-card border-border focus:ring-accent h-9 text-foreground"
                            placeholder="Search posts..." />
                    </div>
                    
                    <div class="relative">
                        <button @click="showFiltersDropdown = !showFiltersDropdown"
                            class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 border shadow-sm h-9 w-9 border-border bg-card text-foreground"
                            type="button">
                            <FunnelIcon class="h-4 w-4" aria-hidden="true" />
                        </button>

                        <!-- Filters Dropdown -->
                        <div v-show="showFiltersDropdown"
                            class="z-50 absolute top-full right-0 mt-2 w-56 rounded-md border border-border bg-popover p-1 shadow-md text-popover-foreground">
                            <div class="px-2 py-1.5 text-sm font-semibold">Filter by Category</div>
                            <div class="h-px bg-border my-1"></div>
                            <div @click="currentCategory = ''; showFiltersDropdown = false"
                                class="relative flex select-none items-center justify-between rounded-sm px-2 py-1.5 text-sm outline-none transition-colors hover:bg-accent hover:text-accent-foreground cursor-pointer"
                                :class="{ 'bg-accent/50 text-accent-foreground': currentCategory === '' }">
                                All Categories
                                <span class="text-xs font-semibold bg-muted px-2 py-0.5 rounded border border-border">{{ props.allCategoriesCount }}</span>
                            </div>
                            <div v-for="category in categories" :key="category.id" 
                                @click="currentCategory = category.id; showFiltersDropdown = false"
                                class="relative flex select-none items-center justify-between rounded-sm px-2 py-1.5 text-sm outline-none transition-colors hover:bg-accent hover:text-accent-foreground cursor-pointer"
                                :class="{ 'bg-accent/50 text-accent-foreground': currentCategory == category.id }">
                                {{ category.name }}
                                <span class="text-xs font-semibold bg-muted px-2 py-0.5 rounded border border-border">{{ category.posts_count }}</span>
                            </div>
                            
                            <div class="h-px bg-border my-1"></div>
                            <div class="px-2 py-1.5 text-sm font-semibold">Sort by</div>
                            <div @click="currentSort = 'created_at'; showFiltersDropdown = false"
                                class="relative flex select-none items-center rounded-sm px-2 py-1.5 text-sm outline-none transition-colors hover:bg-accent hover:text-accent-foreground cursor-pointer">
                                Newest first
                            </div>
                            <div @click="currentSort = 'content'; showFiltersDropdown = false"
                                class="relative flex select-none items-center rounded-sm px-2 py-1.5 text-sm outline-none transition-colors hover:bg-accent hover:text-accent-foreground cursor-pointer">
                                Alphabetical
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Posts Grid -->
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                <!-- Empty State -->
                <div v-if="posts.data.length === 0"
                    class="col-span-full py-20 flex flex-col items-center justify-center text-center space-y-4">
                    <div class="h-20 w-20 rounded-full bg-muted/30 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="h-10 w-10 text-muted-foreground/40" aria-hidden="true">
                            <path d="M16 3a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2 1 1 0 0 1 1 1v1a2 2 0 0 1-2 2 1 1 0 0 0-1 1v2a1 1 0 0 0 1 1 6 6 0 0 0 6-6V5a2 2 0 0 0-2-2z" />
                            <path d="M5 3a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2 1 1 0 0 1 1 1v1a2 2 0 0 1-2 2 1 1 0 0 0-1 1v2a1 1 0 0 0 1 1 6 6 0 0 0 6-6V5a2 2 0 0 0-2-2z" />
                        </svg>
                    </div>
                    <div class="space-y-1">
                        <h3 class="text-xl font-bold text-foreground">No posts found</h3>
                        <p class="text-muted-foreground max-w-xs mx-auto">
                            There are currently no {{ filterDescription ? filterDescription : 'posts' }}.
                        </p>
                    </div>
                    <button @click="clearFilters"
                        class="inline-flex items-center justify-center gap-2 rounded-md text-sm font-bold transition-colors border border-accent/20 px-4 py-2 hover:bg-accent/10 text-accent">
                        Clear Filters
                    </button>
                </div>

                <!-- Post Cards -->
                <div v-for="post in posts.data" :key="post.id"
                    class="rounded-xl border border-border bg-card hover:border-accent/50 transition-all group overflow-hidden shadow-sm flex flex-col h-full min-h-[180px]">
                    <div class="p-6 flex-1">
                        <p class="text-base font-medium leading-relaxed italic text-foreground/90">
                            "{{ post.quote }}"
                        </p>
                    </div>
                    <div class="px-6 pb-6 mt-auto">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span :class="[
                                    'inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-medium',
                                    post.post?.status === 'posted' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' :
                                    post.post?.status === 'scheduled' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300' :
                                    (post.post?.status === 'draft' || !post.post) ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300' :
                                    'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-400'
                                ]">
                                    {{ post.post?.status === 'posted' ? 'Published' : (post.post?.status || 'Draft') }}
                                </span>
                            </div>
                            <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button @click="openEdit(post)" class="p-1 text-muted-foreground hover:text-accent transition-colors">
                                        <PencilIcon class="h-4 w-4" />
                                    </button>
                                    <button @click="openDelete(post)" class="p-1 text-muted-foreground hover:text-destructive transition-colors">
                                        <TrashIcon class="h-4 w-4" />
                                    </button>
                                </div>
                        </div>
                        <div class="mt-2 text-[10px] text-muted-foreground">
                            Created on {{ new Date(post.created_at).toLocaleDateString() }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="posts.links.length > 3" class="mt-8 flex justify-center">
                <nav class="flex items-center gap-1">
                    <template v-for="(link, k) in posts.links" :key="k">
                        <div v-if="link.url === null" 
                            class="px-3 py-1 text-sm text-gray-500 border border-border rounded-md opacity-50" 
                            v-html="link.label" />
                        <Link v-else 
                            :href="link.url" 
                            class="px-3 py-1 text-sm border border-border rounded-md hover:bg-accent/10 transition-colors" 
                            :class="{ 'bg-accent text-accent-foreground border-accent': link.active }" 
                            v-html="link.label" />
                    </template>
                </nav>
            </div>
        </div>

        <!-- Modals -->
        <EditPostModal
            :show="showEditModal"
            :post="postToEdit"
            :categories="categories"
            @close="showEditModal = false"
        />

        <DeleteConfirmationModal
            :show="showDeleteModal"
            :processing="processing"
            title="Delete Post"
            message="Are you sure you want to delete this post? This action cannot be undone."
            @close="showDeleteModal = false"
            @confirm="deletePost"
        />
    </AdminLayout>
</template>
