import PayRun from '~/vue/payrun/PayRunList.vue'
import { createApp } from 'vue'
import { createPinia } from 'pinia'

const payRunList = async () => {
    const payRunList = createApp(PayRun)
    payRunList.use(createPinia())
    const root = payRunList.mount('#payrun-container')

    return root
}

// payrun()
payRunList();
