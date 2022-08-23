import RequestsDetail from '~/vue/requests/RequestsDetail.vue'
import { createApp, h, provide } from 'vue'
import { DefaultApolloClient } from '@vue/apollo-composable'
import { defaultClient } from '~/js/composables/useApolloClient'

const requestsDetail = async () => {
    const requestDetail = createApp({
        setup() {
            provide(DefaultApolloClient, defaultClient)
        },
        render: () => h(RequestsDetail)
    })
    const root = requestDetail.mount('#request-container')

    return root
}

requestsDetail().then( () => {
    console.log()
})