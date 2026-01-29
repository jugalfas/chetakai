<script setup>
import { ref, nextTick, watch, onMounted, computed } from 'vue'
import axios from 'axios'
import { Head, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import DeleteConfirmationModal from '@/Components/DeleteConfirmationModal.vue'
import { Send, Plus, Sparkles, MessageSquare, User, Bot, Menu, X, Pin, Trash2, Edit2, Copy, RefreshCw, ChevronDown, MoreVertical } from 'lucide-vue-next'
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

const conversations = ref([...props.conversations])
const messages = ref([...props.messages])
const selectedConversationId = ref(props.selectedConversation?.id || null)
const input = ref('')
const loading = ref(false)
const isStreaming = ref(false)
const streamingMessageId = ref(null)
const isMobileHistoryOpen = ref(false)
const editingChatId = ref(null)
const editingChatTitle = ref('')
const editingMessageId = ref(null)
const editingMessageContent = ref('')
const showJumpToBottom = ref(false)
const includeVersions = ref(false)

// Delete Confirmation Modal State
const deleteModal = ref({
    show: false,
    title: '',
    message: '',
    type: null, // 'chat' or 'message'
    id: null,
    processing: false
})

// Watch for prop changes (when navigating)
watch(() => props.conversations, (newConvs) => {
    conversations.value = [...newConvs]
}, { deep: true })

watch(() => props.messages, (newMsgs) => {
    messages.value = [...newMsgs]
}, { deep: true })

watch(() => props.selectedConversation, (newConv) => {
    selectedConversationId.value = newConv?.id || null
    isMobileHistoryOpen.value = false
})

const sortedConversations = computed(() => {
    return [...conversations.value].sort((a, b) => {
        if (a.pinned !== b.pinned) return b.pinned - a.pinned
        return new Date(b.updated_at) - new Date(a.updated_at)
    })
})

const updateConversationInList = (updatedConv) => {
    const index = conversations.value.findIndex(c => c.id === updatedConv.id)
    if (index !== -1) {
        conversations.value[index] = { ...conversations.value[index], ...updatedConv }
    } else {
        conversations.value.unshift(updatedConv)
    }
}

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
            title: null
        })
        const newConv = response.data.conversation
        conversations.value.unshift(newConv)
        selectedConversationId.value = newConv.id
        messages.value = []
        router.get(`/chat?conversation_id=${newConv.id}`, {}, { preserveState: true })
    } catch (e) {
        console.error('Error creating new chat:', e)
    }
}

const sendMessage = async () => {
    if (!input.value.trim() || loading.value || isStreaming.value) return

    const userMsg = input.value
    input.value = ''
    loading.value = true

    // Optimistic user message (temporary ID)
    const tempUserMsg = {
        id: Date.now(),
        role: 'user',
        content: userMsg,
        version: 1,
        created_at: new Date().toISOString()
    }
    messages.value.push(tempUserMsg)
    scrollToBottom(true)

    try {
        const response = await axios.post('/chat/send', {
            message: userMsg,
            conversation_id: selectedConversationId.value
        })

        // Replace temp message and add assistant reply with streaming effect
        messages.value = messages.value.filter(m => m.id !== tempUserMsg.id)
        messages.value.push(response.data.user_message)
        
        const assistantMsg = response.data.assistant_message
        const fullContent = assistantMsg.content
        assistantMsg.content = '' // Start empty for streaming
        messages.value.push(assistantMsg)
        
        if (!selectedConversationId.value) {
            selectedConversationId.value = response.data.conversation.id
            router.get(`/chat?conversation_id=${response.data.conversation.id}`, {}, { preserveState: true })
        }
        
        updateConversationInList(response.data.conversation)
        
        // Start streaming effect
        await startStreaming(assistantMsg.id, fullContent)

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
        scrollToBottom(true)
    }
}

