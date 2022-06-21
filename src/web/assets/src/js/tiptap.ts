import TipTap from '~/vue/organisms/fields/field--tiptap.vue'
import { createApp, h } from 'vue'

const tiptap = async () => {
    const tiptap = createApp({
        render: () => h(TipTap)
    })
    
    return tiptap.mount('#tiptap-container')
}

tiptap().then(() => {
    console.log()
})