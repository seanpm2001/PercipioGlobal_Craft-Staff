import PayRun from '~/vue/payrun/PayRunEmployers.vue'
import { createApp } from 'vue'
import { createPinia } from 'pinia'

const payRunEmployers = async () => {
    const payRunEmployers = createApp(PayRun)
    payRunEmployers.use(createPinia())
    const root = payRunEmployers.mount('#payrun-container')

    return root
}

// payrun()
payRunEmployers();
