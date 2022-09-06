import BenefitEmployees from '~/vue/benefits/Employees.vue'
import {createApp, h, provide} from 'vue'
import { DefaultApolloClient } from '@vue/apollo-composable'
import { defaultClient } from '~/js/composables/useApolloClient'

const benefitEmployees = async () => {
    const benefitEmployees = createApp({
        setup() {
            provide(DefaultApolloClient, defaultClient)
        },
        render: () => h(BenefitEmployees)
    })
    const root = benefitEmployees.mount('#benefit-employees-container')

    return root
}

benefitEmployees().then(() => {
    console.log()
})

// Accept HMR as per: https://vitejs.dev/guide/api-hmr.html
if (import.meta.hot) {
    import.meta.hot.accept(() => {
        console.log('HMR active')
    });
}