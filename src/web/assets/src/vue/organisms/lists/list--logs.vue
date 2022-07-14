<script setup lang="ts">
    import { getPayRunLogs } from '~/js/composables/useAxiosClient'
    import { ref, onMounted, onUnmounted, watchEffect } from 'vue'
    import { usePayRunStore } from '~/stores/payrun'

    import LogListItem from '~/vue/molecules/listitems/listitem--log.vue'
    import LoadingList from '~/vue/molecules/listitems/listitem--loading.vue'

    const store = usePayRunStore()

    const props = defineProps({
        payrun: String,
    })

    watchEffect(() => {
        if(props.payrun) {
            getPayRunLogs(props.payrun)
        }
    })

</script>

<template>
  <div class="mt-8 flex flex-col w-full">
    <div class="-my-2 overflow-x-auto w-full">
      <div class="inline-block min-w-full py-2 align-middle">
        <div class="overflow-hidden border border-solid border-gray-300 md:rounded-lg">
          <!-- HEADINGS -->
          <div class="grid grid-cols-7 border-b border-solid border-gray-300">
            <div class="col-span-2 py-3 px-3 text-left text-sm font-semibold text-gray-900">
              Filename
            </div>
            <div class="py-3 px-3 text-left text-sm font-semibold text-gray-900">
              Row count
            </div>
            <div class="col-span-2 px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
              Uploaded By
            </div>
            <div class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
              Uploaded
            </div>
            <div class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
              Status
            </div>
          </div>

          <!-- CONTENT -->
          <LoadingList v-if="!payrun" />

          <div
            v-if="payrun && store.logs.length === 0"
            class="grid grid-cols-7 border-b border-solid border-gray-200 py-4 px-3 text-center"
          >
            <div class="col-span-7">
              There are currently no logs for this pay run
            </div>
          </div>

          <LogListItem
            v-for="log in store.logs"
            :key="log.id"
            :log="log"
          />
        </div>
      </div>
    </div>
  </div>
</template>