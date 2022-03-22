import Details from '~/vue/payrun/PayRunDetail.vue'
import { createApp } from 'vue'
import { createPinia } from 'pinia'

const payRunDetails = async () => {
    const payRunDetails = createApp(Details)
    payRunDetails.use(createPinia())
    const root = payRunDetails.mount('#detail-container')

    return root
}

payRunDetails().then( () => {
    console.log()
})