const startStreaming = async (messageId, fullText) => {
    isStreaming.value = true
    streamingMessageId.value = messageId
    const messageIndex = messages.value.findIndex(m => m.id === messageId)
    
    if (messageIndex === -1) return

    const words = fullText.split(' ')
    let currentText = ''
    
    for (let i = 0; i < words.length; i++) {
        currentText += (i === 0 ? '' : ' ') + words[i]
        messages.value[messageIndex].content = currentText
        
        // Faster for more words, slower for fewer to keep it natural
        const delay = words.length > 50 ? 20 : 40
        await new Promise(resolve => setTimeout(resolve, delay))
        
        // Auto-scroll as content grows
        scrollToBottom()
    }
    
    isStreaming.value = false
    streamingMessageId.value = null
}

const startRenameChat = (conversation) => {
    editingChatId.value = conversation.id
    editingChatTitle.value = conversation.title || 'Untitled Chat'
}

const saveRenameChat = async () => {
    if (!editingChatId.value || !editingChatTitle.value.trim()) return

    const chatId = editingChatId.value
    const newTitle = editingChatTitle.value
    editingChatId.value = null

    try {
        const response = await axios.patch(`/conversations/${chatId}/rename`, {
            title: newTitle
        })
        updateConversationInList(response.data)
    } catch (e) {
        console.error('Error renaming chat:', e)
    }
}

const deleteChat = (id) => {
    deleteModal.value = {
        show: true,
        title: 'Delete Chat',
        message: 'Are you sure you want to delete this conversation? This will hide it from your history.',
        type: 'chat',
        id: id,
        processing: false
    }
}

const deleteMessage = (id) => {
    deleteModal.value = {
        show: true,
        title: 'Delete Message',
        message: 'Are you sure you want to delete this message?',
        type: 'message',
        id: id,
        processing: false
    }
}

const confirmDelete = async () => {
    if (!deleteModal.value.id || deleteModal.value.processing) return

    deleteModal.value.processing = true
    try {
        if (deleteModal.value.type === 'chat') {
            await axios.delete(`/conversations/${deleteModal.value.id}`)
            conversations.value = conversations.value.filter(c => c.id !== deleteModal.value.id)
            if (selectedConversationId.value === deleteModal.value.id) {
                router.get('/chat')
            }
        } else if (deleteModal.value.type === 'message') {
            await axios.delete(`/messages/${deleteModal.value.id}`)
            messages.value = messages.value.filter(m => m.id !== deleteModal.value.id)
        }
        deleteModal.value.show = false
    } catch (e) {
        console.error(`Error deleting ${deleteModal.value.type}:`, e)
    } finally {
        deleteModal.value.processing = false
    }
}

const togglePinChat = async (id) => {
    try {
        const response = await axios.patch(`/conversations/${id}/pin`)
        updateConversationInList(response.data)
    } catch (e) {
        console.error('Error pinning chat:', e)
    }
}

const copyToClipboard = (text) => {
    navigator.clipboard.writeText(text)
    // Optional: show toast
}

const startEditMessage = (msg) => {
    editingMessageId.value = msg.id
    editingMessageContent.value = msg.content
}

const cancelEditMessage = () => {
    editingMessageId.value = null
    editingMessageContent.value = ''
}

const saveEditMessage = async () => {
    if (!editingMessageId.value || !editingMessageContent.value.trim()) return

    loading.value = true
    const msgId = editingMessageId.value
    const newContent = editingMessageContent.value
    editingMessageId.value = null

    try {
        const response = await axios.patch(`/messages/${msgId}/edit`, {
            content: newContent
        })

        // Refresh all messages to show the new branch
        if (includeVersions.value) {
            // If showing versions, re-fetch from backend to get full branch history
            const res = await axios.get(`/chat/${selectedConversationId.value}?include_versions=1`)
            messages.value = res.data.messages
        } else {
            messages.value = [
                ...messages.value.filter(m => m.id < msgId && !m.is_superseded),
                response.data.user_message
            ]
            
            const assistantMsg = response.data.assistant_message
            const fullContent = assistantMsg.content
            assistantMsg.content = ''
            messages.value.push(assistantMsg)
            
            updateConversationInList(response.data.conversation)
            await startStreaming(assistantMsg.id, fullContent)
        }
        
        scrollToBottom(true)
    } catch (e) {
        console.error('Error editing message:', e)
        router.reload({ only: ['messages'] })
    } finally {
        loading.value = false
    }
}

