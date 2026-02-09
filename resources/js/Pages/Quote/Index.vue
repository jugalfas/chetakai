<script setup>
import EditQuoteModal from "@/Components/EditQuoteModal.vue";
import SchedulePostModal from "@/Components/SchedulePostModal.vue";
import DeleteConfirmationModal from "@/Components/DeleteConfirmationModal.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, router, Link } from "@inertiajs/vue3";
import { ref, watch, computed } from "vue";
import dayjs from "dayjs";
import relativeTime from "dayjs/plugin/relativeTime";
dayjs.extend(relativeTime);
import {
    TrashIcon,
    MagnifyingGlassIcon,
    PlusIcon,
    PencilIcon,
    CalendarDaysIcon,
    EyeIcon,
} from "@heroicons/vue/24/outline";
import Badge from "@/Components/Badge.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import { MoreVertical } from "lucide-vue-next";

const props = defineProps({
    quotes: Object,
    filters: Object,
    categories: Array,
    statusCounts: Object,
    allCategoriesCount: Number, // Added this
    canvaClientId: String,
});

const search = ref(props.filters?.search || "");
const currentStatus = ref(props.filters?.status || "all");
const currentCategory = ref(props.filters?.category || "");
const currentSort = ref(props.filters?.sort || "created_at");
const perPage = ref(props.filters?.per_page || 10);
const showFiltersDropdown = ref(false);

const updateQuotes = () => {
    router.get(
        route("quotes.index"),
        {
            search: search.value,
            status: currentStatus.value === "all" ? "" : currentStatus.value,
            category: currentCategory.value,
            sort: currentSort.value,
            per_page: perPage.value,
        },
        {
            preserveState: true,
            replace: true,
        }
    );
};

// Server-side filters
watch([search, currentStatus, currentCategory, currentSort, perPage], () => {
    updateQuotes();
});

// Computed status counts
const statusCounts = computed(() => {
    return props.statusCounts || {
        all: 0,
        published: 0,
        scheduled: 0,
        draft: 0
    };
});

// Computed category counts
const categoryCounts = computed(() => {
    const quotes = props.quotes.data;
    const counts = {};
    props.categories.forEach((category) => {
        counts[category.id] = category.quotes_count;
    });
    return counts;
});

// Computed filter description for empty state
const filterDescription = computed(() => {
    if (currentCategory.value) {
        const category = props.categories.find(c => c.id == currentCategory.value);
        return `in the ${category?.name || 'selected'} category`;
    } else if (currentStatus.value !== 'all') {
        return `${currentStatus.value} quotes`;
    } else {
        return '';
    }
});

const formatDate = (date) => {
    if (!date) return '';
    return dayjs(date).format('MMM D, YYYY [at] h:mm A');
};

const showEditModal = ref(false);
const showDeleteConfirmModal = ref(false);
const showScheduleModal = ref(false);
const selectedQuote = ref(null);
const quoteToDelete = ref(null);
const scheduleData = ref({});
const processing = ref(false);

// Initialize schedule data
watch(
    () => props.quotes.data,
    (quotes) => {
        quotes.forEach((quote) => {
            if (!scheduleData.value[quote.id]) {
                scheduleData.value[quote.id] = {
                    date: "",
                    time: "20:00",
                };
            }
        });
    },
    {
        immediate: true,
    }
);

const editQuote = (quote) => {
    selectedQuote.value = quote;
    showEditModal.value = true;
};

const confirmQuoteDeletion = (quote) => {
    quoteToDelete.value = quote;
    showDeleteConfirmModal.value = true;
};

const deleteQuote = () => {
    if (!quoteToDelete.value) return;

    router.delete(route("quotes.destroy", quoteToDelete.value.id), {
        preserveScroll: true,
        onBefore: () => processing.value = true,
        onFinish: () => processing.value = false,
        onSuccess: () => {
            showDeleteConfirmModal.value = false;
            quoteToDelete.value = null;
        },
    });
};

const scheduleQuote = (quote) => {
    selectedQuote.value = quote;
    showScheduleModal.value = true;
};
</script>

