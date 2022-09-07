import BenefitVariant from '~/vue/benefits/DeleteVariants.vue'
import {createApp, h, provide} from 'vue'

const benefitVariants = async () => {
    const benefitVariants = createApp({
        setup() {},
        render: () => h(BenefitVariant)
    })
    const root = benefitVariants.mount('#benefit-variants-container')

    return root
}

benefitVariants().then(() => {
    console.log()
})

// Accept HMR as per: https://vitejs.dev/guide/api-hmr.html
if (import.meta.hot) {
    import.meta.hot.accept(() => {
        console.log('HMR active')
    });
}