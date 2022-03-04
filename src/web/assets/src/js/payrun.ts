import PayRun from '~/vue/payrun/PayRun.vue'
import { createApp } from 'vue'

const payrun = async () => {
    const app = createApp(PayRun)
    const root = app.mount('#payrun-container')

    return root
}

payrun()