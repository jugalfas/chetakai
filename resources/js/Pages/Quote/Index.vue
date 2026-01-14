<script setup>
import EditQuoteModal from '@/Components/EditQuoteModal.vue'
import SchedulePostModal from '@/Components/SchedulePostModal.vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import TextInput from '@/Components/TextInput.vue'
import { Head, router } from '@inertiajs/vue3'
import { ref, watch } from 'vue'
import {
    TrashIcon,
    MagnifyingGlassIcon,
    PlusIcon,
    PencilIcon,
    CalendarDaysIcon,
    EyeIcon
} from '@heroicons/vue/24/outline'
import Badge from '@/Components/Badge.vue'

const props = defineProps({
    quotes: Object,
    filters: Object,
    categories: Array,
})

const search = ref(props.filters?.search || '')

// Server-side search
watch(search, (value) => {
    router.get(
        route('quotes.index'),
        { search: value },
        { preserveState: true, replace: true }
    )
})

const showEditModal = ref(false)
const showDeleteConfirmModal = ref(false)
const showScheduleModal = ref(false)
const selectedQuote = ref(null)
const quoteToDelete = ref(null)
const scheduleData = ref({})

// Initialize schedule data
watch(() => props.quotes.data, (quotes) => {
    quotes.forEach(quote => {
        if (!scheduleData.value[quote.id]) {
            scheduleData.value[quote.id] = { date: '', time: '20:00' }
        }
    })
}, { immediate: true })

const editQuote = (quote) => {
    selectedQuote.value = quote
    showEditModal.value = true
}

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

const scheduleQuote = (quote) => {
    selectedQuote.value = quote
    showScheduleModal.value = true
}
</script>

