<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import { ref, watch, onMounted } from "vue";
import axios from "axios";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

const props = defineProps({
    template: Object,
    platforms: Array,
    contentTypes: Array,
    categories: Array,
    contentGoals: Array,
    tones: Array,
    audiences: Array,
    styles: Array,
});

const form = useForm({
    template_name: props.template.template_name || "",
    platform_id: props.template.platform_id || null,
    content_type_id: props.template.content_type_id || null,
    category_id: props.template.category_id || null,
    goal_id: props.template.goal_id || null,
    tone_id: props.template.tone_id || null,
    audience_id: props.template.audience_id || null,
    style_id: props.template.style_id || null,
    length: props.template.length || "medium",
    bulk_generate: props.template.bulk_generate ?? 1,
    template_attributes: [],
});

const dynamicAttributes = ref([]);
const dynamicValues = ref({});

const prepareDynamicValues = (loadedAttributes) => {
    const existing = (props.template.templateAttributes || []).reduce((carry, attr) => {
        carry[attr.attribute_id] = attr.value;
        return carry;
    }, {});

    dynamicValues.value = {};
    loadedAttributes.forEach((attribute) => {
        dynamicValues.value[attribute.id] = existing[attribute.id] ?? "";
    });
};

const fetchAttributes = async (contentTypeId) => {
    dynamicAttributes.value = [];
    dynamicValues.value = {};

    if (!contentTypeId) return;

    try {
        const response = await axios.get(route("admin.content_types.attributes", contentTypeId));
        dynamicAttributes.value = response.data.attributes;

        if (props.template.templateAttributes?.length) {
            prepareDynamicValues(dynamicAttributes.value);
        } else {
            dynamicAttributes.value.forEach((attribute) => {
                dynamicValues.value[attribute.id] = "";
            });
        }
    } catch (error) {
        console.error("Failed to load content type attributes", error);
    }
};

watch(
    () => form.content_type_id,
    async (newValue) => {
        await fetchAttributes(newValue);
    }
);

onMounted(async () => {
    if (form.content_type_id) {
        await fetchAttributes(form.content_type_id);
    }
});

