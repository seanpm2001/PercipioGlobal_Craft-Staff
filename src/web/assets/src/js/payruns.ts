import PayRuns from '~/vue/payrun/PayRunList.vue'
import { createApp } from 'vue'
import { createPinia } from 'pinia'

const payruns = async () => {
    const payruns = createApp(PayRuns)
    payruns.use(createPinia())
    const root = payruns.mount('#payruns-container')

    return root
}

payruns().then( () => {
    console.log()
})