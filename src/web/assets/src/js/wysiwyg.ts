import Wysiwyg from '~/vue/atoms/inputs/input--wysiwyg.vue'
import { createApp, h } from 'vue'

console.log("wysiwyg")

const wysiwyg = async () => {
    console.log("test")

    const wysiwyg = createApp({
        render: () => h(Wysiwyg)
    })
    
    return wysiwyg.mount('#wysiwyg-container')
}

wysiwyg().then(() => {
    console.log()
})