<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router, useForm } from "@inertiajs/vue3";
import { ref, computed, watch } from "vue";
import { ChevronDown, Check } from "lucide-vue-next";
import {
    SelectRoot,
    SelectTrigger,
    SelectValue,
    SelectContent,
    SelectViewport,
    SelectItem,
    SelectPortal,
    SelectItemText,
    SelectItemIndicator,
} from "radix-vue";
import { toast } from "vue-sonner";

const props = defineProps({
    categories: Array,
    templates: Array,
    history: Array,
});

const contentTypes = [
    { value: 'text_post', label: 'Text Post' },
    { value: 'quote_post', label: 'Quote Post' },
    { value: 'carousel_post', label: 'Carousel Post' },
    { value: 'reel_script', label: 'Reel Script' },
    { value: 'ai_image', label: 'AI Image' },
    { value: 'meme', label: 'Meme' },
    { value: 'story_post', label: 'Story Post' },
    { value: 'caption_hashtags', label: 'Caption + Hashtags' },
    { value: 'content_ideas', label: 'Content Ideas' },
];

const goals = ['Educate','Inspire','Entertain','Promote','Storytelling','Viral Engagement'];
const tones = ['Inspirational','Deep','Emotional','Calm','Tough Love','Relatable','Funny','Informative','Storytelling'];
const lengths = ['Short','Medium','Long'];
const styles = ['Instagram Viral','Minimal','Advice Style','Journal Style','Story Style','Educational','Motivational'];
const memeTypes = ['Relatable','Sarcastic','Dark Humor','Wholesome'];
const imageStyles = ['Cinematic','Realistic','Anime','Fantasy','Digital Art','Cyberpunk','Minimal','3D Render'];
const imageMoods = ['Dark','Inspirational','Epic','Peaceful','Dreamy'];
const imageLight = ['Soft','Studio','Dramatic','Neon'];

const categories = ref(props.categories || []);
const templates = ref(props.templates || []);
const history = ref(props.history || []);

const form = useForm({
    content_type: 'text_post',
    category: '',
    goal: '',
    tone: '',
    audience: '',
    custom_audience: '',
    length: 'Medium',
    style: '',
    bulk: 1,
    options: {
        carousel_slides: 5,
        image_subject: '',
        image_style: '',
        image_mood: '',
        image_lighting: '',
        meme_type: '',
    }
});

const showCustomCategory = ref(false);
const newCategory = ref('');
const savingCategory = ref(false);

const audienceOptions = ['Everyone','Men','Women','Students','Entrepreneurs','Gym beginners','Creators','Business owners','People going through breakup','Custom'];
const selectedAudience = ref('Everyone');
watch(selectedAudience, v => {
    if (v !== 'Custom') form.audience = v;
    if (v === 'Custom') form.audience = form.custom_audience || '';
});
watch(() => form.custom_audience, v => {
    if (selectedAudience.value === 'Custom') form.audience = v;
});

const typeLabel = computed(() => contentTypes.find(t => t.value === form.content_type)?.label || '');
const contentTypeValues = computed(() => contentTypes.map(t => ({ value: t.value, label: t.label })));

const buildPreview = (s) => {
    const type = s.content_type;
    if (type === 'quote_post') {
        return 'Short, original quote with emotional impact and clear language.';
    }
    if (type === 'carousel_post') {
        const slides = Number(s.options.carousel_slides || 5);
        const lines = ['Slide 1: Hook'];
        for (let i = 2; i < slides; i++) lines.push(`Slide ${i}: Educational point`);
        lines.push(`Slide ${slides}: Conclusion or CTA`);
        return lines.join('\\n');
    }
    if (type === 'reel_script') {
        return 'Hook (3s)\\nMain message\\nCall to action';
    }
    if (type === 'ai_image') {
        return 'Descriptive image prompt based on subject, style, mood, lighting.';
    }
    if (type === 'meme') {
        return 'Top text\\nBottom text';
    }
    if (type === 'caption_hashtags') {
        return 'Caption text\\n#hashtag1 #hashtag2 ...';
    }
    if (type === 'content_ideas') {
        return '10 short content ideas';
    }
    return 'Short post content';
};
const preview = computed(() => buildPreview(form));

