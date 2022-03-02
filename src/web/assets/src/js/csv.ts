import App from '~/vue/csv/App.vue'
import { createApp } from '../vue'

const csv = async () => {
    const app = createApp(App)
    const root = app.mount('#csv-container')

    return root
}

main().then( () => {} )