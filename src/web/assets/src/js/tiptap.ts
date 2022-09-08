import TipTap from '~/vue/Tiptap.vue'
import { createApp } from 'vue'

const tiptapField = async () => {

    const tiptapFields = document.querySelectorAll('[id$=_tiptap]')
    const tiptapFieldsToMount = new Object()

    tiptapFields.forEach( (tiptapField) => {
    
        const field = tiptapField.id.replace('-', '')

        tiptapFieldsToMount[field] = {
            'id': '#' + tiptapField.id,
            'tiptap': createApp({ ...TipTap })
        }

    })

    const tiptap = Object.entries(tiptapFieldsToMount).map(entry => {
        const field = entry[1]
        return field.tiptap.mount(field.id)
    })
    
    return tiptap
}

tiptapField().then( () => {
    console.log()
})