const regenerateMessage = async (msgId) => {
    loading.value = true
    try {
        const response = await axios.post(`/messages/${msgId}/regenerate`)
        
        // Refresh messages
        if (includeVersions.value) {
            const res = await axios.get(`/chat/${selectedConversationId.value}?include_versions=1`)
            messages.value = res.data.messages
        } else {
            messages.value = messages.value.filter(m => m.id !== msgId && !m.is_superseded)
            
            const assistantMsg = response.data.assistant_message
            const fullContent = assistantMsg.content
            assistantMsg.content = ''
            messages.value.push(assistantMsg)
            
            updateConversationInList(response.data.conversation)
            await startStreaming(assistantMsg.id, fullContent)
        }

        scrollToBottom(true)
    } catch (e) {
        console.error('Error regenerating message:', e)
        router.reload({ only: ['messages'] })
    } finally {
        loading.value = false
    }
}

const toggleVersions = () => {
    includeVersions.value = !includeVersions.value
    router.reload({ 
        data: { include_versions: includeVersions.value },
        only: ['messages'],
        preserveState: true 
    })
}

const handleScroll = (e) => {
    const el = e.target
    const threshold = 100
    showJumpToBottom.value = el.scrollHeight - el.scrollTop - el.clientHeight > threshold
}

const scrollToBottom = (force = false) => {
    const el = document.getElementById('chat-body')
    if (el && (force || !showJumpToBottom.value)) {
        el.scrollTop = el.scrollHeight
    }
}

const parseMarkdown = (text) => {
    if (!text) return ''
    return marked.parse(text)
}

const vFocus = {
    mounted: (el) => el.focus()
}

onMounted(() => {
    scrollToBottom(true)
})
</script>

