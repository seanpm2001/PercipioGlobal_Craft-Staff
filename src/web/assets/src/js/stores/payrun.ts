import { defineStore } from 'pinia'

export const usePayRunStore = defineStore('payrun', {
    id: 'payrun',

    state: () => ({
        employeeCount: 115,
        queue: 0,
        loadingFetched: false,
        token: null,
        logs: []
    }),
})