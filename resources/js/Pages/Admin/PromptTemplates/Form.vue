<script setup>
import { ref, onMounted } from "vue";
import { useForm, router } from "@inertiajs/vue3";
import axios from "axios";
import { toast } from "vue-sonner";
import { BeakerIcon, ArrowPathIcon, ArrowsPointingOutIcon } from "@heroicons/vue/24/outline";
import Modal from "@/Components/Modal.vue";
import {
    SelectRoot,
    SelectTrigger,
    SelectValue,
    SelectPortal,
    SelectContent,
    SelectViewport,
    SelectItem,
    SelectItemText,
    SelectItemIndicator,
} from "radix-vue";
import { ChevronDown, Check } from "lucide-vue-next";

const props = defineProps({
    modelValue: {
        type: Object,
        default: () => ({}),
    },
    platforms: Array,
    contentTypes: Array,
    isEdit: Boolean,
});

const emit = defineEmits(["submit"]);

const form = useForm({
    name: props.modelValue.name || "",
    platform_id: props.modelValue.platform_id ? props.modelValue.platform_id.toString() : "none",
    content_type_id: props.modelValue.content_type_id ? props.modelValue.content_type_id.toString() : "none",
    scope: props.modelValue.scope || "system",
    role: props.modelValue.role || "system",
    version: props.modelValue.version || 1,
    prompt_template: props.modelValue.prompt_template || "",
    variables: props.modelValue.variables ? JSON.stringify(props.modelValue.variables, null, 2) : "",
    output_schema: props.modelValue.output_schema ? JSON.stringify(props.modelValue.output_schema, null, 2) : "",
    model_preferences: props.modelValue.model_preferences ? JSON.stringify(props.modelValue.model_preferences, null, 2) : "",
    is_default: props.modelValue.is_default ?? false,
    status: props.modelValue.status ?? true,
});

// Test Prompt Logic
const testVariables = ref("{}");
const testResults = ref(null);
const testing = ref(false);

const fullScreenField = ref(null);
const fullScreenTitle = ref("");

const openFullScreen = (field, title) => {
    fullScreenField.value = field;
    fullScreenTitle.value = title;
};

const closeFullScreen = () => {
    fullScreenField.value = null;
};

const handleTest = async () => {
    if (!props.isEdit) {
        toast.error("Please save the template before testing.");
        return;
    }

    try {
        const parsedVars = JSON.parse(testVariables.value);
        testing.value = true;
        
        const response = await axios.post(route('admin.prompt-templates.test', props.modelValue.id), {
            test_variables: parsedVars
        });
        
        testResults.value = response.data;
        toast.success("Test completed successfully!");
    } catch (e) {
        if (e instanceof SyntaxError) {
            toast.error("Invalid JSON format in test variables.");
        } else {
            const message = e.response?.data?.message || "Test failed.";
            toast.error(message);
        }
    } finally {
        testing.value = false;
    }
};

const handleSubmit = () => {
    // Basic JSON validation before submit
    if (!validateJsonFields()) return;
    
    // Convert 'none' values back to null for the database
    const formData = { ...form.data() };
    if (formData.platform_id === "none") formData.platform_id = null;
    if (formData.content_type_id === "none") formData.content_type_id = null;
    
    emit("submit", formData);
};

const validateJsonFields = () => {
    const fields = ['variables', 'output_schema', 'model_preferences'];
    let isValid = true;

    fields.forEach(field => {
        if (form[field] && form[field].trim()) {
            try {
                JSON.parse(form[field]);
            } catch (e) {
                form.setError(field, `Invalid JSON format for ${field.replace('_', ' ')}.`);
                isValid = false;
            }
        }
    });

    return isValid;
};

const formatJson = (field) => {
    try {
        if (form[field] && form[field].trim()) {
            const parsed = JSON.parse(form[field]);
            form[field] = JSON.stringify(parsed, null, 2);
        }
    } catch (e) {
        // Just let validation handle it
    }
};

onMounted(() => {
    // Initialize test variables with placeholders if empty
    if (props.modelValue.variables) {
        const defaultVars = {};
        Object.keys(props.modelValue.variables).forEach(k => defaultVars[k] = "sample_value");
        testVariables.value = JSON.stringify(defaultVars, null, 2);
    }
});

const variablesPlaceholder = `{
  "content_type": "string",
  "tone": "string",
  "audience": "string"
}`;