<template>
    <Head title="AI Chat" />

    <AuthenticatedLayout>
        <div class="h-[calc(100vh-12rem)] flex gap-4 relative">
            <!-- Mobile History Drawer -->
            <!-- Backdrop -->
            <div
                class="fixed inset-0 z-50 bg-black/80 data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0" v-if="isMobileHistoryOpen"
                @click="isMobileHistoryOpen = false"
            ></div>
            <div
                v-if="isMobileHistoryOpen"
                class="fixed inset-0 z-50 lg:hidden"
            >

                <!-- Drawer Content -->
                <div class="fixed z-50 gap-4 shadow-lg transition ease-in-out data-[state=closed]:duration-300 data-[state=open]:duration-500 data-[state=open]:animate-in data-[state=closed]:animate-out inset-y-0 left-0 h-full border-r data-[state=closed]:slide-out-to-left data-[state=open]:slide-in-from-left sm:max-w-sm w-72 bg-sidebar border-r-sidebar-border p-6">
                    <button
                        @click="isMobileHistoryOpen = false"
                        class="absolute right-4 top-4 rounded-sm opacity-70 ring-offset-background transition-opacity hover:opacity-100 focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:pointer-events-none data-[state=open]:bg-secondary"
                    >
                        <X class="h-4 w-4" />
                    </button>
                    <div class="flex flex-col space-y-2 text-center sm:text-left mb-6">
                        <h2 class="text-lg font-semibold text-foreground text-left">Chat History</h2>
                    </div>

                    <button
                        @click="createNewChat(); isMobileHistoryOpen = false"
                        class="inline-flex items-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 hover-elevate active-elevate-2 border shadow-xs active:shadow-none min-h-9 px-4 py-2 w-full gap-2 border-sidebar-border bg-sidebar hover:bg-sidebar-accent justify-start h-11 mb-4"
                    >
                        <Plus class="h-4 w-4" />
                        New Chat
                    </button>

                    <div class="flex-1 overflow-y-auto pr-2 custom-scrollbar">
                        <div class="space-y-1">
                            <div
                                v-for="chat in sortedConversations"
                                :key="chat.id"
                                class="group relative flex items-center gap-3 px-3 py-2.5 rounded-lg cursor-pointer transition-all duration-200"
                                :class="[
                                    selectedConversationId === chat.id
                                        ? 'bg-sidebar-accent text-sidebar-accent-foreground shadow-sm'
                                        : 'text-sidebar-foreground/70 hover:bg-sidebar-accent/50 hover:text-sidebar-foreground'
                                ]"
                                @click="selectConversation(chat.id)"
                            >
                                <MessageSquare class="h-4 w-4 flex-shrink-0" />
                                
                                <div class="flex-1 min-w-0">
                                    <input
                                        v-if="editingChatId === chat.id"
                                        v-model="editingChatTitle"
                                        @blur="saveRenameChat"
                                        @keyup.enter="saveRenameChat"
                                        @keyup.esc="editingChatId = null"
                                        v-focus
                                        class="w-full bg-transparent border-none p-0 text-sm focus:ring-0 font-medium"
                                    />
                                    <div 
                                        v-else 
                                        @dblclick="startRenameChat(chat)"
                                        class="truncate text-sm font-medium"
                                    >
                                        {{ chat.title || 'New Chat' }}
                                    </div>
                                </div>

                                <!-- Chat Actions -->
                                <div v-if="editingChatId !== chat.id" class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button
                                        @click.stop="startRenameChat(chat)"
                                        class="p-1 hover:bg-sidebar-foreground/10 rounded-md transition-colors"
                                        title="Rename"
                                    >
                                        <Edit2 class="h-3.5 w-3.5" />
                                    </button>
                                    <button
                                        @click.stop="togglePinChat(chat.id)"
                                        class="p-1 hover:bg-sidebar-foreground/10 rounded-md transition-colors"
                                        :class="{ 'text-primary opacity-100': chat.pinned }"
                                        :title="chat.pinned ? 'Unpin' : 'Pin'"
                                    >
                                        <Pin class="h-3.5 w-3.5" :class="{ 'fill-current': chat.pinned }" />
                                    </button>
                                    <button
                                        @click.stop="deleteChat(chat.id)"
                                        class="p-1 hover:bg-sidebar-foreground/10 rounded-md transition-colors text-destructive/70 hover:text-destructive"
                                        title="Delete"
                                    >
                                        <Trash2 class="h-3.5 w-3.5" />
                                    </button>
                                </div>
                            </div>
                            <p v-if="conversations.length === 0" class="text-sm text-muted-foreground px-3 py-2 text-center">
                                No conversations yet. Start a new chat!
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar - History (Desktop) -->
            <div class="w-64 hidden lg:flex flex-col gap-4">
                <button
                    @click="createNewChat"
                    class="inline-flex items-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 hover-elevate active-elevate-2 border shadow-xs active:shadow-none min-h-9 px-4 py-2 w-full gap-2 border-sidebar-border bg-sidebar hover:bg-sidebar-accent justify-start h-11"
                >
                    <Plus class="h-4 w-4" />
                    New Chat
                </button>
                <div class="flex-1 overflow-y-auto pr-2 custom-scrollbar">
                    <div class="space-y-1">
                        <div
                            v-for="chat in sortedConversations"
                            :key="chat.id"
                            class="group relative flex items-center gap-3 px-3 py-2.5 rounded-lg cursor-pointer transition-all duration-200"
                            :class="[
                                selectedConversationId === chat.id
                                    ? 'bg-sidebar-accent text-sidebar-accent-foreground shadow-sm'
                                    : 'text-sidebar-foreground/70 hover:bg-sidebar-accent/50 hover:text-sidebar-foreground'
                            ]"
                            @click="selectConversation(chat.id)"
                        >
                            <MessageSquare class="h-4 w-4 flex-shrink-0" />
                            
                            <div class="flex-1 min-w-0">
                                <input
                                    v-if="editingChatId === chat.id"
                                    v-model="editingChatTitle"
                                    @blur="saveRenameChat"
                                    @keyup.enter="saveRenameChat"
                                    @keyup.esc="editingChatId = null"
                                    v-focus
                                    class="w-full bg-transparent border-none p-0 text-sm focus:ring-0 font-medium"
                                />
                                <div 
                                    v-else 
                                    @dblclick="startRenameChat(chat)"
                                    class="truncate text-sm font-medium"
                                >
                                    {{ chat.title || 'New Chat' }}
                                </div>
                            </div>

                            <!-- Chat Actions -->
                            <div v-if="editingChatId !== chat.id" class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button
                                    @click.stop="startRenameChat(chat)"
                                    class="p-1 hover:bg-sidebar-foreground/10 rounded-md transition-colors"
                                    title="Rename"
                                >
                                    <Edit2 class="h-3.5 w-3.5" />
                                </button>
                                <button
                                    @click.stop="togglePinChat(chat.id)"
                                    class="p-1 hover:bg-sidebar-foreground/10 rounded-md transition-colors"
                                    :class="{ 'text-primary opacity-100': chat.pinned }"
                                    :title="chat.pinned ? 'Unpin' : 'Pin'"
                                >
                                    <Pin class="h-3.5 w-3.5" :class="{ 'fill-current': chat.pinned }" />
                                </button>
                                <button
                                    @click.stop="deleteChat(chat.id)"
                                    class="p-1 hover:bg-sidebar-foreground/10 rounded-md transition-colors text-destructive/70 hover:text-destructive"
                                    title="Delete"
                                >
                                    <Trash2 class="h-3.5 w-3.5" />
                                </button>
                            </div>
                        </div>
                        <p v-if="conversations.length === 0" class="text-sm text-muted-foreground px-3 py-2 text-center">
                            No conversations yet. Start a new chat!
                        </p>
                    </div>
                </div>
            </div>

            <!-- Main Chat Area -->
            <div class="flex-1 flex flex-col bg-card border border-border rounded-xl overflow-hidden shadow-sm card-shadow relative">
                <!-- Jump to Latest Button -->
                <button
                    v-if="showJumpToBottom"
                    @click="scrollToBottom(true)"
                    class="absolute bottom-24 right-8 z-10 bg-primary text-primary-foreground p-2 rounded-full shadow-lg hover:scale-110 transition-all animate-in fade-in slide-in-from-bottom-4"
                >
                    <ChevronDown class="h-5 w-5" />
                </button>

                <!-- Mobile Chat Header -->
                <div class="lg:hidden flex items-center justify-between p-4 border-b border-border bg-sidebar/50">
                    <div class="flex items-center gap-3">
                        <button
                            @click="isMobileHistoryOpen = true"
                            class="p-2 hover:bg-sidebar-accent rounded-md transition-colors"
                        >
                            <Menu class="h-5 w-5" />
                        </button>
                        <h1 class="font-bold text-lg josefin-sans">Chetak AI</h1>
                    </div>
                    <button
                        @click="createNewChat"
                        class="p-2 hover:bg-sidebar-accent rounded-md transition-colors text-muted-foreground"
                        title="New Chat"
                    >
                        <Plus class="h-5 w-5" />
                    </button>
                </div>

                <div id="chat-body" class="flex-1 overflow-y-auto p-4 sm:p-6 scroll-smooth" @scroll="handleScroll">
                    <!-- Version History Toggle -->
                    <div v-if="selectedConversationId" class="sticky top-0 z-20 flex justify-center mb-4">
                        <button 
                            @click="toggleVersions"
                            class="text-[10px] uppercase tracking-widest font-bold px-3 py-1 rounded-full border transition-all shadow-sm hover:scale-105"
                            :class="includeVersions ? 'bg-primary/10 border-primary text-primary' : 'bg-sidebar border-sidebar-border text-muted-foreground hover:text-foreground'"
                        >
                            {{ includeVersions ? 'Hide Version History' : 'Show All Versions' }}
                        </button>
                    </div>

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
                            class="group flex gap-4 animate-in fade-in slide-in-from-bottom-2 duration-300"
                            :class="[
                                msg.role === 'user' ? 'flex-row-reverse' : 'flex-row',
                                msg.is_superseded ? 'opacity-50 grayscale-[0.5]' : ''
                            ]"
                        >
                            <div
                                class="h-8 w-8 rounded-lg flex items-center justify-center flex-shrink-0 border border-sidebar-border"
                                :class="msg.role === 'user' ? 'bg-sidebar-accent' : 'bg-primary/20'"
                            >
                                <User v-if="msg.role === 'user'" class="h-4 w-4" />
                                <Bot v-else class="h-4 w-4 text-primary" />
                            </div>
                            
                            <div class="flex flex-col gap-2 max-w-[80%]">
                                <div
                                    class="relative p-4 rounded-2xl text-sm leading-relaxed transition-all"
                                    :class="[
                                        msg.role === 'user'
                                            ? 'bg-primary text-primary-foreground rounded-tr-none'
                                            : 'bg-sidebar-accent/50 text-foreground border border-sidebar-border rounded-tl-none',
                                        msg.is_superseded ? 'border-dashed opacity-80' : ''
                                    ]"
                                >
                                    <!-- Message Content -->
                                    <div v-if="editingMessageId !== msg.id">
                                        <div v-if="msg.role === 'assistant'"
                                            class="markdown-content"
                                            :class="{ 'streaming-cursor': isStreaming && streamingMessageId === msg.id }"
                                            v-html="parseMarkdown(msg.content)"
                                        ></div>
                                        <span v-else class="whitespace-pre-wrap">{{ msg.content }}</span>
                                    </div>

                                    <!-- Edit Mode -->
                                    <div v-else class="space-y-3">
                                        <textarea
                                            v-model="editingMessageContent"
                                            class="w-full bg-sidebar-accent text-foreground border-none rounded-lg p-2 text-sm focus:ring-1 focus:ring-accent min-h-[100px]"
                                            v-focus
                                        ></textarea>
                                        <div class="flex justify-end gap-2">
                                            <button @click="cancelEditMessage" class="px-3 py-1 text-xs hover:bg-sidebar/50 rounded-md transition-colors">Cancel</button>
                                            <button @click="saveEditMessage" class="px-3 py-1 text-xs bg-accent text-accent-foreground rounded-md hover:bg-accent/90 transition-colors">Save & Submit</button>
                                        </div>
                                    </div>

                                    <!-- Message Toolbar (Hover) -->
                                    <div 
                                        v-if="editingMessageId !== msg.id && !msg.is_superseded"
                                        class="absolute top-0 opacity-0 group-hover:opacity-100 transition-opacity duration-200"
                                        :class="msg.role === 'user' ? 'right-full mr-2' : 'left-full ml-2'"
                                    >
                                        <div class="flex flex-col bg-sidebar border border-sidebar-border rounded-lg shadow-sm p-1 gap-1">
                                            <button @click="copyToClipboard(msg.content)" class="p-1.5 hover:bg-sidebar-accent rounded-md transition-colors text-muted-foreground" title="Copy">
                                                <Copy class="h-3.5 w-3.5" />
                                            </button>
                                            <button v-if="msg.role === 'user'" @click="startEditMessage(msg)" class="p-1.5 hover:bg-sidebar-accent rounded-md transition-colors text-muted-foreground" title="Edit">
                                                <Edit2 class="h-3.5 w-3.5" />
                                            </button>
                                            <button v-if="msg.role === 'assistant'" @click="regenerateMessage(msg.id)" class="p-1.5 hover:bg-sidebar-accent rounded-md transition-colors text-muted-foreground" title="Regenerate">
                                                <RefreshCw class="h-3.5 w-3.5" />
                                            </button>
                                            <button @click="deleteMessage(msg.id)" class="p-1.5 hover:bg-sidebar-accent rounded-md transition-colors text-muted-foreground hover:text-destructive" title="Delete">
                                                <Trash2 class="h-3.5 w-3.5" />
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 text-[10px] text-muted-foreground px-1" :class="msg.role === 'user' ? 'justify-end' : 'justify-start'">
                                    <span>{{ new Date(msg.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) }}</span>
                                    <span v-if="msg.version > 1" class="px-1.5 py-0.5 rounded-full bg-sidebar-accent border border-sidebar-border font-medium">
                                        {{ msg.role === 'user' ? 'Edited' : 'Alternative' }} · v{{ msg.version }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Skeleton Loader -->
                        <div v-if="loading && !isStreaming" class="flex gap-4 animate-in fade-in duration-300">
                            <div class="h-8 w-8 rounded-lg bg-primary/20 flex items-center justify-center flex-shrink-0 border border-sidebar-border">
                                <Bot class="h-4 w-4 text-primary animate-pulse" />
                            </div>
                            <div class="flex-1 max-w-[80%] space-y-2 p-4 bg-sidebar-accent/50 border border-sidebar-border rounded-2xl rounded-tl-none">
                                <div class="h-2 bg-muted rounded w-3/4 animate-pulse"></div>
                                <div class="h-2 bg-muted rounded w-1/2 animate-pulse"></div>
                                <div class="h-2 bg-muted rounded w-5/6 animate-pulse"></div>
                            </div>
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

        <!-- Global Components -->
        <DeleteConfirmationModal
            :show="deleteModal.show"
            :title="deleteModal.title"
            :message="deleteModal.message"
            :processing="deleteModal.processing"
            @close="deleteModal.show = false"
            @confirm="confirmDelete"
        />
    </AuthenticatedLayout>
</template>

<style>
.markdown-content {
    @apply space-y-4;
}
.markdown-content p {
    @apply mb-4 last:mb-0;
}
.markdown-content ul, .markdown-content ol {
    @apply ml-4 mb-4 list-outside;
}
.markdown-content ul {
    @apply list-disc;
}
.markdown-content ol {
    @apply list-decimal;
}
.markdown-content li {
    @apply mb-1;
}
.markdown-content code {
    @apply bg-sidebar-accent px-1.5 py-0.5 rounded text-xs font-mono;
}
.markdown-content pre {
    @apply bg-sidebar-accent p-4 rounded-lg overflow-x-auto mb-4 border border-sidebar-border;
}
.markdown-content pre code {
    @apply bg-transparent p-0 text-xs;
}
.markdown-content h1, .markdown-content h2, .markdown-content h3 {
    @apply font-bold mb-2 mt-4 first:mt-0;
}
.markdown-content h1 { @apply text-xl; }
.markdown-content h2 { @apply text-lg; }
.markdown-content h3 { @apply text-base; }

/* Streaming Cursor Effect */
.streaming-cursor > *:last-child::after {
    content: '';
    display: inline-block;
    width: 0.5rem;
    height: 1rem;
    background-color: currentColor;
    margin-left: 0.25rem;
    vertical-align: middle;
    animation: blink 0.8s step-end infinite;
}

@keyframes blink {
    from, to { opacity: 1; }
    50% { opacity: 0; }
}
</style>
