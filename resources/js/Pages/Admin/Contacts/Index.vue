<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import DeleteConfirmationModal from "@/Components/DeleteConfirmationModal.vue";
import RichTextEditor from "@/Components/RichTextEditor.vue";
import Modal from "@/Components/Modal.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import { Head, router, Link, usePage, useForm } from "@inertiajs/vue3";
import { ref, watch } from "vue";
import {
    TrashIcon,
    EnvelopeIcon,
    CalendarIcon,
    MagnifyingGlassIcon,
    CheckCircleIcon,
    ArrowPathIcon,
    ChatBubbleLeftEllipsisIcon,
    PaperAirplaneIcon,
    ClockIcon,
} from "@heroicons/vue/24/outline";
import { toast } from 'vue-sonner';

const props = defineProps({
    contacts: Object,
    filters: Object,
});

const page = usePage();
const search = ref(props.filters.search || "");
const showDeleteModal = ref(false);
const showReplyModal = ref(false);
const contactToDelete = ref(null);
const contactToReply = ref(null);
const processing = ref(false);
const markingAllRead = ref(false);

const replyForm = useForm({
    message: '',
});

const openDelete = (contact) => {
    contactToDelete.value = contact;
    showDeleteModal.value = true;
};

const openReply = (contact) => {
    contactToReply.value = contact;
    replyForm.message = contact.reply_content || '';
    showReplyModal.value = true;
};

const deleteContact = () => {
    if (contactToDelete.value) {
        router.delete(route('admin.contacts.destroy', contactToDelete.value.id), {
            onBefore: () => processing.value = true,
            onFinish: () => processing.value = false,
            onSuccess: () => {
                showDeleteModal.value = false;
                contactToDelete.value = null;
                toast.success('Message deleted successfully');
            }
        });
    }
};

const sendReply = () => {
    replyForm.post(route('admin.contacts.reply', contactToReply.value.id), {
        onSuccess: () => {
            showReplyModal.value = false;
            replyForm.reset();
            toast.success('Reply sent successfully');
        }
    });
};

const markAsRead = (contact) => {
    if (!contact.is_read) {
        router.patch(route('admin.contacts.mark-as-read', contact.id), {}, {
            preserveScroll: true,
        });
    }
};

const markAllAsRead = () => {
    router.patch(route('admin.contacts.mark-all-as-read'), {}, {
        onBefore: () => markingAllRead.value = true,
        onFinish: () => markingAllRead.value = false,
        onSuccess: () => {
            toast.success('All messages marked as read');
        }
    });
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const getInitials = (name) => {
    return name
        .split(' ')
        .map(n => n[0])
        .join('')
        .toUpperCase()
        .substring(0, 2);
};

// Simple debounce implementation to avoid lodash dependency
const localDebounce = (fn, delay) => {
    let timeoutId;
    return (...args) => {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => fn(...args), delay);
    };
};

watch(
    search,
    localDebounce((value) => {
        router.get(
            route("admin.contacts.index"),
            { search: value },
            { preserveState: true, replace: true }
        );
    }, 300)
);
</script>

