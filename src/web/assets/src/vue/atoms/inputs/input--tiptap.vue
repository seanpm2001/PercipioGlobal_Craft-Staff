<script setup lang="ts">
    import { useEditor, EditorContent } from '@tiptap/vue-3'
    import CharacterCount from '@tiptap/extension-character-count'
    import StarterKit from '@tiptap/starter-kit'
    import { ref, watch } from 'vue'

    const props = defineProps({
        content: {
            type: String,
            default: '<p>The benefit provider description field.</p>'
        }
    })

    const emit = defineEmits(['update:content'])
    const classes = {
        border: 'border border-gray-500/25 border-solid',
        colors: 'bg-gray-500/15',
        cursor: 'cursor-pointer',
        display: 'flex items-center justify-center',
        focus: '',
        hover: 'hover:bg-gray-500/30 hover:text-white',
        spacing: 'px-4 py-2',
        transition: 'transition-colors ease-in-out duration-200',
        visual: 'rounded-[3px] shadow-sm' 
    }
    const buttonStyle = [
        classes.border,
        classes.colors,
        classes.cursor,
        classes.display,
        classes.focus,
        classes.hover,
        classes.spacing,
        classes.transition,
        classes.visual
    ].join(' ')
    const focus = ref(false)

    const editor = useEditor({
        content: props.content,
        editorProps: {
            attributes: {
                class: 'prose prose-sm sm:prose mx-auto focus:outline-none max-w-full p-4 focus:outline-none focus:shadow-none'
            }
        },
        extensions: [
            StarterKit.configure({
                blockquote: false,
                bulletList: false,
                code: false,
                codeBlock: false,
                dropcursor: false,
                gapcursor: false,
                heading: false,
                strike: false,
            }),
            CharacterCount,
        ],
        onUpdate: ({ editor }) => {
            let content = editor.getHTML()
            emit('update:content', content)
        }
    })

    watch(() => props.content, (newValue, oldValue) => {
        const isSame = newValue === oldValue
        if (isSame) return
        editor.value?.commands.setContent(newValue, false)
    })

    const setFocus = () => {
        focus.value = !focus.value
        console.warn(focus)
    }
</script>

<template>
    <div 
        v-if="editor"
        :class="
            [
                    'border border-solid border-gray-500/25 rounded-[3px] my-4 overflow-hidden bg-gray-50 bg-opacity',
                    focus ? 'focus-visible' : ''       
            ]"
        @blur="setFocus()"
    >
        <div class="flex flex-row flex-nowrap p-4 pb-0 space-x-2">
            <button 
                :class="
                    [ 
                        buttonStyle,
                        editor.isActive('bold') ? 'bg-gray-500/40' : ''
                    ]
                " 
                @click.prevent="editor.chain().focus().toggleBold().run()"
            >
                <span class="w-4 h-4 inline-flex fill-current">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M6.75 4.75H12.5C14.5711 4.75 16.25 6.42893 16.25 8.5C16.25 10.5711 14.5711 12.25 12.5 12.25H6.75V4.75Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M6.75 12.25H13.75C15.683 12.25 17.25 13.817 17.25 15.75C17.25 17.683 15.683 19.25 13.75 19.25H6.75V12.25Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                </span>
            </button>
            <button 
                :class="
                    [ 
                        buttonStyle,
                        editor.isActive('italic') ? 'bg-gray-500/40' : ''
                    ]
                " 
                @click.prevent="editor.chain().focus().toggleItalic().run()"
            >
                <span class="w-4 h-4 inline-flex fill-current">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M14 4.75H11.75M14 4.75H16.25M14 4.75L10 19.25M10 19.25H7.75M10 19.25H12.25" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                </span>
            </button>
        </div>

        <editor-content :editor="editor"  />

        <div class="border-t border-solid border-gray-200 flex flex-row flex-nowrap space-x-2 px-4 py-2 rounded-[3px]">
            <span class="font-mono text-sm">
                words: {{ editor.storage.characterCount.words() }}
            </span>
            <span class="font-mono text-sm">
                chars: {{ editor.storage.characterCount.characters() }}
            </span>
        </div>
    </div>
</template>