<template>
    <Head title="Quotes" />

    <AuthenticatedLayout>
        <template #title>Quotes</template>
        <template #description>Manage your inspirational quotes and social media content</template>

        <template #actions>
            <PrimaryButton @click="router.visit(route('quotes.create'))">
                <PlusIcon class="-ml-0.5 mr-1.5 h-5 w-5" />
                New Quote
            </PrimaryButton>
        </template>

        <!-- Search and Filters -->
        <div class="bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-200 dark:ring-gray-700 rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex-1 min-w-0">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <MagnifyingGlassIcon class="h-5 w-5 text-gray-400" />
                            </div>
                            <TextInput
                                v-model="search"
                                placeholder="Search quotes..."
                                class="pl-10"
                            />
                        </div>
                    </div>
                    <div class="mt-4 sm:mt-0 sm:ml-4">
                        <SecondaryButton>
                            <svg class="-ml-0.5 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4" />
                            </svg>
                            Filters
                        </SecondaryButton>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quotes Grid -->
        <div class="mt-8">
            <div v-if="quotes.data.length === 0" class="text-center py-12">
                <SparklesIcon class="mx-auto h-12 w-12 text-gray-400" />
                <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">No quotes</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Get started by creating your first inspirational quote.
                </p>
                <div class="mt-6">
                    <PrimaryButton @click="router.visit(route('quotes.create'))">
                        <PlusIcon class="-ml-0.5 mr-1.5 h-5 w-5" />
                        New Quote
                    </PrimaryButton>
                </div>
            </div>

            <div v-else class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <div
                    v-for="quote in quotes.data"
                    :key="quote.id"
                    class="bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-200 dark:ring-gray-700 rounded-lg overflow-hidden hover:shadow-md transition-shadow duration-200"
                >
                    <!-- Card Header -->
                    <div class="px-4 py-5 sm:p-6">
                        <div class="flex items-start justify-between">
                            <div class="flex-1 min-w-0">
                                <p class="text-lg font-serif italic text-gray-900 dark:text-white leading-relaxed">
                                    "{{ quote.quote }}"
                                </p>
                                <p v-if="quote.post?.caption" class="mt-3 text-sm text-gray-600 dark:text-gray-400 font-serif italic">
                                    {{ quote.post.caption }}
                                </p>
                            </div>
                            <button
                                @click="confirmQuoteDeletion(quote)"
                                class="ml-4 p-1 rounded-full text-gray-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors duration-200"
                            >
                                <TrashIcon class="h-5 w-5" />
                            </button>
                        </div>

                        <!-- Meta Information -->
                        <div class="mt-4 flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <Badge variant="primary">{{ quote.category }}</Badge>
                                <Badge
                                    :variant="quote.status === 'published' ? 'success' : quote.status === 'draft' ? 'warning' : 'secondary'"
                                    class="capitalize"
                                >
                                    {{ quote.status }}
                                </Badge>
                            </div>
                        </div>
                    </div>

                    <!-- Card Footer -->
                    <div class="bg-gray-50 dark:bg-gray-700/50 px-4 py-3 sm:px-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <SecondaryButton size="sm" @click="editQuote(quote)">
                                    <PencilIcon class="h-4 w-4" />
                                    <span class="sr-only sm:not-sr-only sm:ml-1">Edit</span>
                                </SecondaryButton>
                                <SecondaryButton size="sm" @click="scheduleQuote(quote)" :disabled="!quote.post?.image_path">
                                    <CalendarDaysIcon class="h-4 w-4" />
                                    <span class="sr-only sm:not-sr-only sm:ml-1">
                                        {{ quote.post?.status === 'scheduled' ? 'Reschedule' : 'Schedule' }}
                                    </span>
                                </SecondaryButton>
                            </div>

                            <!-- Scheduled Info -->
                            <div v-if="quote.post?.status === 'scheduled'" class="text-xs text-gray-500 dark:text-gray-400">
                                <div class="flex items-center">
                                    <CalendarDaysIcon class="h-4 w-4 mr-1" />
                                    {{ new Date(quote.post.scheduled_at).toLocaleDateString() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div v-if="quotes.data.length > 0" class="mt-8">
            <nav class="flex items-center justify-between border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-3 sm:px-6 rounded-lg shadow-sm ring-1 ring-gray-200 dark:ring-gray-700">
                <div class="flex flex-1 justify-between sm:hidden">
                    <SecondaryButton
                        v-if="quotes.prev_page_url"
                        @click="router.visit(quotes.prev_page_url, { preserveState: true })"
                        size="sm"
                    >
                        Previous
                    </SecondaryButton>
                    <SecondaryButton
                        v-if="quotes.next_page_url"
                        @click="router.visit(quotes.next_page_url, { preserveState: true })"
                        size="sm"
                    >
                        Next
                    </SecondaryButton>
                </div>
                <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700 dark:text-gray-300">
                            Showing
                            <span class="font-medium">{{ quotes.from }}</span>
                            to
                            <span class="font-medium">{{ quotes.to }}</span>
                            of
                            <span class="font-medium">{{ quotes.total }}</span>
                            results
                        </p>
                    </div>
                    <div>
                        <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                            <SecondaryButton
                                v-for="link in quotes.links"
                                :key="link.label"
                                :disabled="!link.url"
                                @click="link.url && router.visit(link.url, { preserveState: true })"
                                :class="[
                                    'relative inline-flex items-center px-3 py-2 text-sm font-semibold ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 focus:z-20 focus:outline-offset-0',
                                    link.active
                                        ? 'bg-primary-600 text-white ring-primary-600 hover:bg-primary-700'
                                        : 'text-gray-900 dark:text-gray-300',
                                    link.label === 'Previous' ? 'rounded-l-md' : '',
                                    link.label === 'Next' ? 'rounded-r-md' : '',
                                    !link.url ? 'opacity-50 cursor-not-allowed' : ''
                                ]"
                                v-html="link.label"
                            />
                        </nav>
                    </div>
                </div>
            </nav>
        </div>

        <!-- Modals -->
        <EditQuoteModal :show="showEditModal" :quote="selectedQuote" :categories="categories"
            @close="showEditModal = false" />
        <SchedulePostModal :show="showScheduleModal" :quote="selectedQuote"
            @close="showScheduleModal = false" />
        <DeleteConfirmationModal :show="showDeleteConfirmModal" title="Delete Quote"
            message="Are you sure you want to delete this quote? This action cannot be undone."
            @close="showDeleteConfirmModal = false" @confirm="deleteQuote" />
    </AuthenticatedLayout>
</template>