const outputSchemaPlaceholder = `{
  "title": "string",
  "caption": "string",
  "hashtags": ["string"]
}`;

const preferencesPlaceholder = `{
  "temperature": 0.7,
  "max_tokens": 500,
  "model": "gpt-4"
}`;
</script>

<template>
    <div class="space-y-8">
        <form @submit.prevent="handleSubmit" class="space-y-6 bg-card p-6 rounded-lg border border-border shadow-sm">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <div class="col-span-2">
                    <label for="name" class="block text-sm font-medium text-foreground mb-1">Name</label>
                        <input
                            id="name"
                            v-model="form.name"
                            type="text"
                            class="w-full rounded-md border-input bg-background px-3 py-2 text-sm focus:outline-none focus:border-primary focus:ring-0 transition-colors"
                            :class="{ 'border-destructive': form.errors.name }"
                            placeholder="E.g. Facebook Post Generator"
                        />
                    <p v-if="form.errors.name" class="mt-1 text-xs text-destructive">{{ form.errors.name }}</p>
                </div>

                <!-- Platform -->
                <div>
                    <label for="platform_id" class="block text-sm font-medium text-foreground mb-1">Platform</label>
                    <SelectRoot v-model="form.platform_id">
                        <SelectTrigger
                            class="flex w-full items-center justify-between rounded-md border px-3 py-2 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring bg-background border-border text-foreground capitalize"
                        >
                            <SelectValue placeholder="No specific platform" />
                            <ChevronDown class="h-4 w-4 opacity-50" />
                        </SelectTrigger>

                        <SelectPortal>
                            <SelectContent
                                class="z-[100] w-[--radix-select-trigger-width] overflow-hidden rounded-md border bg-popover text-popover-foreground shadow-md data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0 data-[state=closed]:zoom-out-95 data-[state=open]:zoom-in-95 data-[side=bottom]:slide-in-from-top-2 data-[side=left]:slide-in-from-right-2 data-[side=right]:slide-in-from-left-2 data-[side=top]:slide-in-from-bottom-2 bg-[#041C32] border-sidebar-border"
                                position="popper"
                                :side-offset="5"
                            >
                                <SelectViewport class="p-1">
                                    <SelectItem
                                        value="none"
                                        class="relative flex w-full cursor-default select-none items-center rounded-sm py-1.5 pl-2 pr-9 text-sm outline-none focus:bg-accent focus:text-accent-foreground data-[state=checked]:bg-[#092644] data-[state=checked]:text-white data-[disabled]:pointer-events-none data-[disabled]:opacity-50 hover:bg-muted/20 transition-colors capitalize"
                                    >
                                        <SelectItemText>No specific platform</SelectItemText>
                                        <SelectItemIndicator class="absolute right-3 flex h-4 w-4 items-center justify-center">
                                            <Check class="h-4 w-4 stroke-[3px]" />
                                        </SelectItemIndicator>
                                    </SelectItem>
                                    <SelectItem
                                        v-for="platform in platforms"
                                        :key="platform.id"
                                        :value="platform.id.toString()"
                                        class="relative flex w-full cursor-default select-none items-center rounded-sm py-1.5 pl-2 pr-9 text-sm outline-none focus:bg-accent focus:text-accent-foreground data-[state=checked]:bg-[#092644] data-[state=checked]:text-white data-[disabled]:pointer-events-none data-[disabled]:opacity-50 hover:bg-muted/20 transition-colors capitalize"
                                    >
                                        <SelectItemText>{{ platform.name }}</SelectItemText>
                                        <SelectItemIndicator class="absolute right-3 flex h-4 w-4 items-center justify-center">
                                            <Check class="h-4 w-4 stroke-[3px]" />
                                        </SelectItemIndicator>
                                    </SelectItem>
                                </SelectViewport>
                            </SelectContent>
                        </SelectPortal>
                    </SelectRoot>
                </div>

                <!-- Content Type -->
                <div>
                    <label for="content_type_id" class="block text-sm font-medium text-foreground mb-1">Content Type</label>
                    <SelectRoot v-model="form.content_type_id">
                        <SelectTrigger
                            class="flex h-11 w-full items-center justify-between rounded-md border px-3 py-2 text-sm shadow-sm transition-colors focus:outline-none focus:border-primary focus:ring-0 bg-background border-border text-foreground capitalize"
                        >
                            <SelectValue placeholder="No specific content type" />
                            <ChevronDown class="h-4 w-4 opacity-50" />
                        </SelectTrigger>

                        <SelectPortal>
                            <SelectContent
                                class="z-[100] w-[--radix-select-trigger-width] overflow-hidden rounded-md border bg-popover text-popover-foreground shadow-md data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0 data-[state=closed]:zoom-out-95 data-[state=open]:zoom-in-95 data-[side=bottom]:slide-in-from-top-2 data-[side=left]:slide-in-from-right-2 data-[side=right]:slide-in-from-left-2 data-[side=top]:slide-in-from-bottom-2 bg-[#041C32] border-sidebar-border"
                                position="popper"
                                :side-offset="5"
                            >
                                <SelectViewport class="p-1">
                                    <SelectItem
                                        value="none"
                                        class="relative flex w-full cursor-default select-none items-center rounded-sm py-1.5 pl-2 pr-9 text-sm outline-none focus:bg-accent focus:text-accent-foreground data-[state=checked]:bg-[#092644] data-[state=checked]:text-white data-[disabled]:pointer-events-none data-[disabled]:opacity-50 hover:bg-muted/20 transition-colors capitalize"
                                    >
                                        <SelectItemText>No specific content type</SelectItemText>
                                        <SelectItemIndicator class="absolute right-3 flex h-4 w-4 items-center justify-center">
                                            <Check class="h-4 w-4 stroke-[3px]" />
                                        </SelectItemIndicator>
                                    </SelectItem>
                                    <SelectItem
                                        v-for="ct in contentTypes"
                                        :key="ct.id"
                                        :value="ct.id.toString()"
                                        class="relative flex w-full cursor-default select-none items-center rounded-sm py-1.5 pl-2 pr-9 text-sm outline-none focus:bg-accent focus:text-accent-foreground data-[state=checked]:bg-[#092644] data-[state=checked]:text-white data-[disabled]:pointer-events-none data-[disabled]:opacity-50 hover:bg-muted/20 transition-colors capitalize"
                                    >
                                        <SelectItemText>{{ ct.name }}</SelectItemText>
                                        <SelectItemIndicator class="absolute right-3 flex h-4 w-4 items-center justify-center">
                                            <Check class="h-4 w-4 stroke-[3px]" />
                                        </SelectItemIndicator>
                                    </SelectItem>
                                </SelectViewport>
                            </SelectContent>
                        </SelectPortal>
                    </SelectRoot>
                </div>

                <!-- Scope -->
                <div>
                    <label for="scope" class="block text-sm font-medium text-foreground mb-1">Scope</label>
                    <SelectRoot v-model="form.scope">
                        <SelectTrigger
                            class="flex h-11 w-full items-center justify-between rounded-md border px-3 py-2 text-sm shadow-sm transition-colors focus:outline-none focus:border-primary focus:ring-0 bg-background border-border text-foreground capitalize"
                        >
                            <SelectValue placeholder="Select scope" />
                            <ChevronDown class="h-4 w-4 opacity-50" />
                        </SelectTrigger>

                        <SelectPortal>
                            <SelectContent
                                class="z-[100] w-[--radix-select-trigger-width] overflow-hidden rounded-md border bg-popover text-popover-foreground shadow-md data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0 data-[state=closed]:zoom-out-95 data-[state=open]:zoom-in-95 data-[side=bottom]:slide-in-from-top-2 data-[side=left]:slide-in-from-right-2 data-[side=right]:slide-in-from-left-2 data-[side=top]:slide-in-from-bottom-2 bg-[#041C32] border-sidebar-border"
                                position="popper"
                                :side-offset="5"
                            >
                                <SelectViewport class="p-1">
                                    <SelectItem
                                        value="system"
                                        class="relative flex w-full cursor-default select-none items-center rounded-sm py-1.5 pl-2 pr-9 text-sm outline-none focus:bg-accent focus:text-accent-foreground data-[state=checked]:bg-[#092644] data-[state=checked]:text-white data-[disabled]:pointer-events-none data-[disabled]:opacity-50 hover:bg-muted/20 transition-colors capitalize"
                                    >
                                        <SelectItemText>System</SelectItemText>
                                        <SelectItemIndicator class="absolute right-3 flex h-4 w-4 items-center justify-center">
                                            <Check class="h-4 w-4 stroke-[3px]" />
                                        </SelectItemIndicator>
                                    </SelectItem>
                                    <SelectItem
                                        value="user"
                                        class="relative flex w-full cursor-default select-none items-center rounded-sm py-1.5 pl-2 pr-9 text-sm outline-none focus:bg-accent focus:text-accent-foreground data-[state=checked]:bg-[#092644] data-[state=checked]:text-white data-[disabled]:pointer-events-none data-[disabled]:opacity-50 hover:bg-muted/20 transition-colors capitalize"
                                    >
                                        <SelectItemText>User</SelectItemText>
                                        <SelectItemIndicator class="absolute right-3 flex h-4 w-4 items-center justify-center">
                                            <Check class="h-4 w-4 stroke-[3px]" />
                                        </SelectItemIndicator>
                                    </SelectItem>
                                </SelectViewport>
                            </SelectContent>
                        </SelectPortal>
                    </SelectRoot>
                </div>

                <!-- Role -->
                <div>
                    <label for="role" class="block text-sm font-medium text-foreground mb-1">Role</label>
                    <SelectRoot v-model="form.role">
                        <SelectTrigger
                            class="flex h-11 w-full items-center justify-between rounded-md border px-3 py-2 text-sm shadow-sm transition-colors focus:outline-none focus:border-primary focus:ring-0 bg-background border-border text-foreground capitalize"
                        >
                            <SelectValue placeholder="Select role" />
                            <ChevronDown class="h-4 w-4 opacity-50" />
                        </SelectTrigger>

                        <SelectPortal>
                            <SelectContent
                                class="z-[100] w-[--radix-select-trigger-width] overflow-hidden rounded-md border bg-popover text-popover-foreground shadow-md data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0 data-[state=closed]:zoom-out-95 data-[state=open]:zoom-in-95 data-[side=bottom]:slide-in-from-top-2 data-[side=left]:slide-in-from-right-2 data-[side=right]:slide-in-from-left-2 data-[side=top]:slide-in-from-bottom-2 bg-[#041C32] border-sidebar-border"
                                position="popper"
                                :side-offset="5"
                            >
                                <SelectViewport class="p-1">
                                    <SelectItem
                                        value="system"
                                        class="relative flex w-full cursor-default select-none items-center rounded-sm py-1.5 pl-2 pr-9 text-sm outline-none focus:bg-accent focus:text-accent-foreground data-[state=checked]:bg-[#092644] data-[state=checked]:text-white data-[disabled]:pointer-events-none data-[disabled]:opacity-50 hover:bg-muted/20 transition-colors capitalize"
                                    >
                                        <SelectItemText>System</SelectItemText>
                                        <SelectItemIndicator class="absolute right-3 flex h-4 w-4 items-center justify-center">
                                            <Check class="h-4 w-4 stroke-[3px]" />
                                        </SelectItemIndicator>
                                    </SelectItem>
                                    <SelectItem
                                        value="user"
                                        class="relative flex w-full cursor-default select-none items-center rounded-sm py-1.5 pl-2 pr-9 text-sm outline-none focus:bg-accent focus:text-accent-foreground data-[state=checked]:bg-[#092644] data-[state=checked]:text-white data-[disabled]:pointer-events-none data-[disabled]:opacity-50 hover:bg-muted/20 transition-colors capitalize"
                                    >
                                        <SelectItemText>User</SelectItemText>
                                        <SelectItemIndicator class="absolute right-3 flex h-4 w-4 items-center justify-center">
                                            <Check class="h-4 w-4 stroke-[3px]" />
                                        </SelectItemIndicator>
                                    </SelectItem>
                                    <SelectItem
                                        value="assistant"
                                        class="relative flex w-full cursor-default select-none items-center rounded-sm py-1.5 pl-2 pr-9 text-sm outline-none focus:bg-accent focus:text-accent-foreground data-[state=checked]:bg-[#092644] data-[state=checked]:text-white data-[disabled]:pointer-events-none data-[disabled]:opacity-50 hover:bg-muted/20 transition-colors capitalize"
                                    >
                                        <SelectItemText>Assistant</SelectItemText>
                                        <SelectItemIndicator class="absolute right-3 flex h-4 w-4 items-center justify-center">
                                            <Check class="h-4 w-4 stroke-[3px]" />
                                        </SelectItemIndicator>
                                    </SelectItem>
                                </SelectViewport>
                            </SelectContent>
                        </SelectPortal>
                    </SelectRoot>
                    <p v-if="form.errors.role" class="mt-1 text-xs text-destructive">{{ form.errors.role }}</p>
                </div>

                <!-- Version -->
                <div>
                    <label for="version" class="block text-sm font-medium text-foreground mb-1">Version</label>
                        <input
                            id="version"
                            v-model="form.version"
                            type="number"
                            min="1"
                            class="w-full rounded-md border-input bg-background px-3 py-2 text-sm focus:outline-none focus:border-primary focus:ring-0 transition-colors"
                        />
                </div>

                <!-- Model Preferences (JSON) -->
                <div>
                    <label for="model_preferences" class="block text-sm font-medium text-foreground mb-1">Model Preferences (JSON)</label>
                    <div class="relative group">
                        <textarea
                            id="model_preferences"
                            v-model="form.model_preferences"
                            rows="3"
                            @blur="formatJson('model_preferences')"
                            class="w-full rounded-md border-input bg-background px-3 py-2 text-sm font-mono focus:outline-none focus:border-primary focus:ring-0 transition-colors pr-10"
                            :placeholder="preferencesPlaceholder"
                        ></textarea>
                        <button 
                            type="button" 
                            @click="openFullScreen('model_preferences', 'Model Preferences (JSON)')"
                            class="absolute bottom-2 right-2 p-1.5 rounded-md bg-muted/50 text-muted-foreground hover:bg-muted hover:text-foreground opacity-0 group-hover:opacity-100 transition-opacity"
                            title="Expand"
                        >
                            <ArrowsPointingOutIcon class="h-4 w-4" />
                        </button>
                    </div>
                    <p v-if="form.errors.model_preferences" class="mt-1 text-xs text-destructive">{{ form.errors.model_preferences }}</p>
                </div>

                <!-- Prompt Template -->
                <div class="col-span-2">
                    <label for="prompt_template" class="block text-sm font-medium text-foreground mb-1">Prompt Template</label>
                    <div class="relative group">
                        <textarea
                            id="prompt_template"
                            v-model="form.prompt_template"
                            rows="12"
                            class="w-full rounded-md border-input bg-background px-3 py-2 text-sm font-mono focus:outline-none focus:border-primary focus:ring-0 transition-colors pr-10"
                            placeholder="Enter your prompt template here. Use {{variable_name}} for dynamic content."
                        ></textarea>
                        <button 
                            type="button" 
                            @click="openFullScreen('prompt_template', 'Prompt Template')"
                            class="absolute bottom-3 right-3 p-2 rounded-md bg-muted/50 text-muted-foreground hover:bg-muted hover:text-foreground opacity-0 group-hover:opacity-100 transition-opacity"
                            title="Expand"
                        >
                            <ArrowsPointingOutIcon class="h-5 w-5" />
                        </button>
                    </div>
                    <p v-if="form.errors.prompt_template" class="mt-1 text-xs text-destructive">{{ form.errors.prompt_template }}</p>
                </div>

                <!-- Variables (JSON) -->
                <div class="col-span-1">
                    <label for="variables" class="block text-sm font-medium text-foreground mb-1">Variables (JSON)</label>
                    <div class="relative group">
                        <textarea
                            id="variables"
                            v-model="form.variables"
                            rows="6"
                            @blur="formatJson('variables')"
                            class="w-full rounded-md border-input bg-background px-3 py-2 text-sm font-mono focus:outline-none focus:border-primary focus:ring-0 transition-colors pr-10"
                            :placeholder="variablesPlaceholder"
                        ></textarea>
                        <button 
                            type="button" 
                            @click="openFullScreen('variables', 'Variables (JSON)')"
                            class="absolute bottom-2 right-2 p-1.5 rounded-md bg-muted/50 text-muted-foreground hover:bg-muted hover:text-foreground opacity-0 group-hover:opacity-100 transition-opacity"
                            title="Expand"
                        >
                            <ArrowsPointingOutIcon class="h-4 w-4" />
                        </button>
                    </div>
                    <p v-if="form.errors.variables" class="mt-1 text-xs text-destructive">{{ form.errors.variables }}</p>
                </div>

                <!-- Output Schema (JSON) -->
                <div class="col-span-1">
                    <label for="output_schema" class="block text-sm font-medium text-foreground mb-1">Output Schema (JSON)</label>
                    <div class="relative group">
                        <textarea
                            id="output_schema"
                            v-model="form.output_schema"
                            rows="6"
                            @blur="formatJson('output_schema')"
                            class="w-full rounded-md border-input bg-background px-3 py-2 text-sm font-mono focus:outline-none focus:border-primary focus:ring-0 transition-colors pr-10"
                            :placeholder="outputSchemaPlaceholder"
                        ></textarea>
                        <button 
                            type="button" 
                            @click="openFullScreen('output_schema', 'Output Schema (JSON)')"
                            class="absolute bottom-2 right-2 p-1.5 rounded-md bg-muted/50 text-muted-foreground hover:bg-muted hover:text-foreground opacity-0 group-hover:opacity-100 transition-opacity"
                            title="Expand"
                        >
                            <ArrowsPointingOutIcon class="h-4 w-4" />
                        </button>
                    </div>
                    <p v-if="form.errors.output_schema" class="mt-1 text-xs text-destructive">{{ form.errors.output_schema }}</p>
                </div>

                <!-- Toggles -->
                <div class="col-span-2 flex items-center space-x-8">
                    <div class="flex items-center space-x-2">
                        <input
                            id="is_default"
                            v-model="form.is_default"
                            type="checkbox"
                            class="h-4 w-4 rounded border-input text-primary focus:ring-0 focus:outline-none bg-background"
                        />
                        <label for="is_default" class="text-sm font-medium text-foreground">Is Default (Sets others to false)</label>
                    </div>

                    <div class="flex items-center space-x-2">
                        <button
                            type="button"
                            class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-0"
                            :class="form.status ? 'bg-primary' : 'bg-muted'"
                            @click="form.status = !form.status"
                        >
                            <span
                                aria-hidden="true"
                                class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                :class="form.status ? 'translate-x-5' : 'translate-x-0'"
                            ></span>
                        </button>
                        <label class="text-sm font-medium text-foreground">Active Status</label>
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-4 space-x-4">
                <button
                    v-if="isEdit"
                    type="button"
                    @click="handleTest"
                    :disabled="testing"
                    class="inline-flex items-center justify-center rounded-md border border-primary text-primary px-6 py-2 text-sm font-semibold hover:bg-primary/5 shadow-sm disabled:opacity-50 transition-all"
                >
                    <ArrowPathIcon v-if="testing" class="mr-2 h-4 w-4 animate-spin" />
                    <BeakerIcon v-else class="mr-2 h-4 w-4" />
                    Test Prompt
                </button>
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="inline-flex items-center justify-center rounded-md bg-primary px-8 py-2 text-sm font-semibold text-primary-foreground hover:bg-primary/90 shadow-sm disabled:opacity-50 transition-all font-outfit"
                >
                    {{ form.processing ? 'Saving...' : 'Save Template' }}
                </button>
            </div>
        </form>

        <!-- Test Results Section -->
        <div v-if="testResults" class="bg-card p-6 rounded-lg border border-primary/20 shadow-lg animate-in fade-in slide-in-from-bottom-2 duration-500">
            <h3 class="text-lg font-bold text-foreground mb-4 flex items-center">
                <BeakerIcon class="mr-2 h-5 w-5 text-primary" />
                Prompt Test Results
            </h3>
            
            <div class="space-y-6">
                <!-- Final Prompt -->
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-muted-foreground mb-2">Final Prompt</h4>
                    <div class="bg-muted p-4 rounded-md font-mono text-sm whitespace-pre-wrap border border-border">
                        {{ testResults.final_prompt }}
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Parsed Variables -->
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-muted-foreground mb-2">Variables Used</h4>
                        <div class="bg-muted p-4 rounded-md font-mono text-xs border border-border">
                            <pre>{{ JSON.stringify(testResults.parsed_variables, null, 2) }}</pre>
                        </div>
                    </div>
                    
                    <!-- AI Response Mock -->
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-muted-foreground mb-2 text-primary">AI Response</h4>
                        <div class="bg-primary/5 p-4 rounded-md text-sm italic border border-primary/10">
                            {{ testResults.ai_response }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <button @click="testResults = null" class="text-xs text-muted-foreground hover:text-foreground">Clear Results</button>
            </div>
        </div>

        <!-- Testing Interface (Variables Input) -->
        <div v-if="isEdit && !testResults" class="bg-background/50 p-6 rounded-lg border border-dashed border-border">
            <h3 class="text-sm font-semibold text-foreground mb-3 flex items-center">
                <ArrowPathIcon class="mr-2 h-4 w-4" />
                Prepare Test Data
            </h3>
            <div class="relative group">
                <textarea
                    v-model="testVariables"
                    rows="4"
                    class="w-full rounded-md border-input bg-background/50 px-3 py-2 text-xs font-mono focus:outline-none focus:border-primary focus:ring-0 transition-colors pr-10"
                    placeholder="Enter JSON variables for testing..."
                ></textarea>
                <button 
                    type="button" 
                    @click="openFullScreen('testVariables', 'Test Variables')"
                    class="absolute bottom-2 right-2 p-1.5 rounded-md bg-muted/50 text-muted-foreground hover:bg-muted hover:text-foreground opacity-0 group-hover:opacity-100 transition-opacity"
                    title="Expand"
                >
                    <ArrowsPointingOutIcon class="h-4 w-4" />
                </button>
            </div>
            <p class="mt-1 text-[10px] text-muted-foreground">Input JSON data to populate {{variables}} in your prompt.</p>
        </div>

        <!-- Full Screen Editor Modal -->
        <Modal :show="fullScreenField !== null" maxWidth="5xl" @close="closeFullScreen">
            <div class="flex flex-col h-[80vh] bg-background">
                <div class="flex items-center justify-between p-4 border-b border-border pr-12">
                    <h3 class="text-lg font-bold text-foreground">{{ fullScreenTitle }}</h3>
                    <div class="text-xs text-muted-foreground uppercase tracking-widest font-mono hidden sm:block">Editing Full Screen</div>
                </div>
                <div class="flex-1 p-0 relative h-full">
                    <textarea
                        v-if="fullScreenField === 'testVariables'"
                        v-model="testVariables"
                        class="w-full h-full p-6 bg-background text-foreground font-mono text-base resize-none focus:outline-none border-none outline-none"
                        placeholder="Enter content..."
                    ></textarea>
                    <textarea
                        v-else-if="fullScreenField"
                        v-model="form[fullScreenField]"
                        @blur="formatJson(fullScreenField)"
                        class="w-full h-full p-6 bg-background text-foreground font-mono text-base resize-none focus:outline-none border-none outline-none"
                        placeholder="Enter content..."
                    ></textarea>
                </div>
                <div class="p-4 border-t border-border flex justify-end">
                    <button 
                        @click="closeFullScreen"
                        class="px-6 py-2 rounded-md bg-primary text-primary-foreground font-semibold hover:bg-primary/90 transition-all"
                    >
                        Done Editing
                    </button>
                </div>
            </div>
        </Modal>
    </div>
</template>

<style scoped>
/* Minimal scrollbar for textareas */
textarea {
    resize: none;
    scrollbar-width: thin;
    scrollbar-color: rgba(155, 155, 155, 0.3) transparent;
}

textarea::-webkit-scrollbar {
    width: 6px;
}

textarea::-webkit-resizer,
textarea::-webkit-scrollbar-corner {
    background-color: transparent;
}

/* Minimal number input (hide default spinners) */
input[type="number"] {
    appearance: textfield;
    -moz-appearance: textfield;
}

input[type="number"]::-webkit-inner-spin-button,
input[type="number"]::-webkit-outer-spin-button {
    -webkit-appearance: none;
    appearance: none;
    margin: 0;
}

textarea::-webkit-scrollbar-track {
    background: transparent;
}

textarea::-webkit-scrollbar-thumb {
    background: rgba(155, 155, 155, 0.3);
    border-radius: 10px;
}

textarea::-webkit-scrollbar-thumb:hover {
    background: rgba(155, 155, 155, 0.5);
}

/* For Firefox */
textarea {
    scrollbar-width: thin;
    scrollbar-color: rgba(155, 155, 155, 0.3) transparent;
}

.data-\[state\=checked\]\:bg-\[\#092644\][data-state="checked"] {
    background-color: #092644;
}

.focus\:bg-accent:focus {
    background-color: #092644;
}
</style>
