import PayRun from '~/vue/payrun/PayRun.vue'
import { createApp } from 'vue'
import { createPinia } from 'pinia'

const payrun = async () => {
    const payrun = createApp(PayRun)
    payrun.use(createPinia())
    const root = payrun.mount('#payrun-container')

    return root
}

payrun()