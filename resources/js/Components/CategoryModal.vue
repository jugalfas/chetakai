<script setup>
import { useForm } from '@inertiajs/vue3'
import { watch } from 'vue'
import { toast } from 'sonner'

const props = defineProps({
    show: Boolean,
    category: Object,
})

const emit = defineEmits(['close'])

const colors = ['blue-500', 'emerald-500', 'amber-500', 'purple-500', 'rose-500']

const form = useForm({
    name: '',
    color: 'blue-500',
})

watch(
    () => props.category,
    (val) => {
        form.name = val ? val.name : ''
        form.color = val ? val.color : 'blue-500'
    },
    { immediate: true }
)

const submit = () => {
    if (props.category) {
        form.put(`/categories/${props.category.id}`, {
            onSuccess: () => {
                toast.success('Category updated successfully!')
                emit('close')
            },
            onError: () => {
                toast.error('Failed to update category. Please try again.')
            },
        })
    } else {
        form.post('/categories', {
            onSuccess: () => {
                toast.success('Category created successfully!')
                emit('close')
            },
            onError: () => {
                toast.error('Failed to create category. Please try again.')
            },
        })
    }
}
</script>

<template>
    <div>
        <div v-if="show" data-state="open" data-replit-metadata="client/src/components/ui/sheet.tsx:61:4" data-component-name="SheetOverlay" class="fixed inset-0 z-50 bg-black/80 data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0" style="pointer-events: auto;" data-aria-hidden="true" aria-hidden="true"></div>
        <div v-if="show" role="dialog" id="radix-_r_5_" aria-describedby="radix-_r_7_" aria-labelledby="radix-_r_6_" data-state="open" class="fixed z-50 gap-4 shadow-lg transition ease-in-out data-[state=closed]:duration-300 data-[state=open]:duration-500 data-[state=open]:animate-in data-[state=closed]:animate-out inset-y-0 right-0 h-full border-l data-[state=closed]:slide-out-to-right data-[state=open]:slide-in-from-right w-full sm:max-w-md bg-sidebar dark:bg-[#041C32] text-sidebar-foreground dark:text-white border-l-sidebar-border p-0 flex flex-col" tabindex="-1" style="pointer-events: auto;">
        <div class="p-6 flex-1 overflow-y-auto space-y-6">
            <div class="text-center sm:text-left flex flex-row items-center justify-between space-y-0">
                <div>
                    <h2 id="radix-_r_6_" class="text-2xl font-bold text-sidebar-foreground dark:text-white">{{ category ? 'Edit Category' : 'Add Category' }}</h2>
                    <p id="radix-_r_7_" class="text-sm text-sidebar-foreground/60">{{ category ? 'Update the category details below.' : 'Create a new category below.' }}</p>
                </div>
                <button type="button" @click="$emit('close')" class="text-sidebar-foreground dark:text-white hover:opacity-70 transition-opacity">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x h-5 w-5" aria-hidden="true">
                        <path d="M18 6 6 18"></path>
                        <path d="m6 6 12 12"></path>
                    </svg>
                </button>
            </div>
            <form class="space-y-6">
                <div class="space-y-2">
                    <label class="peer-disabled:cursor-not-allowed peer-disabled:opacity-70 text-sm font-bold">Category Name</label>
                    <input v-model="form.name" class="flex w-full rounded-md border px-3 py-1 text-base shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50 md:text-sm bg-muted/20 border-border h-11" :placeholder="category ? 'Update category name' : 'Enter category name'" />
                </div>
                <div class="space-y-2">
                    <label class="peer-disabled:cursor-not-allowed peer-disabled:opacity-70 text-sm font-bold">Theme Color</label>
                    <div class="grid grid-cols-5 gap-3">
                        <button v-for="color in colors" :key="color" type="button" @click="form.color = color" :class="['h-8 rounded-lg transition-all', 'bg-' + color, form.color === color ? 'ring-2 ring-white ring-offset-2 ring-offset-background scale-110' : 'opacity-70 hover:opacity-100']"></button>
                    </div>
                </div>
            </form>
        </div>
        <div class="p-6 border-t border-border flex gap-3">
            <button @click="$emit('close')" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 hover-elevate active-elevate-2 border border-transparent min-h-9 px-4 py-2 flex-1">Cancel</button>
            <button @click="submit" :disabled="form.processing" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 hover-elevate active-elevate-2 border border-primary-border min-h-9 px-4 py-2 flex-1 bg-accent text-accent-foreground hover:bg-accent/90 font-bold">{{ category ? 'Save Changes' : 'Create Category' }}</button>
        </div>
    </div>
    </div>
</template>