const submit = () => {
    form.template_attributes = dynamicAttributes.value.map((attribute) => ({
        attribute_id: attribute.id,
        value: dynamicValues.value[attribute.id] ?? "",
    }));

    form.put(route("admin.templates.update", props.template.id), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head :title="`Edit Template: ${props.template.template_name}`" />
    <AdminLayout>
        <template #header>
            <div class="flex items-center gap-4">
                <Link
                    :href="route('admin.templates.index')"
                    class="text-sm text-muted-foreground hover:text-foreground"
                >
                    ← Templates
                </Link>
                <h2 class="font-semibold text-xl text-foreground leading-tight">Edit Template</h2>
            </div>
        </template>

        <div class="p-6">
            <div class="rounded-lg border border-border bg-card p-6 max-w-3xl">
                <form class="space-y-6" @submit.prevent="submit">
                    <div>
                        <InputLabel value="Template Name" required />
                        <input
                            v-model="form.template_name"
                            type="text"
                            class="mt-1 w-full rounded-md border border-border bg-background px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary"
                            required
                        />
                        <InputError :message="form.errors.template_name" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <InputLabel value="Platform" />
                            <select
                                v-model="form.platform_id"
                                class="mt-1 w-full rounded-md border border-border bg-background px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary"
                            >
                                <option :value="null">— Select platform —</option>
                                <option v-for="p in props.platforms" :key="p.id" :value="p.id">{{ p.name }}</option>
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
                                <option v-for="c in props.contentTypes" :key="c.id" :value="c.id">{{ c.name }}</option>
                            </select>
                            <InputError :message="form.errors.content_type_id" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <InputLabel value="Category" />
                            <select
                                v-model="form.category_id"
                                class="mt-1 w-full rounded-md border border-border bg-background px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary"
                            >
                                <option :value="null">— Select category —</option>
                                <option v-for="item in props.categories" :key="item.id" :value="item.id">{{ item.name }}</option>
                            </select>
                            <InputError :message="form.errors.category_id" />
                        </div>

                        <div>
                            <InputLabel value="Goal" />
                            <select
                                v-model="form.goal_id"
                                class="mt-1 w-full rounded-md border border-border bg-background px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary"
                            >
                                <option :value="null">— Select goal —</option>
                                <option v-for="item in props.contentGoals" :key="item.id" :value="item.id">{{ item.name }}</option>
                            </select>
                            <InputError :message="form.errors.goal_id" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <InputLabel value="Tone" />
                            <select
                                v-model="form.tone_id"
                                class="mt-1 w-full rounded-md border border-border bg-background px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary"
                            >
                                <option :value="null">— Select tone —</option>
                                <option v-for="item in props.tones" :key="item.id" :value="item.id">{{ item.name }}</option>
                            </select>
                            <InputError :message="form.errors.tone_id" />
                        </div>

                        <div>
                            <InputLabel value="Audience" />
                            <select
                                v-model="form.audience_id"
                                class="mt-1 w-full rounded-md border border-border bg-background px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary"
                            >
                                <option :value="null">— Select audience —</option>
                                <option v-for="item in props.audiences" :key="item.id" :value="item.id">{{ item.name }}</option>
                            </select>
                            <InputError :message="form.errors.audience_id" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <InputLabel value="Style" />
                            <select
                                v-model="form.style_id"
                                class="mt-1 w-full rounded-md border border-border bg-background px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary"
                            >
                                <option :value="null">— Select style —</option>
                                <option v-for="item in props.styles" :key="item.id" :value="item.id">{{ item.name }}</option>
                            </select>
                            <InputError :message="form.errors.style_id" />
                        </div>

                        <div>
                            <InputLabel value="Length" />
                            <select
                                v-model="form.length"
                                class="mt-1 w-full rounded-md border border-border bg-background px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary"
                            >
                                <option value="short">Short</option>
                                <option value="medium">Medium</option>
                                <option value="long">Long</option>
                            </select>
                            <InputError :message="form.errors.length" />
                        </div>
                    </div>

                    <div>
                        <InputLabel value="Bulk Generate" />
                        <input
                            v-model="form.bulk_generate"
                            type="number"
                            min="0"
                            class="mt-1 w-32 rounded-md border border-border bg-background px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary"
                        />
                        <p class="mt-1 text-xs text-muted-foreground">Number of results to generate from this template.</p>
                        <InputError :message="form.errors.bulk_generate" />
                    </div>

                    <div v-if="dynamicAttributes.length">
                        <h3 class="text-base font-semibold text-foreground mb-2">Dynamic Attributes</h3>
                        <p class="text-sm text-muted-foreground mb-4">Fields based on selected content type.</p>
                        <div class="space-y-4">
                            <div v-for="attribute in dynamicAttributes" :key="attribute.id" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <InputLabel :value="attribute.name" :required="attribute.is_required" />
                                    <template v-if="attribute.input_type === 'select'">
                                        <select
                                            v-model="dynamicValues[attribute.id]"
                                            class="mt-1 w-full rounded-md border border-border bg-background px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary"
                                        >
                                            <option value="">— Select {{ attribute.name }} —</option>
                                            <option
                                                v-for="option in attribute.attribute_values"
                                                :key="option.id"
                                                :value="option.value"
                                            >
                                                {{ option.value }}
                                            </option>
                                        </select>
                                    </template>
                                    <template v-else>
                                        <input
                                            v-model="dynamicValues[attribute.id]"
                                            type="text"
                                            class="mt-1 w-full rounded-md border border-border bg-background px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary"
                                            :placeholder="`Enter ${attribute.name}`"
                                        />
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <PrimaryButton type="submit" :disabled="form.processing">Update Template</PrimaryButton>
                        <Link
                            :href="route('admin.templates.index')"
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
