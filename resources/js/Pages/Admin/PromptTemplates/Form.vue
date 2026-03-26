<script setup>
import { ref, onMounted } from "vue";
import { useForm, router } from "@inertiajs/vue3";
import axios from "axios";
import { toast } from "vue-sonner";
import { BeakerIcon, ArrowPathIcon } from "@heroicons/vue/24/outline";

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
    platform_id: props.modelValue.platform_id || "",
    content_type_id: props.modelValue.content_type_id || "",
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
    
    emit("submit", form);
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
                        class="w-full rounded-md border-input bg-background px-3 py-2 text-sm ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                        :class="{ 'border-destructive': form.errors.name }"
                        placeholder="E.g. Facebook Post Generator"
                    />
                    <p v-if="form.errors.name" class="mt-1 text-xs text-destructive">{{ form.errors.name }}</p>
                </div>

                <!-- Platform -->
                <div>
                    <label for="platform_id" class="block text-sm font-medium text-foreground mb-1">Platform</label>
                    <select
                        id="platform_id"
                        v-model="form.platform_id"
                        class="w-full rounded-md border-input bg-background px-3 py-2 text-sm ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                    >
                        <option value="">No specific platform</option>
                        <option v-for="platform in platforms" :key="platform.id" :value="platform.id">
                            {{ platform.name }}
                        </option>
                    </select>
                </div>

                <!-- Content Type -->
                <div>
                    <label for="content_type_id" class="block text-sm font-medium text-foreground mb-1">Content Type</label>
                    <select
                        id="content_type_id"
                        v-model="form.content_type_id"
                        class="w-full rounded-md border-input bg-background px-3 py-2 text-sm ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                    >
                        <option value="">No specific content type</option>
                        <option v-for="ct in contentTypes" :key="ct.id" :value="ct.id">
                            {{ ct.name }}
                        </option>
                    </select>
                </div>

                <!-- Scope -->
                <div>
                    <label for="scope" class="block text-sm font-medium text-foreground mb-1">Scope</label>
                    <select
                        id="scope"
                        v-model="form.scope"
                        class="w-full rounded-md border-input bg-background px-3 py-2 text-sm ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                    >
                        <option value="system">System</option>
                        <option value="user">User</option>
                    </select>
                </div>

                <!-- Role -->
                <div>
                    <label for="role" class="block text-sm font-medium text-foreground mb-1">Role</label>
                    <select
                        id="role"
                        v-model="form.role"
                        class="w-full rounded-md border-input bg-background px-3 py-2 text-sm ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                    >
                        <option value="system">System</option>
                        <option value="user">User</option>
                        <option value="assistant">Assistant</option>
                    </select>
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
                        class="w-full rounded-md border-input bg-background px-3 py-2 text-sm ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                    />
                </div>

                <!-- Model Preferences (JSON) -->
                <div>
                    <label for="model_preferences" class="block text-sm font-medium text-foreground mb-1">Model Preferences (JSON)</label>
                    <textarea
                        id="model_preferences"
                        v-model="form.model_preferences"
                        rows="3"
                        @blur="formatJson('model_preferences')"
                        class="w-full rounded-md border-input bg-background px-3 py-2 text-sm font-mono ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                        :placeholder="preferencesPlaceholder"
                    ></textarea>
                    <p v-if="form.errors.model_preferences" class="mt-1 text-xs text-destructive">{{ form.errors.model_preferences }}</p>
                </div>

                <!-- Prompt Template -->
                <div class="col-span-2">
                    <label for="prompt_template" class="block text-sm font-medium text-foreground mb-1">Prompt Template</label>
                    <textarea
                        id="prompt_template"
                        v-model="form.prompt_template"
                        rows="12"
                        class="w-full rounded-md border-input bg-background px-3 py-2 text-sm font-mono ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                        placeholder="Enter your prompt template here. Use {{variable_name}} for dynamic content."
                    ></textarea>
                    <p v-if="form.errors.prompt_template" class="mt-1 text-xs text-destructive">{{ form.errors.prompt_template }}</p>
                </div>

                <!-- Variables (JSON) -->
                <div class="col-span-1">
                    <label for="variables" class="block text-sm font-medium text-foreground mb-1">Variables (JSON)</label>
                    <textarea
                        id="variables"
                        v-model="form.variables"
                        rows="6"
                        @blur="formatJson('variables')"
                        class="w-full rounded-md border-input bg-background px-3 py-2 text-sm font-mono ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                        :placeholder="variablesPlaceholder"
                    ></textarea>
                    <p v-if="form.errors.variables" class="mt-1 text-xs text-destructive">{{ form.errors.variables }}</p>
                </div>

                <!-- Output Schema (JSON) -->
                <div class="col-span-1">
                    <label for="output_schema" class="block text-sm font-medium text-foreground mb-1">Output Schema (JSON)</label>
                    <textarea
                        id="output_schema"
                        v-model="form.output_schema"
                        rows="6"
                        @blur="formatJson('output_schema')"
                        class="w-full rounded-md border-input bg-background px-3 py-2 text-sm font-mono ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                        :placeholder="outputSchemaPlaceholder"
                    ></textarea>
                    <p v-if="form.errors.output_schema" class="mt-1 text-xs text-destructive">{{ form.errors.output_schema }}</p>
                </div>

                <!-- Toggles -->
                <div class="col-span-2 flex items-center space-x-8">
                    <div class="flex items-center space-x-2">
                        <input
                            id="is_default"
                            v-model="form.is_default"
                            type="checkbox"
                            class="h-4 w-4 rounded border-input text-primary focus:ring-ring"
                        />
                        <label for="is_default" class="text-sm font-medium text-foreground">Is Default (Sets others to false)</label>
                    </div>

                    <div class="flex items-center space-x-2">
                        <button
                            type="button"
                            class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"
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
            <textarea
                v-model="testVariables"
                rows="4"
                class="w-full rounded-md border-input bg-background/50 px-3 py-2 text-xs font-mono ring-offset-background focus:outline-none focus:ring-1 focus:ring-primary"
                placeholder="Enter JSON variables for testing..."
            ></textarea>
            <p class="mt-1 text-[10px] text-muted-foreground">Input JSON data to populate {{variables}} in your prompt.</p>
        </div>
    </div>
</template>
