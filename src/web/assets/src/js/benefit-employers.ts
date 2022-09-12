import BenefitEmployers from '~/vue/benefits/Employers.vue'
import {createApp, h, provide} from 'vue'
import { DefaultApolloClient } from '@vue/apollo-composable'
import { defaultClient } from '~/js/composables/useApolloClient'

const benefitEmployers = async () => {
    const benefitEmployers = createApp({
        setup() {
            provide(DefaultApolloClient, defaultClient)
        },
        render: () => h(BenefitEmployers)
    })
    const root = benefitEmployers.mount('#benefit-employer-container')

    return root
}

benefitEmployers().then(() => {
    console.log()
})

// Accept HMR as per: https://vitejs.dev/guide/api-hmr.html
if (import.meta.hot) {
    import.meta.hot.accept(() => {
        console.log('HMR active')
    });
}