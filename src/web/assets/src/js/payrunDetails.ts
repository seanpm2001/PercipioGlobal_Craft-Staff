import Details from '~/vue/payrun/PayRunDetail.vue'
import { createApp, h, provide } from 'vue'
import { createPinia } from 'pinia'
import { DefaultApolloClient } from '@vue/apollo-composable'
import { defaultClient } from '~/js/composables/useApolloClient'


const payRunDetails = async () => {
    const payRunDetails = createApp({
        setup() {
            provide(DefaultApolloClient, defaultClient)
        },
        render: () => h(Details)
    })
    payRunDetails.use(createPinia())
    const root = payRunDetails.mount('#detail-container')

    return root
}

payRunDetails().then( () => {
    console.log()
})