<template>
    <Head title="Contact Messages" />

    <AdminLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-foreground leading-tight">
                    Messages
                </h2>
                <div class="flex items-center gap-4">
                    <button 
                        v-if="page.props.auth.unreadMessagesCount > 0"
                        @click="markAllAsRead"
                        :disabled="markingAllRead"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-accent hover:bg-accent/10 rounded-lg transition-colors disabled:opacity-50"
                    >
                        <ArrowPathIcon v-if="markingAllRead" class="h-4 w-4 animate-spin" />
                        <CheckCircleIcon v-else class="h-4 w-4" />
                        Mark all as read
                    </button>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <!-- Search and Filters -->
                <div class="flex items-center gap-4 bg-card border border-border p-4 rounded-xl shadow-sm">
                    <div class="relative flex-1 max-w-md">
                        <MagnifyingGlassIcon class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-muted-foreground" />
                        <input 
                            v-model="search"
                            type="text" 
                            placeholder="Search messages by name, email or content..."
                            class="w-full pl-10 pr-4 py-2 bg-background border border-border rounded-lg focus:ring-2 focus:ring-accent focus:border-accent transition-all text-sm"
                        >
                    </div>
                </div>

                <!-- Messages List -->
                <div class="space-y-4">
                    <div v-if="contacts.data.length === 0" class="bg-card border border-border rounded-xl p-12 text-center shadow-sm">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-muted mb-4">
                            <EnvelopeIcon class="h-8 w-8 text-muted-foreground opacity-50" />
                        </div>
                        <h3 class="text-lg font-semibold text-foreground">No messages found</h3>
                        <p class="text-muted-foreground max-w-xs mx-auto mt-2">
                            {{ search ? 'Try adjusting your search terms to find what you\'re looking for.' : 'When someone contacts you, their message will appear here.' }}
                        </p>
                    </div>

                    <div v-else class="space-y-3">
                        <div v-for="contact in contacts.data" :key="contact.id" 
                            class="bg-card border border-border rounded-xl p-5 transition-all hover:shadow-md hover:border-accent/30 group relative"
                            :class="{ 'border-l-4 border-l-accent ring-1 ring-accent/5': !contact.is_read }"
                            @mouseenter="markAsRead(contact)"
                        >
                            <div class="flex items-start gap-5">
                                <!-- Avatar -->
                                <div class="hidden sm:flex shrink-0 w-12 h-12 rounded-full bg-accent/10 items-center justify-center text-accent font-bold text-lg">
                                    {{ getInitials(contact.name) }}
                                </div>

                                <!-- Content -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex flex-wrap items-center justify-between gap-x-4 gap-y-1 mb-2">
                                        <div class="flex items-center gap-3">
                                            <h4 class="font-bold text-foreground truncate max-w-[200px]">
                                                {{ contact.name }}
                                            </h4>
                                            <span v-if="!contact.is_read" class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-accent text-accent-foreground">
                                                New
                                            </span>
                                            <span v-if="contact.reply_sent_at" class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-emerald-500/10 text-emerald-500">
                                                <CheckCircleIcon class="h-3 w-3" />
                                                Replied
                                            </span>
                                        </div>
                                        <div class="flex items-center gap-4 text-xs text-muted-foreground">
                                            <span class="flex items-center gap-1.5">
                                                <CalendarIcon class="h-3.5 w-3.5" />
                                                {{ formatDate(contact.created_at) }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="flex flex-col gap-1 mb-3">
                                        <a :href="`mailto:${contact.email}`" class="text-xs text-accent hover:underline flex items-center gap-1.5 w-fit">
                                            <EnvelopeIcon class="h-3.5 w-3.5" />
                                            {{ contact.email }}
                                        </a>
                                    </div>

                                    <div class="relative mb-4">
                                        <p class="text-sm text-foreground/80 leading-relaxed whitespace-pre-wrap break-words">
                                            {{ contact.message }}
                                        </p>
                                    </div>

                                    <!-- Reply preview if exists -->
                                    <div v-if="contact.reply_sent_at" class="mt-4 p-4 rounded-lg bg-muted/30 border border-border/50">
                                        <div class="flex items-center gap-2 mb-2 text-xs font-semibold text-muted-foreground">
                                            <ChatBubbleLeftEllipsisIcon class="h-3.5 w-3.5" />
                                            Your Reply ({{ formatDate(contact.reply_sent_at) }})
                                        </div>
                                        <div class="text-sm text-foreground/70 prose prose-sm dark:prose-invert max-w-none" v-html="contact.reply_content"></div>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="flex flex-col gap-2 shrink-0">
                                    <button 
                                        @click="openReply(contact)"
                                        class="p-2 text-muted-foreground hover:text-accent hover:bg-accent/10 rounded-lg transition-all"
                                        :class="contact.reply_sent_at ? 'opacity-100' : 'opacity-0 group-hover:opacity-100'"
                                        :title="contact.reply_sent_at ? 'Edit reply' : 'Reply to message'"
                                    >
                                        <ChatBubbleLeftEllipsisIcon class="h-5 w-5" />
                                    </button>
                                    <button 
                                        @click="openDelete(contact)"
                                        class="p-2 text-muted-foreground hover:text-red-500 hover:bg-red-500/10 rounded-lg transition-all opacity-0 group-hover:opacity-100"
                                        title="Delete message"
                                    >
                                        <TrashIcon class="h-5 w-5" />
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Pagination -->
                        <div v-if="contacts.links.length > 3" class="pt-6 flex flex-col sm:flex-row items-center justify-between gap-4">
                            <p class="text-sm text-muted-foreground">
                                Showing {{ contacts.from }} to {{ contacts.to }} of {{ contacts.total }} messages
                            </p>
                            <nav class="flex items-center gap-1">
                                <template v-for="(link, k) in contacts.links" :key="k">
                                    <div v-if="link.url === null" 
                                        class="px-4 py-2 text-sm text-muted-foreground border border-border rounded-lg opacity-50" 
                                        v-html="link.label" />
                                    <Link v-else 
                                        :href="link.url" 
                                        class="px-4 py-2 text-sm border border-border rounded-lg hover:bg-accent/10 hover:border-accent/30 transition-all" 
                                        :class="{ 'bg-accent text-accent-foreground border-accent': link.active }" 
                                        v-html="link.label" />
                                </template>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reply Modal -->
        <Modal :show="showReplyModal" @close="showReplyModal = false" max-width="2xl">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-foreground">Reply to {{ contactToReply?.name }}</h3>
                        <p class="text-sm text-muted-foreground">{{ contactToReply?.email }}</p>
                    </div>
                    <button @click="showReplyModal = false" class="text-muted-foreground hover:text-foreground">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Original Message Quote -->
                <div class="mb-6 p-4 rounded-xl bg-muted/30 border border-border/50 text-sm">
                    <div class="font-semibold text-muted-foreground mb-1 flex items-center gap-1.5">
                        <ClockIcon class="h-3.5 w-3.5" />
                        Original Message:
                    </div>
                    <p class="text-foreground/70 italic">{{ contactToReply?.message }}</p>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-2">Your Reply</label>
                        <RichTextEditor 
                            v-model="replyForm.message" 
                            placeholder="Type your detailed reply here..."
                        />
                        <div v-if="replyForm.errors.message" class="mt-1 text-sm text-red-500">
                            {{ replyForm.errors.message }}
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-4">
                        <SecondaryButton @click="showReplyModal = false" :disabled="replyForm.processing">
                            Cancel
                        </SecondaryButton>
                        <PrimaryButton 
                            @click="sendReply" 
                            :disabled="replyForm.processing"
                            class="gap-2"
                        >
                            <PaperAirplaneIcon v-if="!replyForm.processing" class="h-4 w-4" />
                            <ArrowPathIcon v-else class="h-4 w-4 animate-spin" />
                            {{ contactToReply?.reply_sent_at ? 'Update Reply' : 'Send Reply' }}
                        </PrimaryButton>
                    </div>
                </div>
            </div>
        </Modal>

        <DeleteConfirmationModal
            :show="showDeleteModal"
            :processing="processing"
            title="Delete Message"
            message="Are you sure you want to delete this message? This action cannot be undone."
            @close="showDeleteModal = false"
            @confirm="deleteContact"
        />
    </AdminLayout>
</template>

<style scoped>
.group {
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}
</style>