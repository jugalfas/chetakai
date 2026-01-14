<script setup>
import { ref, nextTick } from 'vue'
import axios from 'axios'
import { Head } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Send, Plus, Sparkles, MessageSquare, User, Bot } from 'lucide-vue-next'

const messages = ref([
    { role: "assistant", content: "How can I help you today? I'm here to assist with quote generation, analysis, and more." }
])
const input = ref('')
const conversationId = ref(null)
const loading = ref(false)

const sendMessage = async () => {
    if (!input.value.trim() || loading.value) return

    const userMessage = input.value

    messages.value.push({
        role: 'user',
        content: userMessage,
    })

    input.value = ''
    loading.value = true

    await nextTick()
    scrollToBottom()

    try {
        const response = await axios.post('/chat/send', {
            message: userMessage,
            conversation_id: conversationId.value,
        })

        messages.value.push({
            role: 'assistant',
            content: response.data.reply,
        })

        if (response.data.conversation_id) {
            conversationId.value = response.data.conversation_id
        }
    } catch (e) {
        console.log(e);
        messages.value.push({
            role: 'assistant',
            content: 'Something broke 💀 Try again.',
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
</script>

<template>
    <Head title="AI Chat" />

    <AuthenticatedLayout>
        <div class="h-[calc(100vh-12rem)] flex gap-4">
            <!-- Sidebar - History -->
            <div class="w-64 hidden lg:flex flex-col gap-4">
                <button class="w-full gap-2 border-sidebar-border bg-sidebar hover:bg-sidebar-accent justify-start h-11 px-4 py-2 rounded-md font-medium text-sidebar-foreground hover:text-sidebar-accent-foreground flex items-center">
                    <Plus class="h-4 w-4" />
                    New Chat
                </button>
                <div class="flex-1 overflow-y-auto">
                    <div class="space-y-2">
                        <button
                            v-for="chat in ['Quote Ideas: Success', 'Instagram Hook Generation', 'Category Research', 'Personal Branding']"
                            :key="chat"
                            class="w-full justify-start font-normal text-muted-foreground hover:text-foreground hover:bg-sidebar-accent px-3 py-2 h-auto text-sm rounded-md flex items-center"
                        >
                            <MessageSquare class="h-4 w-4 mr-2 flex-shrink-0" />
                            <span class="truncate">{{ chat }}</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Main Chat Area -->
            <div class="flex-1 flex flex-col bg-card border border-border rounded-xl overflow-hidden shadow-sm card-shadow">
                <div id="chat-body" class="flex-1 overflow-y-auto p-4 sm:p-6">
                    <div class="max-w-3xl mx-auto space-y-8">
                        <div v-if="messages.length === 1" class="flex flex-col items-center justify-center py-20 text-center animate-in fade-in zoom-in duration-500">
                            <div class="h-12 w-12 rounded-2xl bg-accent/20 flex items-center justify-center mb-6">
                                <Sparkles class="h-6 w-6 text-accent" />
                            </div>
                            <h2 class="text-2xl font-bold mb-2">How can I help you today?</h2>
                            <p class="text-muted-foreground max-w-sm">
                                Start a conversation by typing a message below. I'm here to assist with questions, writing, analysis, and more.
                            </p>
                        </div>

                        <div
                            v-for="(msg, i) in messages"
                            :key="i"
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
                                {{ msg.content }}
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
                            class="w-full pr-12 h-12 bg-sidebar-accent/50 border border-sidebar-border rounded-md px-3 focus:outline-none focus:ring-2 focus:ring-accent"
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
