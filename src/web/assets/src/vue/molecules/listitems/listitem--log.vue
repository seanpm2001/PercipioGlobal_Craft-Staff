<script setup lang="ts">
    import { usePayRunStore } from '~/js/stores/payrun'

    const store = usePayRunStore()

    const user = (log) => {

        if(log?.firstName || log?.lastName){
            return `${log.firstName} ${log.lastName}`
        }

        return log.username
    }

    const style = (log) => {

        const styles = {
            'Succeeded': 'bg-emerald-400 text-emerald-900',
            'Failed': 'bg-red-400 text-red-900',
            'default': 'bg-orange-400 text-orange-900'
        }

        return styles[log.status] ?? styles['default']
    }
    
</script>

<template>
    <div 
        v-for="log in store.logs" 
        :key="log.id"
        class="grid grid-cols-7 border-b border-solid border-gray-200"
    >
        <div class="col-span-2 whitespace-nowrap px-3 py-4 flex text-sm text-gray-500">{{ log.filename }}</div>
        <div class="whitespace-nowrap px-3 py-4 flex text-sm text-gray-500">{{ log.rowCount }}</div>
        <div class="col-span-2 whitespace-nowrap px-3 py-4 flex text-sm text-gray-500">{{ user(log) }}</div>
        <div class="whitespace-nowrap px-3 py-4 flex text-sm text-gray-500">{{ log.dateCreated }}</div>
        <div class="items-center px-3 py-2 flex">
            <div :class="[
                'whitespace-nowrap rounded-full text-xs inline-block px-3 py-1 mb-0 font-bold',
                style(log)
            ]">{{ log.status ? log.status : 'Unknown' }}</div>
        </div>
    </div>
</template>