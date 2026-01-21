<script setup>
import { ref, computed } from 'vue'
import CategoryModal from '@/Components/CategoryModal.vue'
import DeleteCategoryModal from '@/Components/DeleteCategoryModal.vue'
import { Head, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { toast } from 'vue-sonner'

const { categories } = defineProps({
    categories: Array,
})

const showModal = ref(false)
const showDeleteModal = ref(false)
const editCategory = ref(null)
const categoryToDelete = ref(null)
const deleteProcessing = ref(false)
const search = ref('')

const filteredCategories = computed(() => {
    if (!search.value) return categories
    return categories.filter(cat => cat.name.toLowerCase().includes(search.value.toLowerCase()))
})

const colors = ['bg-blue-500', 'bg-emerald-500', 'bg-amber-500', 'bg-purple-500', 'bg-rose-500']

const getColorClass = (index) => colors[index % colors.length]

const openCreate = () => {
    editCategory.value = null
    showModal.value = true
}

const openEdit = (category) => {
    editCategory.value = category
    showModal.value = true
}

const openDelete = (category) => {
    categoryToDelete.value = category
    showDeleteModal.value = true
}

const confirmDelete = () => {
    if (!categoryToDelete.value) return

    deleteProcessing.value = true
    router.delete(route('admin.categories.destroy', categoryToDelete.value.id), {
        onSuccess: () => {
            showDeleteModal.value = false
            categoryToDelete.value = null
            toast.success('Category deleted successfully')
        },
        onError: () => {
            toast.error('Failed to delete category')
        },
        onFinish: () => {
            deleteProcessing.value = false
        }
    })
}

const saveCategory = (data) => {
    if (editCategory.value) {
        router.put(route('admin.categories.update', editCategory.value.id), data, {
            onSuccess: () => {
                showModal.value = false
                editCategory.value = null
                toast.success('Category updated successfully')
            }
        })
    } else {
        router.post(route('admin.categories.store'), data, {
            onSuccess: () => {
                showModal.value = false
                toast.success('Category created successfully')
            }
        })
    }
}
</script>

<template>
    <Head title="Manage Categories" />

    <AdminLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Manage Categories</h2>
        </template>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h1 class="text-3xl font-bold tracking-tight text-foreground">Categories</h1>
                        <p class="text-muted-foreground mt-1">Manage and organize all quote categories.</p>
                    </div>
                    <button
                        class="inline-flex items-center justify-center whitespace-nowrap transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 hover-elevate active-elevate-2 border border-primary-border min-h-8 rounded-md text-xs gap-2 bg-accent text-accent-foreground hover:bg-accent/90 font-semibold px-4 py-2"
                        @click="openCreate">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-plus h-4 w-4" aria-hidden="true">
                            <path d="M5 12h14"></path>
                            <path d="M12 5v14"></path>
                        </svg>Add Category
                    </button>
                </div>

                <div class="relative w-full max-w-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-search absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground" aria-hidden="true">
                        <path d="m21 21-4.34-4.34"></path>
                        <circle cx="11" cy="11" r="8"></circle>
                    </svg>
                    <input
                        v-model="search"
                        class="flex w-full rounded-md border px-3 py-1 text-base shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50 md:text-sm pl-9 bg-card border-border focus:ring-accent h-10 text-foreground"
                        placeholder="Search categories..."
                    >
                </div>

                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    <div v-for="(cat, index) in filteredCategories" :key="cat.id" class="rounded-xl border text-card-foreground border-border bg-card hover:border-accent/50 transition-all group shadow-sm">
                        <div class="p-6">
                            <div class="flex items-start justify-between mb-4">
                                <div class="h-10 w-10 rounded-xl flex items-center justify-center text-white shadow-lg" :class="getColorClass(index)">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-folder h-5 w-5">
                                        <path d="M20 20a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-7.9a2 2 0 0 1-1.69-.9L9.6 3.9A2 2 0 0 0 7.93 3H4a2 2 0 0 0-2 2v13a2 2 0 0 0 2 2Z"></path>
                                    </svg>
                                </div>
                                <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button @click="openEdit(cat)" class="p-2 hover:bg-accent rounded-md transition-colors" title="Edit">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-pencil h-4 w-4 text-muted-foreground">
                                            <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"></path>
                                            <path d="m15 5 4 4"></path>
                                        </svg>
                                    </button>
                                    <button @click="openDelete(cat)" class="p-2 hover:bg-destructive/10 rounded-md transition-colors" title="Delete">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trash2 h-4 w-4 text-destructive">
                                            <path d="M3 6h18"></path>
                                            <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                                            <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                                            <line x1="10" x2="10" y1="11" y2="17"></line>
                                            <line x1="14" x2="14" y1="11" y2="17"></line>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <h3 class="font-bold text-lg text-foreground group-hover:text-accent transition-colors truncate">{{ cat.name }}</h3>
                            <div class="mt-4 flex items-center justify-between text-xs text-muted-foreground">
                                <span class="flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar h-3 w-3">
                                        <path d="M8 2v4"></path>
                                        <path d="M16 2v4"></path>
                                        <rect width="18" height="18" x="3" y="4" rx="2"></rect>
                                        <path d="M3 10h18"></path>
                                    </svg>
                                    {{ new Date(cat.created_at).toLocaleDateString() }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="filteredCategories.length === 0" class="text-center py-20 border-2 border-dashed border-border rounded-xl">
                    <div class="h-16 w-16 bg-accent/10 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-folder-open h-8 w-8 text-accent">
                            <path d="m6 14 1.45-2.9A2 2 0 0 1 9.24 10H20a2 2 0 0 1 1.94 2.5l-1.55 6a2 2 0 0 1-1.94 1.5H4a2 2 0 0 1-2-2V5c0-1.1.9-2 2-2h3.93a2 2 0 0 1 1.66.9l.82 1.2a2 2 0 0 0 1.66.9H18a2 2 0 0 1 2 2v2"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-foreground">No categories found</h3>
                    <p class="text-muted-foreground mt-1">Start by adding a new category to organize your quotes.</p>
                    <button @click="openCreate" class="mt-6 inline-flex items-center gap-2 bg-primary text-primary-foreground px-6 py-2 rounded-md font-semibold hover:bg-primary/90 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-plus h-4 w-4">
                            <path d="M5 12h14"></path>
                            <path d="M12 5v14"></path>
                        </svg>Add Category
                    </button>
                </div>
            </div>
        </div>

        <CategoryModal
            :show="showModal"
            :category="editCategory"
            @close="showModal = false"
            @submit="saveCategory"
        />

        <DeleteCategoryModal
            :show="showDeleteModal"
            :category-name="categoryToDelete?.name"
            :processing="deleteProcessing"
            @close="showDeleteModal = false"
            @confirm="confirmDelete"
        />
    </AdminLayout>
</template>
