<script setup>
import { ref, nextTick, watch, onMounted } from 'vue'
import axios from 'axios'
import { Head, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Send, Plus, Sparkles, MessageSquare, User, Bot } from 'lucide-vue-next'
import { marked } from 'marked'

// Configure marked for markdown parsing
marked.setOptions({
    breaks: true, // Convert line breaks to <br>
    gfm: true, // GitHub Flavored Markdown
})

const props = defineProps({
    conversations: {
        type: Array,
        default: () => []
    },
    selectedConversation: {
        type: Object,
        default: null
    },
    messages: {
        type: Array,
        default: () => []
    }
})

const messages = ref(props.messages || [])
const input = ref('')
const selectedConversationId = ref(props.selectedConversation?.id || null)
const loading = ref(false)
const conversations = ref(props.conversations || [])

// Watch for prop changes (when navigating)
watch(() => props.selectedConversation, (newConv) => {
    selectedConversationId.value = newConv?.id || null
}, { immediate: true })

watch(() => props.messages, (newMessages) => {
    messages.value = newMessages || []
    nextTick(() => scrollToBottom())
}, { immediate: true })

watch(() => props.conversations, (newConvs) => {
    conversations.value = newConvs || []
}, { immediate: true })

const selectConversation = async (conversationId) => {
    if (selectedConversationId.value === conversationId) return

    router.get('/chat', { conversation_id: conversationId }, {
        preserveState: true,
        preserveScroll: true,
    })
}

const createNewChat = async () => {
    try {
        const response = await axios.post('/chat', {
            title: 'New Chat'
        })

        // Refresh conversations list
        router.reload({
            only: ['conversations'],
            preserveState: true,
        })

        // Select the new conversation
        if (response.data.conversation) {
            selectConversation(response.data.conversation.id)
        }
    } catch (e) {
        console.error('Error creating new chat:', e)
    }
}

const sendMessage = async () => {
    if (!input.value.trim() || loading.value) return

    const userMessage = input.value

    // Add user message to UI immediately
    messages.value.push({
        id: Date.now(), // Temporary ID
        role: 'user',
        content: userMessage,
        created_at: new Date().toISOString()
    })

    input.value = ''
    loading.value = true

    await nextTick()
    scrollToBottom()

    try {
        const response = await axios.post('/chat/send', {
            message: userMessage,
            conversation_id: selectedConversationId.value,
        })

        // Add assistant response
        messages.value.push({
            id: Date.now() + 1, // Temporary ID
            role: 'assistant',
            content: response.data.reply,
            created_at: new Date().toISOString()
        })

        // Update conversation ID if it was a new conversation
        if (response.data.conversation_id && !selectedConversationId.value) {
            selectedConversationId.value = response.data.conversation_id
            // Reload to get updated conversations list
            router.reload({
                only: ['conversations', 'selectedConversation', 'messages'],
                preserveState: false,
            })
        } else {
            // Just reload messages for current conversation
            router.reload({
                only: ['messages'],
                preserveState: true,
            })
        }
    } catch (e) {
        console.error('Error sending message:', e)
        messages.value.push({
            id: Date.now() + 1,
            role: 'assistant',
            content: 'Something broke 💀 Try again.',
            created_at: new Date().toISOString()
        })
    } finally {
        loading.value = false
        await nextTick()
        scrollToBottom()
    }
}

const scrollToBottom = () => {
    const el = document.getElementById('chat-body')
    if (el) el.scrollTop = el.scrollHeight
}

const parseMarkdown = (text) => {
    if (!text) return ''
    return marked.parse(text)
}

onMounted(() => {
    scrollToBottom()
})
</script>

