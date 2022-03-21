import PayRun from '~/vue/payrun/PayRunDetail.vue'
import { createApp } from 'vue'
import { createPinia } from 'pinia'

const payRunDetails = async () => {
    const payRunDetails = createApp(PayRun)
    payRunDetails.use(createPinia())
    const root = payRunDetails.mount('#payrun-container')

    return root
}

payRunDetails().then( () => {
    console.log()
})
