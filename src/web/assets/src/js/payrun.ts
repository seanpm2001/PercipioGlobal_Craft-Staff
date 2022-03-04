import PayRun from '~/vue/payrun/PayRun.vue'
import { createApp } from 'vue'

const payrun = async () => {
    const payrun = createApp(PayRun)
    const root = payrun.mount('#payrun-container')

    return root
}

payrun()