<template>
    <Head title="AI Chat" />

    <AuthenticatedLayout>
        <div class="h-[calc(100vh-12rem)] flex gap-4">
            <!-- Sidebar - History -->
            <div class="w-64 hidden lg:flex flex-col gap-4">
                <button
                    @click="createNewChat"
                    class="inline-flex items-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 hover-elevate active-elevate-2 border shadow-xs active:shadow-none min-h-9 px-4 py-2 w-full gap-2 border-sidebar-border bg-sidebar hover:bg-sidebar-accent justify-start h-11"
                >
                    <Plus class="h-4 w-4" />
                    New Chat
                </button>
                <div class="flex-1 overflow-y-auto">
                    <div class="space-y-2">
                        <button
                            v-for="conversation in conversations"
                            :key="conversation.id"
                            @click="selectConversation(conversation.id)"
                            :class="[
                                'w-full justify-start font-normal text-muted-foreground hover:text-foreground hover:bg-sidebar-accent px-3 py-2 h-auto text-sm rounded-md flex items-center transition-colors',
                                selectedConversationId === conversation.id ? 'bg-sidebar-accent text-foreground' : ''
                            ]"
                        >
                            <MessageSquare class="h-4 w-4 mr-2 flex-shrink-0" />
                            <span class="truncate flex-1">{{ conversation.title || 'Untitled Chat' }}</span>
                            <span v-if="conversation.messages_count" class="text-xs text-muted-foreground ml-2">
                                {{ conversation.messages_count }}
                            </span>
                        </button>
                        <p v-if="conversations.length === 0" class="text-sm text-muted-foreground px-3 py-2 text-center">
                            No conversations yet. Start a new chat!
                        </p>
                    </div>
                </div>
            </div>

            <!-- Main Chat Area -->
            <div class="flex-1 flex flex-col bg-card border border-border rounded-xl overflow-hidden shadow-sm card-shadow">
                <div id="chat-body" class="flex-1 overflow-y-auto p-4 sm:p-6">
                    <div class="max-w-3xl mx-auto space-y-8">
                        <div v-if="messages.length === 0" class="flex flex-col items-center justify-center py-20 text-center animate-in fade-in zoom-in duration-500">
                            <div class="h-12 w-12 rounded-2xl bg-accent/20 flex items-center justify-center mb-6">
                                <Sparkles class="h-6 w-6 text-accent" />
                            </div>
                            <h2 class="text-2xl font-bold mb-2">How can I help you today?</h2>
                            <p class="text-muted-foreground max-w-sm">
                                Start a conversation by typing a message below. I'm here to assist with questions, writing, analysis, and more.
                            </p>
                        </div>

                        <div
                            v-for="msg in messages"
                            :key="msg.id || msg.created_at"
                            class="flex gap-4"
                            :class="msg.role === 'user' ? 'flex-row-reverse' : 'flex-row'"
                        >
                            <div
                                class="h-8 w-8 rounded-lg flex items-center justify-center flex-shrink-0 border border-sidebar-border"
                                :class="msg.role === 'user' ? 'bg-sidebar-accent' : 'bg-primary/20'"
                            >
                                <User v-if="msg.role === 'user'" class="h-4 w-4" />
                                <Bot v-else class="h-4 w-4 text-primary" />
                            </div>
                            <div
                                class="max-w-[80%] p-4 rounded-2xl text-sm leading-relaxed"
                                :class="msg.role === 'user'
                                    ? 'bg-primary text-primary-foreground rounded-tr-none'
                                    : 'bg-sidebar-accent/50 text-foreground border border-sidebar-border rounded-tl-none'"
                            >
                                <div v-if="msg.role === 'assistant'"
                                    class="markdown-content"
                                    v-html="parseMarkdown(msg.content)"
                                ></div>
                                <span v-else>{{ msg.content }}</span>
                            </div>
                        </div>

                        <div v-if="loading" class="text-sm text-muted-foreground text-center">
                            AI is thinking...
                        </div>
                    </div>
                </div>

                <div class="p-4 border-t border-sidebar-border bg-sidebar/40">
                    <div class="max-w-3xl mx-auto relative">
                        <input
                            v-model="input"
                            type="text"
                            placeholder="Message Chetak AI..."
                            class="flex w-full rounded-md border px-3 py-1 text-base shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50 md:text-sm pr-12 h-12 bg-sidebar-accent/50 border-sidebar-border focus:ring-accent"
                            @keydown.enter="sendMessage"
                        />
                        <button
                            @click="sendMessage"
                            class="absolute right-1.5 top-1.5 h-9 w-9 bg-accent text-accent-foreground hover:bg-accent/90 rounded-md flex items-center justify-center"
                            :disabled="loading"
                        >
                            <Send class="h-4 w-4" />
                        </button>
                    </div>
                    <p class="text-center text-[10px] text-muted-foreground mt-3">
                        Chetak AI can make mistakes. Consider checking important information.
                    </p>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
