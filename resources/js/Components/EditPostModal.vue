<script setup>
import { useForm } from '@inertiajs/vue3'
import { watch, ref, onUnmounted } from 'vue'
import { toast } from 'vue-sonner'
import { X, Image as ImageIcon, ChevronDown, Check } from 'lucide-vue-next'
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
} from 'radix-vue'

const props = defineProps({
    show: Boolean,
    post: Object,
})

const emit = defineEmits(['close'])

const form = useForm({
    quote: '',
    caption: '',
    image: null,
    content_type: 'image',
})

const fileInput = ref(null)
const previewUrl = ref(null)

watch(
    [() => props.post, () => props.show],
    ([p, show]) => {
        if (show && p) {
            form.quote = p.quote || ''
            form.caption = p.caption || ''
            form.image = null
            form.content_type = p.content_type || 'image'
            const src = p.image_path || null
            previewUrl.value = src ? (src.startsWith('http') ? src : `/storage/${src}`) : null
        }
    },
    { immediate: true }
)

const submit = () => {
    const updateRoute = route('quotes.update', props.post.id)

    form.transform((data) => ({
        ...data,
        _method: 'PUT',
    })).post(updateRoute, {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Post updated successfully!')
            emit('close')
        },
        onError: () => {
            toast.error('Failed to update post. Please try again.')
        },
    })
}

const handleImageUpload = (e) => {
    const file = e.target.files[0]
    if (file) {
        if (previewUrl.value && previewUrl.value.startsWith('blob:')) {
            URL.revokeObjectURL(previewUrl.value)
        }
        form.image = file
        previewUrl.value = URL.createObjectURL(file)
    }
}

const removeImage = (e) => {
    e.stopPropagation()
    if (previewUrl.value && previewUrl.value.startsWith('blob:')) {
        URL.revokeObjectURL(previewUrl.value)
    }
    form.image = null
    const src = props.post?.image_path || null
    previewUrl.value = src ? (src.startsWith('http') ? src : `/storage/${src}`) : null
    if (fileInput.value) {
        fileInput.value.value = ''
    }
}

onUnmounted(() => {
    if (previewUrl.value && previewUrl.value.startsWith('blob:')) {
        URL.revokeObjectURL(previewUrl.value)
    }
})

const getMediaUrl = (path) => {
    if (!path) return null
    return path.startsWith('http') ? path : `/storage/${path}`
}
</script>