const generateProcessing = ref(false);
const generate = () => {
    generateProcessing.value = true;
    router.post(route('studio.generate'), form, {
        preserveScroll: true,
        onSuccess: (page) => {},
        onError: () => toast.error('Generation failed'),
        onFinish: () => {
            generateProcessing.value = false;
        },
        onBefore: () => {},
        onProgress: () => {},
        only: [],
        headers: {},
        onCancelToken: () => {},
        replace: false,
        method: 'post',
    });
};

const generateViaFetch = async () => {
    try {
        generateProcessing.value = true;
        const res = await fetch(route('studio.generate'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content')
            },
            body: JSON.stringify(form.data()),
        });
        const data = await res.json();
        if (data?.results) {
            data.results.forEach(r => {
                history.value.unshift({
                    id: r.id,
                    content_type: form.content_type,
                    category: form.category,
                    result: r.result,
                    created_at: new Date().toISOString(),
                });
            });
            toast.success('Generated content');
        } else {
            toast.error('Generation failed');
        }
    } catch (e) {
        toast.error('Generation error');
    } finally {
        generateProcessing.value = false;
    }
};

const saveTemplateName = ref('');
const savingTemplate = ref(false);
const saveTemplate = async () => {
    if (!saveTemplateName.value) {
        toast.error('Enter a template name');
        return;
    }
    savingTemplate.value = true;
    try {
        const res = await fetch(route('studio.templates.store'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content')
            },
            body: JSON.stringify({
                template_name: saveTemplateName.value,
                content_type: form.content_type,
                settings_json: form.data()
            }),
        });
        const data = await res.json();
        templates.value.unshift(data);
        saveTemplateName.value = '';
        toast.success('Template saved');
    } catch (e) {
        toast.error('Failed to save template');
    } finally {
        savingTemplate.value = false;
    }
};

const applyTemplate = (tpl) => {
    const s = tpl.settings_json || {};
    form.content_type = s.content_type || tpl.content_type;
    form.category = s.category || '';
    form.goal = s.goal || '';
    form.tone = s.tone || '';
    selectedAudience.value = 'Everyone';
    form.audience = s.audience || '';
    form.custom_audience = s.custom_audience || '';
    if (form.custom_audience) selectedAudience.value = 'Custom';
    form.length = s.length || 'Medium';
    form.style = s.style || '';
    form.bulk = s.bulk || 1;
    form.options.carousel_slides = s.options?.carousel_slides ?? 5;
    form.options.image_subject = s.options?.image_subject ?? '';
    form.options.image_style = s.options?.image_style ?? '';
    form.options.image_mood = s.options?.image_mood ?? '';
    form.options.image_lighting = s.options?.image_lighting ?? '';
    form.options.meme_type = s.options?.meme_type ?? '';
    toast.success('Template applied');
};

const addCustomCategory = async () => {
    if (!newCategory.value) return;
    savingCategory.value = true;
    try {
        const res = await fetch(route('studio.categories.store'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content')
            },
            body: JSON.stringify({ name: newCategory.value }),
        });
        const data = await res.json();
        if (data?.name) {
            if (!categories.value.includes(data.name)) categories.value.push(data.name);
            form.category = data.name;
            newCategory.value = '';
            showCustomCategory.value = false;
            toast.success('Category added');
        } else {
            toast.error('Failed to add category');
        }
    } catch (e) {
        toast.error('Failed to add category');
    } finally {
        savingCategory.value = false;
    }
};
</script>

