<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Head, router, Link } from "@inertiajs/vue3";
import { ref, watch } from "vue";
import {
    TrashIcon,
    MagnifyingGlassIcon,
    UserCircleIcon,
} from "@heroicons/vue/24/outline";
import TextInput from "@/Components/TextInput.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import DeleteConfirmationModal from "@/Components/DeleteConfirmationModal.vue";

const props = defineProps({
    users: Object,
    filters: Object,
});

const search = ref(props.filters?.search || "");
const showDeleteModal = ref(false);
const userToDelete = ref(null);
const processing = ref(false);

watch(search, (value) => {
    router.get(
        route("admin.users.index"),
        { search: value },
        {
            preserveState: true,
            replace: true,
        }
    );
});

const confirmDelete = (user) => {
    userToDelete.value = user;
    showDeleteModal.value = true;
};

const deleteUser = () => {
    if (userToDelete.value) {
        router.delete(route('admin.users.destroy', userToDelete.value.id), {
            onBefore: () => processing.value = true,
            onFinish: () => processing.value = false,
            onSuccess: () => {
                showDeleteModal.value = false;
                userToDelete.value = null;
            }
        });
    }
};
</script>

<template>
    <Head title="User Management" />

    <AdminLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">User Management</h2>
        </template>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Search and Filters -->
            <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div class="relative w-full sm:w-96">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <MagnifyingGlassIcon class="h-5 w-5 text-gray-400" />
                    </div>
                    <TextInput
                        v-model="search"
                        type="text"
                        class="block w-full pl-10 h-10"
                        placeholder="Search users..."
                    />
                </div>
            </div>

            <div class="bg-card overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-900/50 border-b border-gray-200 dark:border-gray-700">
                                <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500">User</th>
                                <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500">Email</th>
                                <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500">Instagram</th>
                                <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500">Joined</th>
                                <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            <tr v-for="user in users.data" :key="user.id" class="hover:bg-gray-50 dark:hover:bg-gray-900/40 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 flex-shrink-0">
                                            <UserCircleIcon class="h-10 w-10 text-gray-400" />
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ user.first_name }} {{ user.last_name }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                    {{ user.email }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div v-if="user.instagram_username" class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300">
                                            @{{ user.instagram_username }}
                                        </span>
                                    </div>
                                    <span v-else class="text-xs text-gray-400 italic">Not connected</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                    {{ new Date(user.created_at).toLocaleDateString() }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button 
                                        @click="confirmDelete(user)"
                                        class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 ml-3"
                                    >
                                        <TrashIcon class="h-5 w-5" />
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="users.data.length === 0">
                                <td colspan="5" class="px-6 py-10 text-center text-gray-500 italic">
                                    No users found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="users.links.length > 3" class="px-6 py-4 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700 flex justify-center">
                    <nav class="flex items-center gap-1">
                        <Link
                            v-for="(link, k) in users.links"
                            :key="k"
                            :href="link.url || '#'"
                            v-html="link.label"
                            class="px-3 py-1 text-sm rounded-md transition-colors"
                            :class="[
                                link.active 
                                    ? 'bg-primary text-white font-semibold' 
                                    : 'text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700',
                                !link.url ? 'opacity-50 cursor-not-allowed' : ''
                            ]"
                        />
                    </nav>
                </div>
            </div>
        </div>

        <DeleteConfirmationModal
            :show="showDeleteModal"
            :processing="processing"
            title="Delete User"
            message="Are you sure you want to delete this user? This action cannot be undone."
            @close="showDeleteModal = false"
            @confirm="deleteUser"
        />
    </AdminLayout>
</template>