<template>
    <div>
        <!-- Overlay -->
        <div v-if="show" class="fixed inset-0 z-50 bg-black/80 data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0" @click="$emit('close')"></div>
        
        <!-- Drawer Panel -->
        <div v-if="show" role="dialog" class="fixed z-50 gap-4 shadow-lg transition ease-in-out data-[state=closed]:duration-300 data-[state=open]:duration-500 data-[state=open]:animate-in data-[state=closed]:animate-out inset-y-0 right-0 h-full border-l data-[state=closed]:slide-out-to-right data-[state=open]:slide-in-from-right w-full sm:max-w-md bg-sidebar dark:bg-[#041C32] text-sidebar-foreground dark:text-white border-l-sidebar-border p-0 flex flex-col">
            <div class="p-6 flex-1 overflow-y-auto space-y-6">
                <!-- Header -->
                <div class="flex flex-row items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-sidebar-foreground dark:text-white">Edit Post</h2>
                        <p class="text-sm text-sidebar-foreground/60">Make changes to the post below.</p>
                    </div>
                    <button type="button" @click="$emit('close')" class="text-sidebar-foreground dark:text-white hover:opacity-70 transition-opacity">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x h-5 w-5">
                            <path d="M18 6 6 18"></path>
                            <path d="m6 6 12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Form -->
                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Post Text -->
                    <div class="space-y-2">
                        <label class="text-sm font-bold">Post Text</label>
                        <div class="relative">
                            <textarea 
                                v-model="form.quote" 
                                class="flex w-full rounded-md border px-3 py-2 text-sm shadow-sm transition-colors placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring bg-muted/20 border-border min-h-[120px] resize-none" 
                                placeholder="Enter the post text"
                            ></textarea>
                            <div class="text-[10px] text-muted-foreground/60 mt-1 text-right">{{ form.quote.length }}/500 characters</div>
                        </div>
                    </div>

                    <!-- Description (Caption) -->
                    <div class="space-y-2">
                        <label class="text-sm font-bold">Description</label>
                        <div class="relative">
                            <textarea 
                                v-model="form.caption" 
                                class="flex w-full rounded-md border px-3 py-2 text-sm shadow-sm transition-colors placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring bg-muted/20 border-border min-h-[150px] resize-none" 
                                placeholder="Enter a description for this post"
                            ></textarea>
                            <div class="text-[10px] text-muted-foreground/60 mt-1 text-right">{{ form.caption.length }}/300 characters</div>
                        </div>
                    </div>

                    <div v-if="form.content_type === 'image'" class="space-y-2">
                        <label class="text-sm font-bold">Image Assets</label>
                        <div 
                            @click="fileInput.click()"
                            class="relative group border-2 border-dashed border-border rounded-lg overflow-hidden flex flex-col items-center justify-center gap-2 cursor-pointer hover:bg-muted/10 transition-colors bg-muted/5 min-h-[200px]"
                        >
                            <!-- Preview Image -->
                            <template v-if="previewUrl">
                                <img :src="previewUrl" class="absolute inset-0 w-full h-full object-cover" />
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                    <p class="text-white text-sm font-medium">Click to change image</p>
                                </div>
                                <button 
                                    v-if="form.image"
                                    @click="removeImage" 
                                    class="absolute top-2 right-2 p-1 bg-red-500 text-white rounded-full hover:bg-red-600 transition-colors z-10"
                                >
                                    <X class="h-4 w-4" />
                                </button>
                            </template>

                            <!-- Placeholder -->
                            <template v-else>
                                <ImageIcon class="text-muted-foreground h-8 w-8" />
                                <span class="text-sm text-muted-foreground">Upload post background image</span>
                            </template>

                            <input 
                                ref="fileInput"
                                type="file" 
                                class="hidden" 
                                @change="handleImageUpload" 
                                accept="image/*"
                            />
                        </div>
                        <p v-if="form.image" class="text-[10px] text-accent mt-1">Selected: {{ form.image.name }}</p>
                    </div>

                    <div v-else class="space-y-2">
                        <label class="text-sm font-bold">Reel Video</label>
                        <div class="rounded-lg border border-border bg-muted/5 p-4 flex items-center justify-between">
                            <div class="text-sm text-muted-foreground">
                                <div class="font-medium text-foreground">Download current reel</div>
                                <div class="text-xs mt-1 break-all">
                                    {{ props.post?.video_path ? getMediaUrl(props.post.video_path) : 'No reel video available' }}
                                </div>
                            </div>
                            <a
                                v-if="props.post?.video_path"
                                :href="getMediaUrl(props.post.video_path)"
                                download
                                class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring border border-transparent min-h-9 px-4 py-2 bg-accent text-white hover:opacity-90"
                            >
                                Download
                            </a>
                        </div>
                    </div>

                    <!-- Content Type -->
                    <div class="space-y-2">
                        <label class="text-sm font-bold">Content Type</label>
                        <SelectRoot v-model="form.content_type">
                            <SelectTrigger
                                class="flex h-11 w-full items-center justify-between rounded-md border px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring bg-muted/20 border-border text-foreground capitalize"
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
                                            value="image"
                                            class="relative flex w-full cursor-default select-none items-center rounded-sm py-1.5 pl-2 pr-9 text-sm outline-none focus:bg-accent focus:text-accent-foreground data-[state=checked]:bg-[#0F3D57] data-[state=checked]:text-white data-[disabled]:pointer-events-none data-[disabled]:opacity-50 hover:bg-muted/20 transition-colors capitalize"
                                        >
                                            <SelectItemText>Image</SelectItemText>
                                            <SelectItemIndicator class="absolute right-3 flex h-4 w-4 items-center justify-center">
                                                <Check class="h-4 w-4 stroke-[3px]" />
                                            </SelectItemIndicator>
                                        </SelectItem>
                                        <SelectItem
                                            value="reel"
                                            class="relative flex w-full cursor-default select-none items-center rounded-sm py-1.5 pl-2 pr-9 text-sm outline-none focus:bg-accent focus:text-accent-foreground data-[state=checked]:bg-[#0F3D57] data-[state=checked]:text-white data-[disabled]:pointer-events-none data-[disabled]:opacity-50 hover:bg-muted/20 transition-colors capitalize"
                                        >
                                            <SelectItemText>Reel</SelectItemText>
                                            <SelectItemIndicator class="absolute right-3 flex h-4 w-4 items-center justify-center">
                                                <Check class="h-4 w-4 stroke-[3px]" />
                                            </SelectItemIndicator>
                                        </SelectItem>
                                    </SelectViewport>
                                </SelectContent>
                            </SelectPortal>
                        </SelectRoot>
                        <div class="text-[10px] text-muted-foreground/60 mt-1">
                            Choose whether this is a standard post or a reel.
                        </div>
                    </div>
                </form>
            </div>

            <!-- Footer -->
            <div class="p-6 border-t border-border flex gap-3">
                <button @click="$emit('close')" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring border border-transparent min-h-9 px-4 py-2 flex-1 hover:bg-muted/20">Cancel</button>
                <button @click="submit" :disabled="form.processing" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 border border-primary-border min-h-9 px-4 py-2 flex-1 bg-[#0F3D57] hover:bg-[#0F3D57]/90 text-white font-bold">
                    {{ form.processing ? 'Saving...' : 'Save Changes' }}
                </button>
            </div>
        </div>
    </div>
</template>
