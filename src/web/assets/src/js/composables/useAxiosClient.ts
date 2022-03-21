import axios from 'axios'
import { usePayRunStore } from '~/js/stores/payrun'

const ENDPOINT = window.api.cpUrl ?? 'https://localhost:8003'


export const fetchPayRuns = (employerId: String) => {

    const store = usePayRunStore()
    store.loadingFetched = true

    axios({
        method: 'get',
        url: `${ENDPOINT}/staff-management/pay-runs/fetch-pay-runs/${employerId}`,
    })
    .then(() => {
        store.loadingFetched = false
    })

}

export const fetchPayRun = (payRunId: String) => {

    const store = usePayRunStore()
    store.loadingFetched = true

    axios({
        method: 'get',
        url: `${ENDPOINT}/staff-management/pay-runs/fetch-pay-run/${payRunId}`,
    })
    .then(() => {
        store.loadingFetched = false
    })

}

export const getQueue = () => {

    const store = usePayRunStore()

    axios({
        method: 'get',
        url: `${ENDPOINT}/staff-management/pay-runs/queue`,
    })
    .then(function (response) {
        store.queue = response?.data?.total ? response.data.total : 0
    })

}