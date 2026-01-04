<script setup>
import EditQuoteModal from '@/Components/EditQuoteModal.vue'
import GenerateQuoteModal from '@/Components/GenerateQuoteModal.vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, router } from '@inertiajs/vue3'
import { ref, watch } from 'vue'
import { TrashIcon, MagnifyingGlassIcon, PlusIcon } from '@heroicons/vue/24/outline'
import DeleteConfirmationModal from '@/Components/DeleteConfirmationModal.vue'
import CanvaDesignModal from '@/Components/CanvaDesignModal.vue'

const props = defineProps({
    quotes: Object,
    filters: Object,
    categories: Array,
    moods: Array,
})

const search = ref(props.filters?.search || '')

const moods = [
    { name: 'Positive' },
    { name: 'Neutral' },
    { name: 'Dark' },
]

// Server-side search
watch(search, (value) => {
    router.get(
        route('quotes.index'),
        { search: value },
        { preserveState: true, replace: true }
    )
})

const showModal = ref(false)
const showEditModal = ref(false)
const showDeleteConfirmModal = ref(false)
const showCanvaModal = ref(false)
const selectedQuote = ref(null)
const quoteToDelete = ref(null)

const editQuote = (quote) => {
    selectedQuote.value = quote
    showEditModal.value = true
}

const designQuote = (quote) => {
    selectedQuote.value = quote
    showCanvaModal.value = true
}

const handleDesignSaved = (data) => {
    console.log('Design saved in Index.vue:', data);
    // Here you would typically handle the saved design data, e.g., upload to Laravel
    // For MVP, we are just logging it.
};

const confirmQuoteDeletion = (quote) => {
    quoteToDelete.value = quote
    showDeleteConfirmModal.value = true
}

const deleteQuote = () => {
    if (!quoteToDelete.value) return

    router.delete(route('quotes.destroy', quoteToDelete.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            showDeleteConfirmModal.value = false
            quoteToDelete.value = null
        },
    })
}

</script>

<template>

    <Head title="Quotes" />

    <AuthenticatedLayout>


        <section class="bg-gray-100 dark:bg-gray-900 p-4 sm:p-6">
            <div class="mx-auto max-w-screen-xl">
                <div class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-xl overflow-hidden border border-gray-100 dark:border-gray-700">

                    <!-- Search + Actions -->
                    <div class="flex flex-col md:flex-row items-center justify-between p-5 gap-4">
                        <div class="w-full md:w-1/3 relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <MagnifyingGlassIcon class="w-4 h-4 text-gray-500 dark:text-gray-400" />
                            </div>
                            <input v-model="search" type="text" placeholder="Search quotes..."
                                class="block w-full px-3 h-8 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" />
                        </div>

                        <div class="flex gap-2">
                            <button
                                class="flex items-center justify-center px-3 py-1.5 text-sm font-medium text-white rounded-lg bg-primary-600 hover:bg-primary-700 focus:ring-4 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800"
                                @click="showModal = true">
                                <PlusIcon class="h-3.5 w-3.5 mr-2" />
                                Generate New Quote
                            </button>
                        </div>
                    </div>

                    <!-- Quotes Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 p-5">
                        <div v-for="quote in quotes.data" :key="quote.id"
                            class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700 p-6 flex flex-col justify-between transition-all duration-200 hover:shadow-xl hover:bg-gray-50 dark:hover:bg-gray-700 relative cursor-pointer"
                            @click="editQuote(quote)">
                            <!-- Delete Icon -->
                            <button @click.stop="confirmQuoteDeletion(quote)" class="absolute top-3 right-3 text-gray-400 hover:text-red-600 dark:hover:text-red-500 transition-colors duration-200">
                                <TrashIcon class="h-5 w-5" />
                            </button>
                            <!-- Quote -->
                            <p class="text-gray-800 dark:text-gray-200 text-lg leading-relaxed mb-4 font-serif italic">
                                “{{ quote.quote }}”
                            </p>

                            <!-- Meta -->
                            <div class="flex items-center justify-between mt-auto pt-5 border-t border-gray-100 dark:border-gray-700">
                                <div class="text-xs font-medium space-x-2">
                                    <span class="px-3 py-1 rounded-full bg-primary-100 dark:bg-primary-700 text-primary-700 dark:text-primary-100">
                                        {{ quote.category }}
                                    </span>
                                    <span class="px-3 py-1 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                                        {{ quote.mood }}
                                    </span>
                                    <span class="px-3 py-1 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 capitalize">
                                        {{ quote.status }}
                                    </span>
                                </div>

                                <!-- Actions -->
                                <div class="flex gap-3 text-sm font-medium">
                                     <button @click.stop="designQuote(quote)"
                                         class="px-3 py-1.5 rounded-md text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors duration-200 text-sm">
                                         Design
                                     </button>
                                 </div>
                            </div>
                        </div>

                        <div v-if="quotes.data.length === 0" class="col-span-full text-center p-10 text-gray-500 dark:text-gray-400">
                            <p class="text-lg font-medium mb-2">No quotes found.</p>
                            <p>Generate a new quote to get started!</p>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <nav class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-3 md:space-y-0 p-4" aria-label="Table navigation">
                        <span class="text-sm font-normal text-gray-500 dark:text-gray-400">
                            Showing
                            <span class="font-semibold text-gray-900 dark:text-white">{{ quotes.from }}–{{ quotes.to }}</span>
                            of
                            <span class="font-semibold text-gray-900 dark:text-white">{{ quotes.total }}</span>
                        </span>

                        <ul class="inline-flex items-stretch -space-x-px rounded-md shadow-sm">
                            <li v-for="link in quotes.links" :key="link.label">
                                <button
                                    @click="router.visit(link.url, { preserveState: true })"
                                    :disabled="!link.url"
                                    v-html="link.label"
                                    class="flex items-center justify-center px-4 py-2 text-sm font-medium leading-tight transition-colors duration-200
                                    bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700
                                    dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white"
                                    :class="{
                                        'text-primary-600 bg-primary-50 border-primary-300 dark:bg-primary-700 dark:border-primary-600 dark:text-white': link.active,
                                        'rounded-l-md': link.label.includes('Previous'),
                                        'rounded-r-md': link.label.includes('Next'),
                                        'opacity-50 cursor-not-allowed': !link.url
                                    }"
                                />
                            </li>
                        </ul>
                    </nav>

                    <GenerateQuoteModal :show="showModal" @close="showModal = false" :categories="categories" />
                    <EditQuoteModal :show="showEditModal" :quote="selectedQuote" :categories="categories" :moods="moods"
                        @close="showEditModal = false" />
                    <CanvaDesignModal :show="showCanvaModal" :quoteText="selectedQuote ? selectedQuote.quote : ''" :canvaClientId="$page.props.canvaClientId" @close="showCanvaModal = false" @designSaved="handleDesignSaved" />
                    <DeleteConfirmationModal
                        :show="showDeleteConfirmModal"
                        title="Delete Quote"
                        message="Are you sure you want to delete this quote? This action cannot be undone."
                        @close="showDeleteConfirmModal = false"
                        @confirm="deleteQuote"
                    />

                </div>
            </div>
        </section>
    </AuthenticatedLayout>
</template>
