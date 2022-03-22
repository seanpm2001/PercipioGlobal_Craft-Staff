import axios from 'axios'
import { usePayRunStore } from '~/stores/payrun'

const ENDPOINT = window.api.cpUrl ?? 'https://localhost:8003'


export const fetchPayRuns = (employerId: string) => {

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

export const fetchPayRun = (payRunId: string) => {

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
    .then((response) => {
        store.queue = response?.data?.total ? response.data.total : 0
    })

}

export const getPayRunLogs = (payRunId: string) => {

    const store = usePayRunStore()

    axios({
        method: 'get',
        url: `${ENDPOINT}/staff-management/pay-runs/get-logs/${payRunId}`,
    })
        .then((response) => {
            store.logs = response?.data?.logs ? response.data.logs : []
            // store.loadingFetched = false
        })

}

export const getToken = async (): Promise<string|null> => {

    const token = {
        value: null,
    }

    await axios({
        method: 'get',
        url: `${ENDPOINT}/staff-management/settings/get-gql-token`,
    })
    .then( (response) => {
        token.value = response?.data?.token ? response.data.token : null
    })

    return token.value || null

}