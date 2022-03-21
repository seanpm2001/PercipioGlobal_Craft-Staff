<script setup lang="ts">
    import { usePayRunStore } from '~/js/stores/payrun'

    const store = usePayRunStore()
    
</script>

<template>
    <div 
        v-for="log in store.logs" 
        :key="log.id"
        class="grid grid-cols-5 border-b border-solid border-gray-200"
    >
        <div class="col-span-2 whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ log.filename }}</div>
        <div class="col-span-2 whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ log.uploadedBy }}</div>
        <div class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ log.dateCreated }}</div>
    </div>
</template>