<template>

    <Head title="Quotes" />

    <AuthenticatedLayout>
        <template #title>Quotes</template>
        <template #description>Manage your inspirational quotes and social media content</template>

        <div class="space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight text-foreground">Quotes</h1>
                    <p class="text-muted-foreground mt-1">
                        Manage and organize your quotes collection.
                    </p>
                </div>
            </div>

            <div class="flex flex-col lg:flex-row gap-4 items-start lg:items-center justify-between">
                <div class="flex bg-muted p-1 rounded-lg border border-border overflow-x-auto max-w-full">
                    <button @click="currentStatus = 'all'" :class="[
                        'px-4 py-1.5 rounded-md text-sm font-medium transition-all whitespace-nowrap',
                        currentStatus === 'all'
                            ? 'bg-background text-foreground shadow-sm'
                            : 'text-muted-foreground hover:text-foreground'
                    ]">
                        All
                        <span class="opacity-50 ml-1">({{ statusCounts.all }})</span>
                    </button>
                    <button @click="currentStatus = 'posted'" :class="[
                        'px-4 py-1.5 rounded-md text-sm font-medium transition-all whitespace-nowrap',
                        currentStatus === 'posted'
                            ? 'bg-background text-foreground shadow-sm'
                            : 'text-muted-foreground hover:text-foreground'
                    ]">
                        Published
                        <span class="opacity-50 ml-1">({{ statusCounts.published }})</span>
                    </button>
                    <button @click="currentStatus = 'scheduled'" :class="[
                        'px-4 py-1.5 rounded-md text-sm font-medium transition-all whitespace-nowrap',
                        currentStatus === 'scheduled'
                            ? 'bg-background text-foreground shadow-sm'
                            : 'text-muted-foreground hover:text-foreground'
                    ]">
                        Scheduled
                        <span class="opacity-50 ml-1">({{ statusCounts.scheduled }})</span>
                    </button>
                    <button @click="currentStatus = 'draft'" :class="[
                        'px-4 py-1.5 rounded-md text-sm font-medium transition-all whitespace-nowrap',
                        currentStatus === 'draft'
                            ? 'bg-background text-foreground shadow-sm'
                            : 'text-muted-foreground hover:text-foreground'
                    ]">
                        Draft
                        <span class="opacity-50 ml-1">({{ statusCounts.draft }})</span>
                    </button>
                </div>
                <div class="relative flex items-center gap-2 w-full lg:w-auto">
                    <div class="relative flex-1 lg:w-64">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="lucide lucide-search absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground"
                            aria-hidden="true">
                            <path d="m21 21-4.34-4.34"></path>
                            <circle cx="11" cy="11" r="8"></circle>
                        </svg>
                        <input v-model="search"
                            class="flex w-full rounded-md border px-3 py-1 text-base shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50 md:text-sm pl-9 bg-card border-border focus:ring-accent h-9 text-foreground"
                            placeholder="Search quotes..." />
                    </div>
                    <button @click="showFiltersDropdown = !showFiltersDropdown"
                        class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 hover-elevate active-elevate-2 border shadow-xs active:shadow-none h-9 w-9 border-border bg-card text-foreground"
                        type="button">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="lucide lucide-funnel h-4 w-4" aria-hidden="true">
                            <path
                                d="M10 20a1 1 0 0 0 .553.895l2 1A1 1 0 0 0 14 21v-7a2 2 0 0 1 .517-1.341L21.74 4.67A1 1 0 0 0 21 3H3a1 1 0 0 0-.742 1.67l7.225 7.989A2 2 0 0 1 10 14z">
                            </path>
                        </svg>
                    </button>
                    <!-- Filters Dropdown -->
                    <div v-show="showFiltersDropdown"
                        class="z-50 max-h-[var(--radix-dropdown-menu-content-available-height)] min-w-[8rem] overflow-y-auto overflow-x-hidden rounded-md border p-1 shadow-md data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0 data-[state=closed]:zoom-out-95 data-[state=open]:zoom-in-95 data-[side=bottom]:slide-in-from-top-2 data-[side=left]:slide-in-from-right-2 data-[side=right]:slide-in-from-left-2 data-[side=top]:slide-in-from-bottom-2 origin-[--radix-dropdown-menu-content-transform-origin] w-56 bg-popover border-border text-popover-foreground absolute top-full right-0 mt-1">
                        <div class="px-2 py-1.5 text-sm font-semibold">Filter by Category</div>
                        <div role="separator" aria-orientation="horizontal" class="-mx-1 my-1 h-px bg-border"></div>
                        <div role="menuitem" @click="currentCategory = ''; showFiltersDropdown = false"
                            class="relative select-none gap-2 rounded-sm px-2 py-1.5 text-sm outline-none transition-colors focus:bg-accent focus:text-accent-foreground data-[disabled]:pointer-events-none data-[disabled]:opacity-50 [&amp;&gt;svg]:size-4 [&amp;&gt;svg]:shrink-0 flex items-center justify-between hover:bg-muted cursor-pointer"
                            :class="{ 'bg-accent/50 text-accent-foreground': currentCategory === '' }"
                            tabindex="-1" data-orientation="vertical" data-radix-collection-item="">All Categories <div
                                class="whitespace-nowrap inline-flex items-center rounded-md px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 hover-elevate text-foreground border ml-2 bg-muted border-border">
                                {{ props.allCategoriesCount }}</div>
                        </div>
                        <div v-for="category in props.categories" :key="category.id" role="menuitem" @click="currentCategory = category.id; showFiltersDropdown = false"
                            class="relative select-none gap-2 rounded-sm px-2 py-1.5 text-sm outline-none transition-colors focus:bg-accent focus:text-accent-foreground data-[disabled]:pointer-events-none data-[disabled]:opacity-50 [&amp;&gt;svg]:size-4 [&amp;&gt;svg]:shrink-0 flex items-center justify-between hover:bg-muted cursor-pointer"
                            :class="{ 'bg-accent/50 text-accent-foreground': currentCategory == category.id }"
                            tabindex="-1" data-orientation="vertical" data-radix-collection-item="">{{ category.name.charAt(0).toUpperCase() + category.name.slice(1) }} <div
                                class="whitespace-nowrap inline-flex items-center rounded-md px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 hover-elevate text-foreground border ml-2 bg-muted border-border">
                                {{ categoryCounts[category.id] || 0 }}</div>
                        </div>
                        <div role="separator" aria-orientation="horizontal" class="-mx-1 my-1 h-px bg-border"></div>
                        <div class="px-2 py-1.5 text-sm font-semibold">Sort by
                        </div>
                        <div role="menuitem" @click="currentSort = 'created_at'"
                            class="relative flex select-none items-center gap-2 rounded-sm px-2 py-1.5 text-sm outline-none transition-colors focus:bg-accent focus:text-accent-foreground data-[disabled]:pointer-events-none data-[disabled]:opacity-50 [&amp;&gt;svg]:size-4 [&amp;&gt;svg]:shrink-0 hover:bg-muted cursor-pointer"
                            tabindex="-1" data-orientation="vertical" data-radix-collection-item="">Newest first</div>
                        <div role="menuitem" @click="currentSort = 'updated_at'"
                            class="relative flex select-none items-center gap-2 rounded-sm px-2 py-1.5 text-sm outline-none transition-colors focus:bg-accent focus:text-accent-foreground data-[disabled]:pointer-events-none data-[disabled]:opacity-50 [&amp;&gt;svg]:size-4 [&amp;&gt;svg]:shrink-0 hover:bg-muted cursor-pointer"
                            tabindex="-1" data-orientation="vertical" data-radix-collection-item="">Recently updated</div>
                        <div role="menuitem" @click="currentSort = 'content'"
                            class="relative flex select-none items-center gap-2 rounded-sm px-2 py-1.5 text-sm outline-none transition-colors focus:bg-accent focus:text-accent-foreground data-[disabled]:pointer-events-none data-[disabled]:opacity-50 [&amp;&gt;svg]:size-4 [&amp;&gt;svg]:shrink-0 hover:bg-muted cursor-pointer"
                            tabindex="-1" data-orientation="vertical" data-radix-collection-item="">Alphabetical</div>
                    </div>
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                <!-- If no quote available -->
                <div v-if="props.quotes.data.length === 0"
                    class="col-span-full py-20 flex flex-col items-center justify-center text-center space-y-4">
                    <div class="h-20 w-20 rounded-full bg-muted/30 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="lucide lucide-quote h-10 w-10 text-muted-foreground/40" aria-hidden="true">
                            <path
                                d="M16 3a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2 1 1 0 0 1 1 1v1a2 2 0 0 1-2 2 1 1 0 0 0-1 1v2a1 1 0 0 0 1 1 6 6 0 0 0 6-6V5a2 2 0 0 0-2-2z">
                            </path>
                            <path
                                d="M5 3a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2 1 1 0 0 1 1 1v1a2 2 0 0 1-2 2 1 1 0 0 0-1 1v2a1 1 0 0 0 1 1 6 6 0 0 0 6-6V5a2 2 0 0 0-2-2z">
                            </path>
                        </svg>
                    </div>
                    <div class="space-y-1 mb-4">
                        <h3 class="text-xl font-bold text-foreground">No quotes found</h3>
                        <p class="text-muted-foreground max-w-xs mx-auto">
                            There are currently no {{ filterDescription ? filterDescription : 'quotes' }}.
                        </p>
                    </div>
                    <button
                        @click="search = ''; currentStatus = 'all'; currentCategory = ''; currentSort = 'created_at'"
                        class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 hover-elevate active-elevate-2 border shadow-xs active:shadow-none min-h-9 px-4 py-2 mt-4 border-accent/20 hover:bg-accent/10 text-accent font-bold">
                        Clear Filters
                    </button>
                </div>

                <!-- If quotes are available -->
                <div v-for="quote in props.quotes.data" :key="quote.id"
                    class="rounded-xl border text-card-foreground border-border bg-card hover:border-accent/50 transition-all group shadow-sm">
                    <div class="p-6 flex flex-col h-full min-h-[180px]">
                        <div class="flex-1">
                            <p class="text-base font-medium leading-relaxed italic text-foreground/90">"{{ quote.quote
                            }}"</p>
                        </div>
                        <div class="mt-6 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div
                                    class="whitespace-nowrap inline-flex items-center rounded-md py-0.5 transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 hover-elevate border capitalize text-[10px] h-5 bg-muted border-border text-primary font-bold px-2">
                                    {{ quote.category_relation?.name || 'Uncategorized' }}</div>
                                <div class="flex items-center gap-1.5 text-[10px] font-medium text-muted-foreground">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" :class="[
                                            'h-3 w-3',
                                            (!quote.post || quote.post.status === 'draft') ? 'text-amber-500' :
                                                quote.post.status === 'posted' ? 'text-green-500' :
                                                    quote.post.status === 'scheduled' ? 'text-blue-500' :
                                                        'text-gray-500'
                                        ]" aria-hidden="true">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <line v-if="!quote.post || quote.post.status === 'draft'" x1="12" x2="12" y1="8" y2="12"></line>
                                        <line v-if="!quote.post || quote.post.status === 'draft'" x1="12" x2="12.01" y1="16" y2="16"></line>
                                        <path v-if="quote.post?.status === 'posted'" d="m9 12 2 2 4-4"></path>
                                        <rect v-if="quote.post?.status === 'scheduled'" width="18" height="18" x="3" y="4"
                                            rx="2" ry="2"></rect>
                                        <line v-if="quote.post?.status === 'scheduled'" x1="16" x2="16" y1="2" y2="6"></line>
                                        <line v-if="quote.post?.status === 'scheduled'" x1="8" x2="8" y1="2" y2="6"></line>
                                        <line v-if="quote.post?.status === 'scheduled'" x1="3" x2="21" y1="10" y2="10"></line>
                                    </svg>
                                    <span class="capitalize">
                                        {{ quote.post?.status === 'posted' ? 'Published' : (quote.post?.status || 'Draft') }}
                                        {{ quote.post?.scheduled_at ? ' for ' + formatDate(quote.post.scheduled_at) : '' }}
                                    </span>
                                </div>
                            </div>
                            <div class="flex items-center gap-1">
                                <Dropdown align="right" width="48" :content-classes="'py-1 bg-[#041C32] border border-sidebar-border shadow-2xl relative z-[60]'">
                                    <template #trigger>
                                        <button
                                            class="inline-flex items-center justify-center gap-2 whitespace-nowrap transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 hover-elevate active-elevate-2 border border-transparent min-h-8 rounded-md px-3 h-7 text-[10px] font-bold uppercase tracking-widest text-muted-foreground hover:text-foreground">
                                            <MoreVertical class="h-3.5 w-3.5" />
                                        </button>
                                    </template>
                                    <template #content>
                                        <div class="p-1 flex flex-col min-w-[120px]">
                                            <button 
                                                @click.stop="editQuote(quote)"
                                                class="flex w-full px-3 py-2 text-start text-sm leading-5 text-white hover:bg-white/10 transition duration-150 ease-in-out focus:outline-none rounded-md"
                                            >
                                                Edit
                                            </button>
                                            <button 
                                                @click.stop="scheduleQuote(quote)"
                                                v-if="quote.post?.status !== 'posted'"
                                                class="flex w-full px-3 py-2 text-start text-sm leading-5 text-white hover:bg-white/10 transition duration-150 ease-in-out focus:outline-none rounded-md"
                                            >
                                                Schedule
                                            </button>
                                            <div class="h-px bg-sidebar-border my-1"></div>
                                            <button 
                                                @click.stop="confirmQuoteDeletion(quote)"
                                                class="flex w-full px-3 py-2 text-start text-sm leading-5 text-red-500 hover:bg-red-500/10 transition duration-150 ease-in-out focus:outline-none rounded-md font-medium"
                                            >
                                                Delete
                                            </button>
                                        </div>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pagination & Per Page -->
            <div class="mt-12 mb-8 flex flex-col md:flex-row items-center justify-between gap-6 border-t border-border/50 pt-8">
                <div class="flex flex-col sm:flex-row items-center gap-4 sm:gap-8">
                    <!-- Results Info -->
                    <div class="text-sm text-muted-foreground">
                        Showing <span class="font-medium text-foreground">{{ props.quotes.from || 0 }}</span> 
                        to <span class="font-medium text-foreground">{{ props.quotes.to || 0 }}</span> 
                        of <span class="font-medium text-foreground">{{ props.quotes.total }}</span> results
                    </div>

                    <!-- Per Page Dropdown -->
                    <div class="flex items-center gap-3">
                        <span class="text-sm text-muted-foreground whitespace-nowrap">Per page</span>
                        <Dropdown align="left" vertical-align="top" width="24" :content-classes="'py-1 bg-[#041C32] border border-sidebar-border shadow-2xl'">
                            <template #trigger>
                                <button
                                    class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 border border-border bg-card hover:bg-accent/10 h-9 px-3 min-w-[60px]"
                                >
                                    {{ perPage }}
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-up h-4 w-4 opacity-50"><path d="m18 15-6-6-6 6"/></svg>
                                </button>
                            </template>
                            <template #content>
                                <div class="p-1">
                                    <button v-for="option in [10, 25, 50, 100]" :key="option"
                                        @click="perPage = option"
                                        class="flex w-full px-3 py-2 text-start text-sm leading-5 text-white hover:bg-white/10 transition duration-150 ease-in-out focus:outline-none rounded-md"
                                        :class="{ 'bg-white/10 text-accent font-bold': perPage === option }"
                                    >
                                        {{ option }}
                                    </button>
                                </div>
                            </template>
                        </Dropdown>
                    </div>
                </div>

                <!-- Pagination Links -->
                <div v-if="props.quotes.links.length > 3" class="flex items-center">
                    <nav class="flex items-center gap-1.5" aria-label="Pagination">
                        <template v-for="(link, k) in props.quotes.links" :key="k">
                            <div v-if="link.url === null" 
                                class="inline-flex items-center justify-center px-3 h-9 text-sm text-muted-foreground/50 border border-border/50 rounded-md cursor-not-allowed opacity-50 bg-muted/20" 
                                v-html="link.label" />
                            <Link v-else 
                                :href="link.url" 
                                class="inline-flex items-center justify-center px-3 h-9 text-sm font-medium border border-border rounded-md hover:bg-accent/10 hover:border-accent/50 transition-all duration-200" 
                                :class="{ 
                                    'bg-accent text-accent-foreground border-accent shadow-sm shadow-accent/20': link.active,
                                    'text-foreground/80 hover:text-foreground': !link.active 
                                }" 
                                v-html="link.label" />
                        </template>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Modals -->
        <EditQuoteModal :show="showEditModal" :quote="selectedQuote" :categories="categories"
            @close="showEditModal = false" />
        <SchedulePostModal :show="showScheduleModal" :quote="selectedQuote" @close="showScheduleModal = false" />
        <DeleteConfirmationModal
            :show="showDeleteConfirmModal"
            :processing="processing"
            title="Delete Quote"
            message="Are you sure you want to delete this quote? This action cannot be undone."
            @close="showDeleteConfirmModal = false"
            @confirm="deleteQuote"
        />
    </AuthenticatedLayout>
</template>
