import BenefitPolicy from '~/vue/benefits/Policy.vue'
import {createApp, h, provide} from 'vue'

const benefitPolicy = async () => {
    const benefitPolicy = createApp({
        render: () => h(BenefitPolicy)
    })
    const root = benefitPolicy.mount('#benefit-policy-container')

    return root
}

benefitPolicy().then(() => {
    console.log()
})

// Accept HMR as per: https://vitejs.dev/guide/api-hmr.html
if (import.meta.hot) {
    import.meta.hot.accept(() => {
        console.log('HMR active')
    });
}