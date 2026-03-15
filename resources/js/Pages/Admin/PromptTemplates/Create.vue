<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

const props = defineProps({
    platforms: Array,
    contentTypes: Array,
});

const form = useForm({
    name: "",
    platform_id: null,
    content_type_id: null,
    prompt_template: "",
    status: true,
});

const submit = () => {
    form.post(route("admin.prompt-templates.store"), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Create Prompt Template" />
    <AdminLayout>
        <template #header>
            <div class="flex items-center gap-4">
                <Link
                    :href="route('admin.prompt-templates.index')"
                    class="text-sm text-muted-foreground hover:text-foreground"
                >
                    ← Prompt Templates
                </Link>
                <h2 class="font-semibold text-xl text-foreground leading-tight">Create Prompt Template</h2>
            </div>
        </template>

        <div class="p-6">
            <div class="rounded-lg border border-border bg-card p-6 max-w-3xl">
                <form class="space-y-6" @submit.prevent="submit">
                    <div>
                        <InputLabel value="Name" required />
                        <input
                            v-model="form.name"
                            type="text"
                            class="mt-1 w-full rounded-md border border-border bg-background px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary"
                            placeholder="e.g. Instagram Post Template"
                            required
                        />
                        <InputError :message="form.errors.name" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <InputLabel value="Platform" />
                            <select
                                v-model="form.platform_id"
                                class="mt-1 w-full rounded-md border border-border bg-background px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary"
                            >
                                <option :value="null">— Select platform —</option>
                                <option
                                    v-for="p in platforms"
                                    :key="p.id"
                                    :value="p.id"
                                >
                                    {{ p.name }}
                                </option>
                            </select>
                            <InputError :message="form.errors.platform_id" />
                        </div>
                        <div>
                            <InputLabel value="Content Type" />
                            <select
                                v-model="form.content_type_id"
                                class="mt-1 w-full rounded-md border border-border bg-background px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary"
                            >
                                <option :value="null">— Select content type —</option>
                                <option
                                    v-for="c in contentTypes"
                                    :key="c.id"
                                    :value="c.id"
                                >
                                    {{ c.name }}
                                </option>
                            </select>
                            <InputError :message="form.errors.content_type_id" />
                        </div>
                    </div>

                    <div>
                        <InputLabel value="Prompt Template" required />
                        <textarea
                            v-model="form.prompt_template"
                            rows="12"
                            class="mt-1 w-full rounded-md border border-border bg-background px-3 py-2 text-sm font-mono focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary"
                            placeholder="Generate a {{content_type}} for {{platform}}.&#10;Category: {{category}}&#10;..."
                            required
                        />
                        <p class="mt-1 text-xs text-muted-foreground" v-pre>
                            Use placeholders: {{content_type}}, {{platform}}, {{category}}, {{goal}}, {{tone}}, {{audience}}, {{style}}, and dynamic attributes e.g. {{image_subject}}.
                        </p>
                        <InputError :message="form.errors.prompt_template" />
                    </div>

                    <div class="flex items-center gap-3">
                        <button
                            type="button"
                            role="switch"
                            :aria-checked="form.status"
                            class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2"
                            :class="form.status ? 'bg-primary' : 'bg-muted'"
                            @click="form.status = !form.status"
                        >
                            <span
                                class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition"
                                :class="form.status ? 'translate-x-5' : 'translate-x-1'"
                            />
                        </button>
                        <InputLabel value="Active (template will be used for prompt generation)" class="!text-sm font-normal" />
                    </div>
                    <InputError :message="form.errors.status" />

                    <div class="flex gap-3 pt-2">
                        <PrimaryButton type="submit" :disabled="form.processing">
                            Create Prompt Template
                        </PrimaryButton>
                        <Link
                            :href="route('admin.prompt-templates.index')"
                            class="inline-flex items-center justify-center rounded-md border border-border bg-background px-4 py-2 text-sm font-medium hover:bg-muted"
                        >
                            Cancel
                        </Link>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
