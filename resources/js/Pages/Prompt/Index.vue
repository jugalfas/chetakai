<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router } from "@inertiajs/vue3";
import { ref } from "vue";

const props = defineProps({ prompts: Array });

const byType = Object.fromEntries((props.prompts || []).map(p => [p.type, p]));
const localPrompts = ref(['post', 'reel'].map(t => byType[t] ?? { id: null, type: t, prompt: '' }));
const saving = ref(false);

const saveAll = () => {
    saving.value = true;
    router.post(route('prompts.bulk'), { prompts: localPrompts.value }, {
        preserveScroll: true,
        onFinish: () => saving.value = false
    });
};

</script>

<template>

    <Head title="Prompts" />
    <AuthenticatedLayout>
        <template #title>Prompts</template>
        <template #description>Configure your AI content generation templates.</template>

        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-bold tracking-tight text-foreground">Prompt Library</h1>
                <button @click="saveAll" :disabled="saving"
                    class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 border shadow-xs h-9 px-4 border-border bg-primary text-primary-foreground">
                    {{ saving ? 'Saving...' : 'Save All Changes' }}
                </button>
            </div>

            <div class="grid gap-6 md:grid-cols-2">
                <div v-for="item in localPrompts" :key="item.type"
                     class="rounded-xl border border-border bg-card p-4 space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="text-lg font-bold">
                            {{ item.type === 'post' ? 'Instagram Post Prompt' : 'Instagram Reel Prompt' }}
                        </div>
                        <span class="text-xs uppercase font-bold text-muted-foreground">{{ item.type }}</span>
                    </div>
                    <textarea v-model="item.prompt"
                              class="prompt-textarea w-full h-[60vh] min-h-[60vh] rounded-md border border-border bg-muted/20 p-3 text-sm resize-vertical overflow-auto"></textarea>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>


<style scoped>
.prompt-textarea {
    scrollbar-width: thin;
    scrollbar-color: rgba(148, 163, 184, 0.4) transparent;
}

.prompt-textarea::-webkit-scrollbar {
    width: 6px;
}

.prompt-textarea::-webkit-scrollbar-track {
    background: transparent;
}

.prompt-textarea::-webkit-scrollbar-thumb {
    background-color: rgba(148, 163, 184, 0.4);
    border-radius: 8px;
}
</style>
