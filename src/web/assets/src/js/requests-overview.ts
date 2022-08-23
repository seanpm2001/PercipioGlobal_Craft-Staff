import Requests from '~/vue/requests/Requests.vue'
import { createApp, h, provide } from 'vue'
import { createPinia } from 'pinia'
import { DefaultApolloClient } from '@vue/apollo-composable'
import { defaultClient } from '~/js/composables/useApolloClient'

const requestsOverview = async () => {
    const request = createApp({
        setup() {
            provide(DefaultApolloClient, defaultClient)
        },
        render: () => h(Requests)
    })
    request.use(createPinia())
    const root = request.mount('#request-container')

    return root
}

requestsOverview().then( () => {
    console.log()
})