<template>
    <Head title="Content Studio" />
    <AuthenticatedLayout>
        <div class="space-y-8">
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-foreground">Content Studio</h1>
                <p class="text-muted-foreground mt-1">Generate and manage Instagram content.</p>
            </div>

            <div class="grid gap-6 md:grid-cols-3">
                <div class="md:col-span-2 space-y-6">
                    <div class="rounded-xl border border-border bg-card p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="font-bold text-lg">Create Content</div>
                            <div class="text-xs uppercase font-bold text-muted-foreground">{{ typeLabel }}</div>
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="space-y-2">
                                <label class="text-xs font-bold uppercase">Content Type</label>
                                <SelectRoot v-model="form.content_type">
                                    <SelectTrigger
                                        class="flex h-11 w-full items-center justify-between rounded-md border px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring bg-muted/20 border-border text-foreground"
                                    >
                                        <SelectValue placeholder="Select content type" />
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
                                                    v-for="opt in contentTypeValues"
                                                    :key="opt.value"
                                                    :value="opt.value"
                                                    class="relative flex w-full cursor-default select-none items-center rounded-sm py-1.5 pl-2 pr-9 text-sm outline-none focus:bg-accent focus:text-accent-foreground data-[state=checked]:bg-[#0F3D57] data-[state=checked]:text-white data-[disabled]:pointer-events-none data-[disabled]:opacity-50 hover:bg-muted/20 transition-colors"
                                                >
                                                    <SelectItemText>{{ opt.label }}</SelectItemText>
                                                    <SelectItemIndicator class="absolute right-3 flex h-4 w-4 items-center justify-center">
                                                        <Check class="h-4 w-4 stroke-[3px]" />
                                                    </SelectItemIndicator>
                                                </SelectItem>
                                            </SelectViewport>
                                        </SelectContent>
                                    </SelectPortal>
                                </SelectRoot>
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold uppercase">Category</label>
                                <SelectRoot v-model="form.category">
                                    <SelectTrigger
                                        class="flex h-11 w-full items-center justify-between rounded-md border px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring bg-muted/20 border-border text-foreground"
                                    >
                                        <SelectValue placeholder="Select category" />
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
                                                    v-for="name in categories"
                                                    :key="name"
                                                    :value="name"
                                                    class="relative flex w-full cursor-default select-none items-center rounded-sm py-1.5 pl-2 pr-9 text-sm outline-none focus:bg-accent focus:text-accent-foreground data-[state=checked]:bg-[#0F3D57] data-[state=checked]:text-white data-[disabled]:pointer-events-none data-[disabled]:opacity-50 hover:bg-muted/20 transition-colors"
                                                >
                                                    <SelectItemText>{{ name }}</SelectItemText>
                                                    <SelectItemIndicator class="absolute right-3 flex h-4 w-4 items-center justify-center">
                                                        <Check class="h-4 w-4 stroke-[3px]" />
                                                    </SelectItemIndicator>
                                                </SelectItem>
                                            </SelectViewport>
                                        </SelectContent>
                                    </SelectPortal>
                                </SelectRoot>
                                <div class="flex gap-2">
                                    <button class="text-xs underline" @click="showCustomCategory = !showCustomCategory">Add Custom Category</button>
                                </div>
                                <div v-if="showCustomCategory" class="flex gap-2 mt-2">
                                    <input v-model="newCategory" class="flex-1 rounded-md border border-border bg-muted/20 p-2 text-sm" placeholder="Category name" />
                                    <button @click="addCustomCategory" :disabled="savingCategory" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-xs font-semibold transition-colors border shadow-xs h-9 px-3 border-border bg-accent text-accent-foreground">
                                        {{ savingCategory ? 'Saving...' : 'Save' }}
                                    </button>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label class="text-xs font-bold uppercase">Content Goal</label>
                                <SelectRoot v-model="form.goal">
                                    <SelectTrigger class="flex h-11 w-full items-center justify-between rounded-md border px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring bg-muted/20 border-border text-foreground">
                                        <SelectValue placeholder="Select goal" />
                                        <ChevronDown class="h-4 w-4 opacity-50" />
                                    </SelectTrigger>
                                    <SelectPortal>
                                        <SelectContent class="z-[100] w-[--radix-select-trigger-width] overflow-hidden rounded-md border bg-popover text-popover-foreground shadow-md data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0 data-[state=closed]:zoom-out-95 data-[state=open]:zoom-in-95 data-[side=bottom]:slide-in-from-top-2 data-[side=left]:slide-in-from-right-2 data-[side=right]:slide-in-from-left-2 data-[side=top]:slide-in-from-bottom-2 bg-[#041C32] border-sidebar-border" position="popper" :side-offset="5">
                                            <SelectViewport class="p-1">
                                                <SelectItem v-for="g in goals" :key="g" :value="g" class="relative flex w-full cursor-default select-none items-center rounded-sm py-1.5 pl-2 pr-9 text-sm outline-none focus:bg-accent focus:text-accent-foreground data-[state=checked]:bg-[#0F3D57] data-[state=checked]:text-white data-[disabled]:pointer-events-none data-[disabled]:opacity-50 hover:bg-muted/20 transition-colors">
                                                    <SelectItemText>{{ g }}</SelectItemText>
                                                    <SelectItemIndicator class="absolute right-3 flex h-4 w-4 items-center justify-center">
                                                        <Check class="h-4 w-4 stroke-[3px]" />
                                                    </SelectItemIndicator>
                                                </SelectItem>
                                            </SelectViewport>
                                        </SelectContent>
                                    </SelectPortal>
                                </SelectRoot>
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold uppercase">Tone</label>
                                <SelectRoot v-model="form.tone">
                                    <SelectTrigger class="flex h-11 w-full items-center justify-between rounded-md border px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring bg-muted/20 border-border text-foreground">
                                        <SelectValue placeholder="Select tone" />
                                        <ChevronDown class="h-4 w-4 opacity-50" />
                                    </SelectTrigger>
                                    <SelectPortal>
                                        <SelectContent class="z-[100] w-[--radix-select-trigger-width] overflow-hidden rounded-md border bg-popover text-popover-foreground shadow-md data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0 data-[state=closed]:zoom-out-95 data-[state=open]:zoom-in-95 data-[side=bottom]:slide-in-from-top-2 data-[side=left]:slide-in-from-right-2 data-[side=right]:slide-in-from-left-2 data-[side=top]:slide-in-from-bottom-2 bg-[#041C32] border-sidebar-border" position="popper" :side-offset="5">
                                            <SelectViewport class="p-1">
                                                <SelectItem v-for="t in tones" :key="t" :value="t" class="relative flex w-full cursor-default select-none items-center rounded-sm py-1.5 pl-2 pr-9 text-sm outline-none focus:bg-accent focus:text-accent-foreground data-[state=checked]:bg-[#0F3D57] data-[state=checked]:text-white data-[disabled]:pointer-events-none data-[disabled]:opacity-50 hover:bg-muted/20 transition-colors">
                                                    <SelectItemText>{{ t }}</SelectItemText>
                                                    <SelectItemIndicator class="absolute right-3 flex h-4 w-4 items-center justify-center">
                                                        <Check class="h-4 w-4 stroke-[3px]" />
                                                    </SelectItemIndicator>
                                                </SelectItem>
                                            </SelectViewport>
                                        </SelectContent>
                                    </SelectPortal>
                                </SelectRoot>
                            </div>

                            <div class="space-y-2">
                                <label class="text-xs font-bold uppercase">Audience</label>
                                <SelectRoot v-model="selectedAudience">
                                    <SelectTrigger class="flex h-11 w-full items-center justify-between rounded-md border px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring bg-muted/20 border-border text-foreground">
                                        <SelectValue placeholder="Select audience" />
                                        <ChevronDown class="h-4 w-4 opacity-50" />
                                    </SelectTrigger>
                                    <SelectPortal>
                                        <SelectContent class="z-[100] w-[--radix-select-trigger-width] overflow-hidden rounded-md border bg-popover text-popover-foreground shadow-md data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0 data-[state=closed]:zoom-out-95 data-[state=open]:zoom-in-95 data-[side=bottom]:slide-in-from-top-2 data-[side=left]:slide-in-from-right-2 data-[side=right]:slide-in-from-left-2 data-[side=top]:slide-in-from-bottom-2 bg-[#041C32] border-sidebar-border" position="popper" :side-offset="5">
                                            <SelectViewport class="p-1">
                                                <SelectItem v-for="a in audienceOptions" :key="a" :value="a" class="relative flex w-full cursor-default select-none items-center rounded-sm py-1.5 pl-2 pr-9 text-sm outline-none focus:bg-accent focus:text-accent-foreground data-[state=checked]:bg-[#0F3D57] data-[state=checked]:text-white data-[disabled]:pointer-events-none data-[disabled]:opacity-50 hover:bg-muted/20 transition-colors">
                                                    <SelectItemText>{{ a }}</SelectItemText>
                                                    <SelectItemIndicator class="absolute right-3 flex h-4 w-4 items-center justify-center">
                                                        <Check class="h-4 w-4 stroke-[3px]" />
                                                    </SelectItemIndicator>
                                                </SelectItem>
                                            </SelectViewport>
                                        </SelectContent>
                                    </SelectPortal>
                                </SelectRoot>
                                <input v-if="selectedAudience === 'Custom'" v-model="form.custom_audience" class="mt-2 rounded-md border border-border bg-muted/20 p-2 text-sm w-full" placeholder="Describe target audience" />
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold uppercase">Length</label>
                                <SelectRoot v-model="form.length">
                                    <SelectTrigger class="flex h-11 w-full items-center justify-between rounded-md border px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring bg-muted/20 border-border text-foreground">
                                        <SelectValue placeholder="Select length" />
                                        <ChevronDown class="h-4 w-4 opacity-50" />
                                    </SelectTrigger>
                                    <SelectPortal>
                                        <SelectContent class="z-[100] w-[--radix-select-trigger-width] overflow-hidden rounded-md border bg-popover text-popover-foreground shadow-md data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0 data-[state=closed]:zoom-out-95 data-[state=open]:zoom-in-95 data-[side=bottom]:slide-in-from-top-2 data-[side=left]:slide-in-from-right-2 data-[side=right]:slide-in-from-left-2 data-[side=top]:slide-in-from-bottom-2 bg-[#041C32] border-sidebar-border" position="popper" :side-offset="5">
                                            <SelectViewport class="p-1">
                                                <SelectItem v-for="l in lengths" :key="l" :value="l" class="relative flex w-full cursor-default select-none items-center rounded-sm py-1.5 pl-2 pr-9 text-sm outline-none focus:bg-accent focus:text-accent-foreground data-[state=checked]:bg-[#0F3D57] data-[state=checked]:text-white data-[disabled]:pointer-events-none data-[disabled]:opacity-50 hover:bg-muted/20 transition-colors">
                                                    <SelectItemText>{{ l }}</SelectItemText>
                                                    <SelectItemIndicator class="absolute right-3 flex h-4 w-4 items-center justify-center">
                                                        <Check class="h-4 w-4 stroke-[3px]" />
                                                    </SelectItemIndicator>
                                                </SelectItem>
                                            </SelectViewport>
                                        </SelectContent>
                                    </SelectPortal>
                                </SelectRoot>
                            </div>

                            <div class="space-y-2">
                                <label class="text-xs font-bold uppercase">Style</label>
                                <SelectRoot v-model="form.style">
                                    <SelectTrigger class="flex h-11 w-full items-center justify-between rounded-md border px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring bg-muted/20 border-border text-foreground">
                                        <SelectValue placeholder="Select style" />
                                        <ChevronDown class="h-4 w-4 opacity-50" />
                                    </SelectTrigger>
                                    <SelectPortal>
                                        <SelectContent class="z-[100] w-[--radix-select-trigger-width] overflow-hidden rounded-md border bg-popover text-popover-foreground shadow-md data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0 data-[state=closed]:zoom-out-95 data-[state=open]:zoom-in-95 data-[side=bottom]:slide-in-from-top-2 data-[side=left]:slide-in-from-right-2 data-[side=right]:slide-in-from-left-2 data-[side=top]:slide-in-from-bottom-2 bg-[#041C32] border-sidebar-border" position="popper" :side-offset="5">
                                            <SelectViewport class="p-1">
                                                <SelectItem v-for="s in styles" :key="s" :value="s" class="relative flex w-full cursor-default select-none items-center rounded-sm py-1.5 pl-2 pr-9 text-sm outline-none focus:bg-accent focus:text-accent-foreground data-[state=checked]:bg-[#0F3D57] data-[state=checked]:text-white data-[disabled]:pointer-events-none data-[disabled]:opacity-50 hover:bg-muted/20 transition-colors">
                                                    <SelectItemText>{{ s }}</SelectItemText>
                                                    <SelectItemIndicator class="absolute right-3 flex h-4 w-4 items-center justify-center">
                                                        <Check class="h-4 w-4 stroke-[3px]" />
                                                    </SelectItemIndicator>
                                                </SelectItem>
                                            </SelectViewport>
                                        </SelectContent>
                                    </SelectPortal>
                                </SelectRoot>
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold uppercase">Bulk Generate</label>
                                <input type="number" v-model.number="form.bulk" min="1" max="20" class="rounded-md border border-border bg-muted/20 p-2 text-sm w-full" />
                            </div>
                        </div>

                        <div class="mt-6 border-t border-border pt-4">
                            <div v-if="form.content_type === 'quote_post'" class="text-sm text-muted-foreground">Quote post will follow short, simple, original rules.</div>
                            <div v-if="form.content_type === 'carousel_post'" class="grid gap-3 md:grid-cols-2">
                                <div class="space-y-2">
                                    <label class="text-xs font-bold uppercase">Number of Slides</label>
                                    <input type="number" min="3" max="10" v-model.number="form.options.carousel_slides" class="rounded-md border border-border bg-muted/20 p-2 text-sm w-full" />
                                </div>
                            </div>
                            <div v-if="form.content_type === 'reel_script'" class="text-sm text-muted-foreground">Structure: Hook (3s), Main, CTA.</div>
                            <div v-if="form.content_type === 'ai_image'" class="grid gap-3 md:grid-cols-2">
                                <div class="space-y-2">
                                    <label class="text-xs font-bold uppercase">Image Subject</label>
                                    <input v-model="form.options.image_subject" class="rounded-md border border-border bg-muted/20 p-2 text-sm w-full" placeholder="Subject" />
                                </div>
                                <div class="space-y-2">
                                    <label class="text-xs font-bold uppercase">Style</label>
                                    <SelectRoot v-model="form.options.image_style">
                                        <SelectTrigger class="flex h-11 w-full items-center justify-between rounded-md border px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring bg-muted/20 border-border text-foreground">
                                            <SelectValue placeholder="Select style" />
                                            <ChevronDown class="h-4 w-4 opacity-50" />
                                        </SelectTrigger>
                                        <SelectPortal>
                                            <SelectContent class="z-[100] w-[--radix-select-trigger-width] overflow-hidden rounded-md border bg-popover text-popover-foreground shadow-md data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0 data-[state=closed]:zoom-out-95 data-[state=open]:zoom-in-95 data-[side=bottom]:slide-in-from-top-2 data-[side=left]:slide-in-from-right-2 data-[side=right]:slide-in-from-left-2 data-[side=top]:slide-in-from-bottom-2 bg-[#041C32] border-sidebar-border" position="popper" :side-offset="5">
                                                <SelectViewport class="p-1">
                                                    <SelectItem v-for="s in imageStyles" :key="s" :value="s" class="relative flex w-full cursor-default select-none items-center rounded-sm py-1.5 pl-2 pr-9 text-sm outline-none focus:bg-accent focus:text-accent-foreground data-[state=checked]:bg-[#0F3D57] data-[state=checked]:text-white data-[disabled]:pointer-events-none data-[disabled]:opacity-50 hover:bg-muted/20 transition-colors">
                                                        <SelectItemText>{{ s }}</SelectItemText>
                                                        <SelectItemIndicator class="absolute right-3 flex h-4 w-4 items-center justify-center">
                                                            <Check class="h-4 w-4 stroke-[3px]" />
                                                        </SelectItemIndicator>
                                                    </SelectItem>
                                                </SelectViewport>
                                            </SelectContent>
                                        </SelectPortal>
                                    </SelectRoot>
                                </div>
                                <div class="space-y-2">
                                    <label class="text-xs font-bold uppercase">Mood</label>
                                    <SelectRoot v-model="form.options.image_mood">
                                        <SelectTrigger class="flex h-11 w-full items-center justify-between rounded-md border px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring bg-muted/20 border-border text-foreground">
                                            <SelectValue placeholder="Select mood" />
                                            <ChevronDown class="h-4 w-4 opacity-50" />
                                        </SelectTrigger>
                                        <SelectPortal>
                                            <SelectContent class="z-[100] w-[--radix-select-trigger-width] overflow-hidden rounded-md border bg-popover text-popover-foreground shadow-md data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0 data-[state=closed]:zoom-out-95 data-[state=open]:zoom-in-95 data-[side=bottom]:slide-in-from-top-2 data-[side=left]:slide-in-from-right-2 data-[side=right]:slide-in-from-left-2 data-[side=top]:slide-in-from-bottom-2 bg-[#041C32] border-sidebar-border" position="popper" :side-offset="5">
                                                <SelectViewport class="p-1">
                                                    <SelectItem v-for="m in imageMoods" :key="m" :value="m" class="relative flex w-full cursor-default select-none items-center rounded-sm py-1.5 pl-2 pr-9 text-sm outline-none focus:bg-accent focus:text-accent-foreground data-[state=checked]:bg-[#0F3D57] data-[state=checked]:text-white data-[disabled]:pointer-events-none data-[disabled]:opacity-50 hover:bg-muted/20 transition-colors">
                                                        <SelectItemText>{{ m }}</SelectItemText>
                                                        <SelectItemIndicator class="absolute right-3 flex h-4 w-4 items-center justify-center">
                                                            <Check class="h-4 w-4 stroke-[3px]" />
                                                        </SelectItemIndicator>
                                                    </SelectItem>
                                                </SelectViewport>
                                            </SelectContent>
                                        </SelectPortal>
                                    </SelectRoot>
                                </div>
                                <div class="space-y-2">
                                    <label class="text-xs font-bold uppercase">Lighting</label>
                                    <SelectRoot v-model="form.options.image_lighting">
                                        <SelectTrigger class="flex h-11 w-full items-center justify-between rounded-md border px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring bg-muted/20 border-border text-foreground">
                                            <SelectValue placeholder="Select lighting" />
                                            <ChevronDown class="h-4 w-4 opacity-50" />
                                        </SelectTrigger>
                                        <SelectPortal>
                                            <SelectContent class="z-[100] w-[--radix-select-trigger-width] overflow-hidden rounded-md border bg-popover text-popover-foreground shadow-md data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0 data-[state=closed]:zoom-out-95 data-[state=open]:zoom-in-95 data-[side=bottom]:slide-in-from-top-2 data-[side=left]:slide-in-from-right-2 data-[side=right]:slide-in-from-left-2 data-[side=top]:slide-in-from-bottom-2 bg-[#041C32] border-sidebar-border" position="popper" :side-offset="5">
                                                <SelectViewport class="p-1">
                                                    <SelectItem v-for="l in imageLight" :key="l" :value="l" class="relative flex w-full cursor-default select-none items-center rounded-sm py-1.5 pl-2 pr-9 text-sm outline-none focus:bg-accent focus:text-accent-foreground data-[state=checked]:bg-[#0F3D57] data-[state=checked]:text-white data-[disabled]:pointer-events-none data-[disabled]:opacity-50 hover:bg-muted/20 transition-colors">
                                                        <SelectItemText>{{ l }}</SelectItemText>
                                                        <SelectItemIndicator class="absolute right-3 flex h-4 w-4 items-center justify-center">
                                                            <Check class="h-4 w-4 stroke-[3px]" />
                                                        </SelectItemIndicator>
                                                    </SelectItem>
                                                </SelectViewport>
                                            </SelectContent>
                                        </SelectPortal>
                                    </SelectRoot>
                                </div>
                            </div>
                            <div v-if="form.content_type === 'meme'" class="grid gap-3 md:grid-cols-2">
                                <div class="space-y-2">
                                    <label class="text-xs font-bold uppercase">Meme Type</label>
                                    <SelectRoot v-model="form.options.meme_type">
                                        <SelectTrigger class="flex h-11 w-full items-center justify-between rounded-md border px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring bg-muted/20 border-border text-foreground">
                                            <SelectValue placeholder="Select meme type" />
                                            <ChevronDown class="h-4 w-4 opacity-50" />
                                        </SelectTrigger>
                                        <SelectPortal>
                                            <SelectContent class="z-[100] w-[--radix-select-trigger-width] overflow-hidden rounded-md border bg-popover text-popover-foreground shadow-md data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0 data-[state=closed]:zoom-out-95 data-[state=open]:zoom-in-95 data-[side=bottom]:slide-in-from-top-2 data-[side=left]:slide-in-from-right-2 data-[side=right]:slide-in-from-left-2 data-[side=top]:slide-in-from-bottom-2 bg-[#041C32] border-sidebar-border" position="popper" :side-offset="5">
                                                <SelectViewport class="p-1">
                                                    <SelectItem v-for="m in memeTypes" :key="m" :value="m" class="relative flex w-full cursor-default select-none items-center rounded-sm py-1.5 pl-2 pr-9 text-sm outline-none focus:bg-accent focus:text-accent-foreground data-[state=checked]:bg-[#0F3D57] data-[state=checked]:text-white data-[disabled]:pointer-events-none data-[disabled]:opacity-50 hover:bg-muted/20 transition-colors">
                                                        <SelectItemText>{{ m }}</SelectItemText>
                                                        <SelectItemIndicator class="absolute right-3 flex h-4 w-4 items-center justify-center">
                                                            <Check class="h-4 w-4 stroke-[3px]" />
                                                        </SelectItemIndicator>
                                                    </SelectItem>
                                                </SelectViewport>
                                            </SelectContent>
                                        </SelectPortal>
                                    </SelectRoot>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 grid gap-4 md:grid-cols-2">
                            <div class="space-y-2">
                                <label class="text-xs font-bold uppercase">Preview</label>
                                <textarea :value="preview" class="w-full h-48 rounded-md border border-border bg-muted/20 p-3 text-sm resize-vertical overflow-auto" readonly></textarea>
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold uppercase">Save Template</label>
                                <div class="flex gap-2">
                                    <input v-model="saveTemplateName" class="flex-1 rounded-md border border-border bg-muted/20 p-2 text-sm" placeholder="Template name" />
                                    <button @click="saveTemplate" :disabled="savingTemplate" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-xs font-semibold transition-colors border shadow-xs h-9 px-4 border-border bg-primary text-primary-foreground">
                                        {{ savingTemplate ? 'Saving...' : 'Save' }}
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 flex items-center justify-end gap-3">
                            <button @click="generateViaFetch" :disabled="generateProcessing" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium transition-colors border shadow-xs h-10 px-6 border-border bg-accent text-accent-foreground">
                                {{ generateProcessing ? 'Generating...' : 'Generate' }}
                            </button>
                        </div>
                    </div>

                    <div class="rounded-xl border border-border bg-card p-6">
                        <div class="font-bold text-lg mb-4">Generated Content History</div>
                        <div v-if="history.length === 0" class="text-sm text-muted-foreground">No generated content yet.</div>
                        <div v-else class="space-y-4">
                            <div v-for="item in history" :key="item.id" class="rounded-lg border border-border p-4">
                                <div class="flex items-center justify-between text-xs text-muted-foreground mb-2">
                                    <div class="uppercase font-bold">{{ item.content_type }}</div>
                                    <div>{{ item.created_at }}</div>
                                </div>
                                <pre class="whitespace-pre-wrap text-sm">{{ item.result }}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="rounded-xl border border-border bg-card p-6">
                        <div class="font-bold text-lg mb-4">Prompt Templates</div>
                        <div v-if="templates.length === 0" class="text-sm text-muted-foreground">No saved templates yet.</div>
                        <div v-else class="space-y-3">
                            <div v-for="tpl in templates" :key="tpl.id" class="flex items-center justify-between rounded-md border border-border p-3">
                                <div>
                                    <div class="font-semibold text-sm">{{ tpl.template_name }}</div>
                                    <div class="text-xs text-muted-foreground">{{ tpl.content_type }}</div>
                                </div>
                                <div class="flex gap-2">
                                    <button @click="applyTemplate(tpl)" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-xs font-semibold transition-colors border shadow-xs h-8 px-3 border-border">Use</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-xl border border-border bg-card p-6">
                        <div class="font-bold text-lg mb-4">Content Ideas</div>
                        <div class="text-sm text-muted-foreground">Use Content Type: Content Ideas to generate ideas.</div>
                    </div>

                    <div class="rounded-xl border border-border bg-card p-6">
                        <div class="font-bold text-lg mb-4">AI Image Generator</div>
                        <div class="text-sm text-muted-foreground">Use Content Type: AI Image with subject, style, mood, lighting.</div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
</style>
