<script setup lang="ts">
    import { getQueue } from '~/js/composables/useAxiosClient'
    import { ref, onMounted, onUnmounted } from 'vue'
    import { usePayRunStore } from '~/stores/payrun'

    const props = defineProps({
        date: String,
    })

    const store = usePayRunStore()
    const interval = ref(null)

    onMounted(() => {
        interval.value = setInterval(() => {
            getQueue()
        }, 5000)
    }) 

    onUnmounted(() => {
        clearInterval(interval.value)
    })
</script>

<template>
    <span class="mt-4 md:mt-0 text-xs inline-flex mr-2 flex-grow" style="margin-bottom:0">
        Last Synced: 
        <span v-if="store.queue != 0" class="flex items-center pl-1">
            <span style="margin-bottom:0">Queue is running to sync</span>
        </span>
        <span v-else class="pl-1">{{ date }}</span>
    </span>
</template>