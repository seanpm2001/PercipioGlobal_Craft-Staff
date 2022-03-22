<script setup lang="ts">
    import { usePayRunStore } from '~/js/stores/payrun'

    const store = usePayRunStore()

    const user = (log) => {

        if(log?.firstName || log?.lastName){
            return `${log.firstName} ${log.lastName}`
        }

        return log.username
    }
    
</script>

<template>
    <div 
        v-for="log in store.logs" 
        :key="log.id"
        class="grid grid-cols-6 border-b border-solid border-gray-200"
    >
        <div class="col-span-2 whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ log.filename }}</div>
        <div class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ log.rowCount }}</div>
        <div class="col-span-2 whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ user(log) }}</div>
        <div class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ log.dateCreated }}</div>
    </div>
</template>