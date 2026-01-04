<script setup>
import { ref, nextTick } from 'vue'
import axios from 'axios'
import { Head } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const messages = ref([])
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
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                AI Chat
            </h2>
        </template>
        <section class="bg-gray-50 dark:bg-gray-900 p-3 sm:p-5">
            <div class="mx-auto max-w-screen-xl max-h-screen-xl px-4 lg:px-12">
                <!-- Header -->
                <div class="p-4 border-b bg-white font-semibold">
                    AI Agent
                </div>

                <!-- Chat Body -->
                <div id="chat-body" class="flex-1 overflow-y-auto p-4 space-y-4">
                    <div v-for="(msg, index) in messages" :key="index" class="flex"
                        :class="msg.role === 'user' ? 'justify-end' : 'justify-start'">
                        <div class="max-w-[70%] px-4 py-2 rounded-lg whitespace-pre-wrap" :class="msg.role === 'user'
                            ? 'bg-blue-600 text-white'
                            : 'bg-white text-gray-800 border'">
                            {{ msg.content }}
                        </div>
                    </div>

                    <div v-if="loading" class="text-sm text-gray-500">
                        AI is thinking…
                    </div>
                </div>

                <!-- Input -->
                <form @submit.prevent="sendMessage" class="p-4 border-t bg-white flex gap-2">
                    <input v-model="input" type="text" placeholder="Ask something…"
                        class="flex-1 border rounded px-3 py-2 focus:outline-none" />

                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded disabled:opacity-50"
                        :disabled="loading">
                        Send
                    </button>
                </form>
            </div>
        </section>
    </AuthenticatedLayout>
</template>
