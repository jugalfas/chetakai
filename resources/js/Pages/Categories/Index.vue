<script setup>
import { ref } from 'vue'
import CategoryModal from '@/Components/CategoryModal.vue'
import { Head, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

defineProps({
    categories: Array,
})

const showModal = ref(false)
const editCategory = ref(null)

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

    <Head title="Quotes" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Categories
            </h2>
        </template>

        <section class="bg-gray-50 dark:bg-gray-900 p-3 sm:p-5">
            <div class="mx-auto max-w-screen-xl px-4 lg:px-12">
                <div class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden">

                    <!-- Actions -->
                    <div class="flex flex-col md:flex-row items-center justify-between p-4 gap-4">
                        <div></div>
                        <div class="flex gap-2">
                            <button
                                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700"
                                @click="openCreate">
                                Add Category
                            </button>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-4 py-3">Name</th>
                                    <th class="px-4 py-3">Slug</th>
                                    <th class="px-4 py-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="cat in categories" :key="cat.id" class="border-b dark:border-gray-700">
                                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                                        {{ cat.name }}
                                    </td>
                                    <td class="px-4 py-3 text-gray-500">{{ cat.slug }}</td>
                                    <td class="px-4 py-3 text-right space-x-2">
                                        <button @click="openEdit(cat)"
                                            class="text-blue-600 hover:underline text-sm">Edit</button>
                                        <button @click="deleteCategory(cat.id)"
                                            class="text-red-600 hover:underline text-sm">Delete</button>
                                    </td>
                                </tr>
                                <tr v-if="categories.length === 0">
                                    <td colspan="3" class="text-center p-6 text-gray-400">
                                        No categories found.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <CategoryModal :show="showModal" :category="editCategory" @close="showModal = false" />
                </div>
            </div>
        </section>
    </AuthenticatedLayout>
</template>
