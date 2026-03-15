<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import axios from 'axios';
import { toast } from 'vue-sonner';

const props = defineProps({
    templates: Array,
});

const selectedTemplateId = ref(null);
const previewPromptText = ref('');
const generatedResult = ref('');
const promptLoading = ref(false);
const generateLoading = ref(false);

const selectedTemplate = computed(() => {
    return props.templates.find((t) => t.id === selectedTemplateId.value) || null;
});

const dynamicFields = computed(() => {
    if (!selectedTemplate.value) return [];
    return selectedTemplate.value.template_attributes || selectedTemplate.value.templateAttributes || [];
});

const formatObject = (item) => {
    return item?.attribute?.name ? `${item.attribute.name}: ${item.value ?? ''}` : '';
};

const onPreviewPrompt = async () => {
    if (!selectedTemplateId.value) {
        toast.error('Please select a template first.');
        return;
    }

    promptLoading.value = true;
    try {
        const { data } = await axios.post('/admin/content-studio/preview', {
            template_id: selectedTemplateId.value,
        });
        previewPromptText.value = data.prompt;
        toast.success('Prompt preview loaded.');
    } catch (error) {
        toast.error('Failed to generate prompt preview.');
    } finally {
        promptLoading.value = false;
    }
};

const onGenerate = async () => {
    if (!selectedTemplateId.value) {
        toast.error('Please select a template first.');
        return;
    }

    generateLoading.value = true;
    try {
        const { data } = await axios.post('/admin/content-studio/generate', {
            template_id: selectedTemplateId.value,
        });

        generatedResult.value = data.ai_response || data.result || '';
        previewPromptText.value = data.prompt || previewPromptText.value;
        toast.success('Content generated successfully.');
    } catch (error) {
        toast.error('Generation failed. Check logs or API settings.');
    } finally {
        generateLoading.value = false;
    }
};

const copyToClipboard = async (text) => {
    try {
        await navigator.clipboard.writeText(text);
        toast.success('Copied to clipboard.');
    } catch (error) {
        toast.error('Copy failed.');
    }
};
</script>

<template>
    <Head title="Content Studio" />
    <AdminLayout>
        <div class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="rounded-lg border border-border bg-card p-5">
                    <h2 class="text-lg font-semibold mb-4">Content Studio</h2>
                    <label class="block text-sm font-medium text-muted-foreground">Template</label>
                    <select v-model="selectedTemplateId" class="mt-1 block w-full rounded-md border border-border bg-background px-3 py-2 text-sm">
                        <option value="">Choose template...</option>
                        <option v-for="template in props.templates" :key="template.id" :value="template.id">
                            {{ template.template_name }}
                        </option>
                    </select>

                    <div class="grid grid-cols-2 gap-3 mt-4 text-xs">
                        <template v-if="selectedTemplate">
                            <div>Content Type: <strong>{{ selectedTemplate.content_type?.name ?? selectedTemplate.contentType?.name ?? '—' }}</strong></div>
                            <div>Category: <strong>{{ selectedTemplate.category?.name ?? '—' }}</strong></div>
                            <div>Goal: <strong>{{ selectedTemplate.content_goal?.name ?? selectedTemplate.contentGoal?.name ?? '—' }}</strong></div>
                            <div>Tone: <strong>{{ selectedTemplate.tone?.name ?? '—' }}</strong></div>
                            <div>Audience: <strong>{{ selectedTemplate.audience?.name ?? '—' }}</strong></div>
                            <div>Style: <strong>{{ selectedTemplate.style?.name ?? '—' }}</strong></div>
                            <div>Length: <strong>{{ selectedTemplate.length || '—' }}</strong></div>
                            <div>Bulk: <strong>{{ selectedTemplate.bulk_generate ?? 1 }}</strong></div>
                        </template>
                    </div>
                </div>

                <div class="rounded-lg border border-border bg-card p-5">
                    <h3 class="font-semibold mb-3">Dynamic Fields</h3>
                    <div class="space-y-2">
                        <div v-if="!dynamicFields.length" class="text-sm text-muted-foreground">No dynamic fields available for selected template.</div>
                        <div v-for="(field, index) in dynamicFields" :key="field.id || index" class="rounded-md border border-border p-2">
                            <p class="text-xs font-semibold">{{ field.attribute?.name || field.attribute?.slug }}</p>
                            <p class="text-sm text-foreground">{{ field.value }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-lg border border-border bg-card p-5">
                <h3 class="font-semibold mb-3">Preview Prompt</h3>
                <textarea
                    v-model="previewPromptText"
                    rows="5"
                    readonly
                    class="w-full rounded-md border border-border bg-muted/10 p-3 text-sm resize-none"
                />
                <div class="flex gap-2 mt-3">
                    <button
                        @click="onPreviewPrompt"
                        :disabled="promptLoading"
                        class="rounded-md bg-primary px-4 py-2 text-sm font-semibold text-primary-foreground hover:bg-primary/90 disabled:cursor-not-allowed"
                    >
                        {{ promptLoading ? 'Loading...' : 'Preview Prompt' }}
                    </button>

                    <button
                        @click="onGenerate"
                        :disabled="generateLoading"
                        class="rounded-md bg-success px-4 py-2 text-sm font-semibold text-white hover:bg-success/90 disabled:cursor-not-allowed"
                    >
                        {{ generateLoading ? 'Generating...' : 'Generate' }}
                    </button>
                </div>
            </div>

            <div class="rounded-lg border border-border bg-card p-5">
                <h3 class="font-semibold mb-3">Generated Content Result</h3>
                <div v-if="!generatedResult" class="text-sm text-muted-foreground">No result yet. Click Generate to create content.</div>
                <div v-else>
                    <pre class="whitespace-pre-wrap rounded-md border border-border bg-background p-3 text-sm">{{ generatedResult }}</pre>
                    <div class="mt-2 flex gap-2">
                        <button
                            @click="copyToClipboard(generatedResult)"
                            class="rounded-md border border-border px-3 py-1 text-xs"
                        >Copy</button>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
