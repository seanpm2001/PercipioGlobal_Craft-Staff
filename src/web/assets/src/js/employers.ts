import Employers from '~/vue/payrun/Employers.vue'
import { createApp, h, provide } from 'vue'
import { createPinia } from 'pinia'
import { DefaultApolloClient } from '@vue/apollo-composable'
import { defaultClient } from '~/js/composables/useApolloClient.ts'

const employers = async () => {
    const employers = createApp({
        setup () {
            provide(DefaultApolloClient, defaultClient)
        },
        render: () => h(Employers)
    })
    employers.use(createPinia())
    const root = employers.mount('#employer-container')

    return root
}

employers().then( () => {
    console.log()
})