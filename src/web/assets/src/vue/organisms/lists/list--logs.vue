<script setup lang="ts">
    import { getPayRunLogs } from '~/js/composables/useAxiosClient'
    import { ref, onMounted, onUnmounted } from 'vue'
    import LogListItem from '~/vue/molecules/listitems/listitem--log.vue'

    const props = defineProps({
        payrun: Number,
    })

    getPayRunLogs(props.payrun)

</script>

<template>
    <div class="mt-8 flex flex-col w-full">
        <div class="-my-2 overflow-x-auto w-full">
            <div class="inline-block min-w-full py-2 align-middle">
                <div class="overflow-hidden border border-solid border-gray-300 md:rounded-lg">

                    <!-- HEADINGS -->
                    <div class="grid grid-cols-7 border-b border-solid border-gray-300">
                        <div class="col-span-2 py-3 px-3 text-left text-sm font-semibold text-gray-900">Filename</div>
                        <div class="py-3 px-3 text-left text-sm font-semibold text-gray-900">Row count</div>
                        <div class="col-span-2 px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Uploaded By</div>
                        <div class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Uploaded</div>
                        <div class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status</div>
                    </div>

                    <!-- CONTENT -->
                    <LogListItem />
                </div>
            </div>
        </div>
    </div>
</template>