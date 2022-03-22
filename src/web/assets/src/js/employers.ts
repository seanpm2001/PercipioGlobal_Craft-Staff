import Employers from '~/vue/payrun/Employers.vue'
import { createApp, h, provide } from 'vue'
import { DefaultApolloClient } from '@vue/apollo-composable'
import { defaultClient } from '~/js/composables/useApolloClient'

const employers = async () => {
    const employers = createApp({
        setup () {
            provide(DefaultApolloClient, defaultClient)
        },
        render: () => h(Employers)
    })
    const root = employers.mount('#employer-container')

    return root
}

employers().then( () => {
    console.log()
})