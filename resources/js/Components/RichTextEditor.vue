<script setup>
import { useEditor, EditorContent } from '@tiptap/vue-3';
import StarterKit from '@tiptap/starter-kit';
import { onBeforeUnmount, watch } from 'vue';

const props = defineProps({
    modelValue: {
        type: String,
        default: '',
    },
    placeholder: {
        type: String,
        default: 'Type your message here...',
    },
});

const emit = defineEmits(['update:modelValue']);

const editor = useEditor({
    content: props.modelValue,
    extensions: [
        StarterKit.configure({
            heading: {
                levels: [1, 2, 3],
            },
        }),
    ],
    editorProps: {
        attributes: {
            class: 'prose dark:prose-invert prose-sm sm:prose-base focus:outline-none max-w-none min-h-[150px] px-4 py-3',
        },
    },
    onUpdate: ({ editor }) => {
        emit('update:modelValue', editor.getHTML());
    },
});

watch(() => props.modelValue, (value) => {
    const isSame = editor.value.getHTML() === value;
    if (isSame) return;
    editor.value.commands.setContent(value, false);
});

onBeforeUnmount(() => {
    editor.value.destroy();
});
</script>

<template>
    <div v-if="editor" class="border border-border rounded-xl overflow-hidden bg-background focus-within:ring-2 focus-within:ring-accent/20 focus-within:border-accent transition-all">
        <!-- Toolbar -->
        <div class="flex flex-wrap items-center gap-1 p-2 border-b border-border bg-muted/30">
            <button 
                @click="editor.chain().focus().toggleBold().run()"
                :disabled="!editor.can().chain().focus().toggleBold().run()"
                :class="{ 'bg-accent/20 text-accent': editor.isActive('bold') }"
                class="p-2 rounded-lg hover:bg-muted transition-colors text-muted-foreground"
                type="button"
                title="Bold"
            >
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 4h8a4 4 0 0 1 4 4 4 4 0 0 1-4 4H6z"/><path d="M6 12h9a4 4 0 0 1 4 4 4 4 0 0 1-4 4H6z"/></svg>
            </button>
            <button 
                @click="editor.chain().focus().toggleItalic().run()"
                :disabled="!editor.can().chain().focus().toggleItalic().run()"
                :class="{ 'bg-accent/20 text-accent': editor.isActive('italic') }"
                class="p-2 rounded-lg hover:bg-muted transition-colors text-muted-foreground"
                type="button"
                title="Italic"
            >
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="4" x2="10" y2="4"/><line x1="14" y1="20" x2="5" y2="20"/><line x1="15" y1="4" x2="9" y2="20"/></svg>
            </button>
            <button 
                @click="editor.chain().focus().toggleStrike().run()"
                :disabled="!editor.can().chain().focus().toggleStrike().run()"
                :class="{ 'bg-accent/20 text-accent': editor.isActive('strike') }"
                class="p-2 rounded-lg hover:bg-muted transition-colors text-muted-foreground"
                type="button"
                title="Strikethrough"
            >
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><path d="M16 6C16 6 14.5 4 12 4C9.5 4 8 6 8 6"/><path d="M8 18C8 18 9.5 20 12 20C14.5 20 16 18 16 18"/></svg>
            </button>
            
            <div class="w-px h-6 bg-border mx-1"></div>

            <button 
                @click="editor.chain().focus().toggleBulletList().run()"
                :class="{ 'bg-accent/20 text-accent': editor.isActive('bulletList') }"
                class="p-2 rounded-lg hover:bg-muted transition-colors text-muted-foreground"
                type="button"
                title="Bullet List"
            >
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
            </button>
            <button 
                @click="editor.chain().focus().toggleOrderedList().run()"
                :class="{ 'bg-accent/20 text-accent': editor.isActive('orderedList') }"
                class="p-2 rounded-lg hover:bg-muted transition-colors text-muted-foreground"
                type="button"
                title="Ordered List"
            >
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="10" y1="6" x2="21" y2="6"/><line x1="10" y1="12" x2="21" y2="12"/><line x1="10" y1="18" x2="21" y2="18"/><path d="M4 6h1v4"/><path d="M4 10h2"/><path d="M6 18H4c0-1 2-2 2-3s-1-1.5-2-1"/></svg>
            </button>
            <button 
                @click="editor.chain().focus().toggleBlockquote().run()"
                :class="{ 'bg-accent/20 text-accent': editor.isActive('blockquote') }"
                class="p-2 rounded-lg hover:bg-muted transition-colors text-muted-foreground"
                type="button"
                title="Blockquote"
            >
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            </button>

            <div class="w-px h-6 bg-border mx-1"></div>

            <button 
                @click="editor.chain().focus().undo().run()"
                :disabled="!editor.can().chain().focus().undo().run()"
                class="p-2 rounded-lg hover:bg-muted transition-colors text-muted-foreground disabled:opacity-30"
                type="button"
                title="Undo"
            >
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7v6h6"/><path d="M21 17a9 9 0 0 0-9-9 9 9 0 0 0-6 2.3L3 13"/></svg>
            </button>
            <button 
                @click="editor.chain().focus().redo().run()"
                :disabled="!editor.can().chain().focus().redo().run()"
                class="p-2 rounded-lg hover:bg-muted transition-colors text-muted-foreground disabled:opacity-30"
                type="button"
                title="Redo"
            >
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 7v6h-6"/><path d="M3 17a9 9 0 0 1 9-9 9 9 0 0 1 6 2.3l3 2.7"/></svg>
            </button>
        </div>

        <!-- Content Area -->
        <editor-content :editor="editor" />
    </div>
</template>

<style>
.ProseMirror p.is-editor-empty:first-child::before {
  content: attr(data-placeholder);
  float: left;
  color: #adb5bd;
  pointer-events: none;
  height: 0;
}

.ProseMirror {
    outline: none !important;
}
</style>