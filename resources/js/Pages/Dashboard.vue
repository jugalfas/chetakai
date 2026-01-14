<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import {
    SparklesIcon,
    CalendarDaysIcon,
    ArrowTrendingUpIcon,
    ArrowTrendingDownIcon,
    ShareIcon
} from '@heroicons/vue/24/outline';

// Mock data - in a real app, this would come from props
const stats = [
    {
        name: 'Total Quotes',
        value: '2,847',
        change: '+12.5%',
        changeType: 'increase',
        icon: SparklesIcon,
    },
    {
        name: 'Scheduled Posts',
        value: '156',
        change: '-4.3%',
        changeType: 'decrease',
        icon: CalendarDaysIcon,
    },
    {
        name: 'Posted on Instagram',
        value: '78',
        change: '+5.0%',
        changeType: 'increase',
        icon: ShareIcon,
    },
];

const recentActivity = [
    {
        id: 1,
        type: 'quote_created',
        message: 'New quote "Success is not final..." created',
        time: '2 minutes ago',
    },
    {
        id: 2,
        type: 'post_scheduled',
        message: 'Post scheduled for tomorrow at 8:00 AM',
        time: '15 minutes ago',
    },
    {
        id: 3,
        type: 'category_added',
        message: 'New category "Motivation" added',
        time: '1 hour ago',
    },
    {
        id: 4,
        type: 'quote_edited',
        message: 'Quote #1234 updated',
        time: '2 hours ago',
    },
];

const categoryInsights = [
    { name: 'Motivation', count: 120, percentage: 30, color: 'bg-blue-500' },
    { name: 'Inspiration', count: 90, percentage: 22.5, color: 'bg-green-500' },
    { name: 'Success', count: 70, percentage: 17.5, color: 'bg-purple-500' },
    { name: 'Life', count: 60, percentage: 15, color: 'bg-yellow-500' },
    { name: 'Wisdom', count: 40, percentage: 10, color: 'bg-red-500' },
];
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-3xl font-semibold text-white">Overview</h1>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <div v-for="stat in stats"
                :key="stat.name"
                 class="rounded-xl border text-card-foreground border-border bg-card shadow-sm card-shadow">
                <div class="space-y-1.5 p-6 flex flex-row items-center justify-between pb-2">
                    <div class="tracking-tight text-sm font-medium text-muted-foreground">
                        {{ stat.name }}
                    </div>
                    <div class="p-2 rounded-lg bg-primary/10">
                        <component :is="stat.icon" class="h-4 w-4 text-primary" aria-hidden="true" />
                    </div>
                </div>
                <div class="p-6 pt-0">
                    <div class="text-3xl font-bold">{{ stat.value }}</div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <!-- Recent Activity -->
            <div class="mt-8 col-span-2">
                <div class="bg-card shadow-sm ring-1 ring-border rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-base font-semibold leading-6 text-white">
                            Recent Activity
                        </h3>
                        <div class="mt-6 flow-root">
                            <ul role="list" class="-mb-8">
                                <li v-for="(activity, activityIdx) in recentActivity" :key="activity.id">
                                    <div class="relative pb-8">
                                        <span
                                            v-if="activityIdx !== recentActivity.length - 1"
                                            class="absolute left-4 top-4 -ml-px h-full w-0.5 bg-gray-700"
                                            aria-hidden="true"
                                        />
                                        <div class="relative flex space-x-3">
                                            <div>
                                                <span
                                                    class="h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-background"
                                                    :class="{
                                                        'bg-blue-500': activity.type === 'quote_created',
                                                        'bg-green-500': activity.type === 'post_scheduled',
                                                        'bg-purple-500': activity.type === 'category_added',
                                                        'bg-yellow-500': activity.type === 'quote_edited',
                                                    }"
                                                >
                                                    <component
                                                        :is="activity.type === 'quote_created' ? SparklesIcon :
                                                             activity.type === 'post_scheduled' ? CalendarDaysIcon :
                                                             activity.type === 'category_added' ? TagIcon :
                                                             ChatBubbleLeftRightIcon"
                                                        class="h-5 w-5 text-white"
                                                        aria-hidden="true"
                                                    />
                                                </span>
                                            </div>
                                            <div class="min-w-0 flex-1 pt-1.5">
                                                <p class="text-sm text-gray-400">
                                                    {{ activity.message }}
                                                    <span class="whitespace-nowrap">{{ activity.time }}</span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
    
            <!-- Category Insights -->
            <div class="mt-8">
                <div class="bg-card shadow-sm ring-1 ring-border rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-base font-semibold leading-6 text-white">
                            Category Insights
                        </h3>
                        <div class="mt-6">
                            <ul role="list" class="divide-y divide-border">
                                <li v-for="category in categoryInsights" :key="category.name" class="flex items-center justify-between py-3">
                                    <div class="flex items-center">
                                        <span :class="['h-3 w-3 rounded-full mr-3', category.color]"></span>
                                        <p class="text-sm font-medium text-gray-300">{{ category.name }}</p>
                                    </div>
                                    <p class="text-sm text-gray-400">{{ category.count }} quotes ({{ category.percentage }}%)</p>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>            </div>
        </div>
    </AuthenticatedLayout>
</template>

