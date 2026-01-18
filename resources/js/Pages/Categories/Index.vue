<script setup>
import { ref, computed } from 'vue'
import CategoryModal from '@/Components/CategoryModal.vue'
import { Head, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const { categories } = defineProps({
    categories: Array,
})

const showModal = ref(false)
const editCategory = ref(null)
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

const deleteCategory = (id) => {
    if (!confirm('Delete this category?')) return
    router.delete(`/categories/${id}`)
}
</script>

<template>

    <Head title="Categories" />

    <AuthenticatedLayout>
        <div class="mx-auto max-w-7xl animate-in fade-in slide-in-from-bottom-2 duration-500">
            <div class="space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h1 class="text-3xl font-bold tracking-tight text-foreground">Categories</h1>
                        <p class="text-muted-foreground mt-1">Manage and organize your quote categories.</p>
                    </div>
                    <button
                        class="inline-flex items-center justify-center whitespace-nowrap transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 hover-elevate active-elevate-2 border border-primary-border min-h-8 rounded-md text-xs gap-2 bg-accent text-accent-foreground hover:bg-accent/90 font-semibold px-4"
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
                        value="">
                </div>
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    <div v-for="(cat, index) in filteredCategories" :key="cat.id" class="rounded-xl border text-card-foreground border-border bg-card hover:border-accent/50 transition-all group shadow-sm">
                        <div class="p-6">
                            <div class="flex items-start justify-between mb-4">
                                <div class="h-10 w-10 rounded-xl flex items-center justify-center text-white shadow-lg" :class="getColorClass(index)">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-grid3x3 lucide-grid-3x3 h-5 w-5" aria-hidden="true">
                                        <rect width="18" height="18" x="3" y="3" rx="2"></rect>
                                        <path d="M3 9h18"></path>
                                        <path d="M3 15h18"></path>
                                        <path d="M9 3v18"></path>
                                        <path d="M15 3v18"></path>
                                    </svg>
                                </div>
                                <div class="flex gap-1">
                                    <button
                                        @click="openEdit(cat)"
                                        class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 hover-elevate active-elevate-2 border border-transparent h-8 w-8 text-muted-foreground hover:text-foreground">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-pen h-4 w-4" aria-hidden="true">
                                            <path d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z"></path>
                                        </svg>
                                    </button>
                                    <button
                                        @click="deleteCategory(cat.id)"
                                        class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 hover-elevate active-elevate-2 border border-transparent h-8 w-8 text-muted-foreground hover:text-destructive">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trash2 lucide-trash-2 h-4 w-4" aria-hidden="true">
                                            <path d="M10 11v6"></path>
                                            <path d="M14 11v6"></path>
                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"></path>
                                            <path d="M3 6h18"></path>
                                            <path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <div class="space-y-1">
                                <h3 class="text-lg font-bold text-foreground group-hover:text-accent transition-colors">{{ cat.name }}</h3>
                                <div class="flex items-center gap-2">
                                    <div class="whitespace-nowrap inline-flex items-center rounded-md px-2.5 py-0.5 transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 hover-elevate border-transparent bg-muted text-muted-foreground hover:bg-muted border-0 text-[10px] font-bold uppercase tracking-wider">{{ cat.quotes_count || 0 }} Quotes Assigned</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-if="filteredCategories.length === 0" class="col-span-full text-center p-6 text-gray-400">
                        No categories found.
                    </div>
                </div>
            </div>
        </div>

        <CategoryModal :show="showModal" :category="editCategory" @close="showModal = false" />
    </AuthenticatedLayout>
</template>
