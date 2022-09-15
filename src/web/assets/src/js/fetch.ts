import Fetch from '~/vue/fetch/Fetch.vue'
import { createApp, h, provide } from 'vue'
import { createPinia } from 'pinia'
import { DefaultApolloClient } from '@vue/apollo-composable'
import { defaultClient } from '~/js/composables/useApolloClient'


const fetch = async () => {
    const fetch = createApp({
        setup() {
            provide(DefaultApolloClient, defaultClient)
        },
        render: () => h(Fetch)
    })
    fetch.use(createPinia())
    const root = fetch.mount('#fetch-container')

    return root
}

fetch().then( () => {
    console.log()
})