<script setup lang="ts">
  import { useEditor, EditorContent } from '@tiptap/vue-3'
  import StarterKit from '@tiptap/starter-kit'
  import { watch } from 'vue'

  const props = defineProps({
    content: {
      type: String,
      default: '<p>I’m running Tiptap with Vue.js. 🎉</p>'
    }
  })

  const emit = defineEmits(['update:content'])

  const editor = useEditor({
    content: props.content,
    editorProps: {
      attributes: {
        class: 'prose prose-sm sm:prose lg:prose-lg xl:prose-2xl mx-auto focus:outline-none'
      }
    },
    extensions: [
      StarterKit,
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
</script>

<template>
    <div v-if="editor">
      quick test
      <div 
        :class="{ 'bg-blue-100': editor.isActive('bold') }" 
        @click="editor.chain().focus().toggleBold().run()"
      >
        Bold
      </div>
      <editor-content :editor="editor" />
    </div>
</template>