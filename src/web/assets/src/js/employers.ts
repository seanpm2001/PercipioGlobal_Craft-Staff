import PayRun from '~/vue/payrun/PayRunEmployers.vue'
import { createApp } from 'vue'
import { createPinia } from 'pinia'

const employers = async () => {
    const employers = createApp(PayRun)
    employers.use(createPinia())
    const root = employers.mount('#employer-container')

    return root
}

employers().then( () => {
    console.log()
})