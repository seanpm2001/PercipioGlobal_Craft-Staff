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

// Accept HMR as per: https://vitejs.dev/guide/api-hmr.html
if (import.meta.hot) {
    import.meta.hot.accept(() => {
        console.log('HMR active')
    });
}