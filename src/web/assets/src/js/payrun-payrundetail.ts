import PayRun from '~/vue/payrun/PayRunDetail.vue'
import { createApp } from 'vue'
import { createPinia } from 'pinia'

const payRunDetail = async () => {
    const payRunDetail = createApp(PayRun)
    payRunDetail.use(createPinia())
    const root = payRunDetail.mount('#payrun-container')

    return root
}

// payrun()
payRunDetail();
