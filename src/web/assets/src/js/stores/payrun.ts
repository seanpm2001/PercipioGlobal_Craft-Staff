import { defineStore } from 'pinia'

export const usePayRunStore = defineStore('payrun', {
    id: 'payrun',

    state: () => ({
        queue: 0,
        fetching: false,
        logs: []
    }),
})