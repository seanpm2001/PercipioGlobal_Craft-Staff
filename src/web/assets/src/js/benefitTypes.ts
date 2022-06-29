import BenefitTypes from '~/vue/benefits/BenefitTypes.vue'
import { createApp } from 'vue'

const benefitTypes = async () => {
    const benefitTypes = createApp(BenefitTypes)
    const root = benefitTypes.mount('#benefit-types-container')

    return root
}

benefitTypes().then(() => {
    console.log()
})

// Accept HMR as per: https://vitejs.dev/guide/api-hmr.html
if (import.meta.hot) {
    import.meta.hot.accept(() => {
        console.log('HMR active')
    });
}