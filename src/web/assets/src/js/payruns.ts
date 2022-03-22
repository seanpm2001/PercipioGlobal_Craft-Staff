import PayRuns from '~/vue/payrun/PayRunList.vue'
import { createApp, h, provide } from 'vue'
import { createPinia } from 'pinia'
import { DefaultApolloClient } from '@vue/apollo-composable'
import { defaultClient } from '~/js/composables/useApolloClient'


const payruns = async () => {
    const payruns = createApp({
        setup() {
            provide(DefaultApolloClient, defaultClient)
        },
        render: () => h(PayRuns)
    })
    payruns.use(createPinia())
    const root = payruns.mount('#payruns-container')

    return root
}

payruns().then( () => {
    console.log()
})