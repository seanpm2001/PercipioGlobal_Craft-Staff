import { defineStore } from 'pinia'

export const useFetchStore = defineStore('fetch', {
    state: () => ({
        fetchingEmployers: false,
        fetchingEmployees: false,
        fetchingPayRun: false,
        fetchingPayRunEntries: false,
    })
})