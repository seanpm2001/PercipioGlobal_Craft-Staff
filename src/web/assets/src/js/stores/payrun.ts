import { defineStore } from 'pinia'

export const usePayRunStore = defineStore('payrun', {
    state: () => ({
        queue: 0,
        fetching: false,
        logs: []
    }),
})