<script setup lang="ts">
    import { useQuery } from '@vue/apollo-composable'
    import { provide, ref, watchEffect } from 'vue'
    import { storeToRefs } from 'pinia'

    import { fetchPayRuns } from '~/js/composables/useAxiosClient'
    import { usePayRunStore } from '~/js/stores/payrun'
    import { PAYRUNS } from '~/graphql/payrun.ts'

    import PayRunList from '~/vue/molecules/listitems/listitem--payrun.vue'
    import StatusSynced from '~/vue/molecules/status/status--synced.vue'

    const employerId = window.location.href.split("/").pop()
    const { result, loading } = useQuery(PAYRUNS, {employerId: employerId})
    const store = usePayRunStore()
    const { queue } = storeToRefs(store)


    const getLatestSync = () => {
        console.log(result)
        return "test"
        // const sorted = result.payruns.sort((a, b) => (a.dateUpdated < b.dateUpdated) ? 1 : -1)
        // return sorted[0]?.dateUpdated
    }

</script>

<template>

    <div class="md:flex items-start" v-if="result">
        <div class="flex-grow pr-4" style="margin-bottom:0">
            <div class="flex items-center">
                <a href="/admin/staff-management/pay-runs" :title="`Back to ${result.payruns[0]?.employer}`" class="inline-flex items-center px-2.5 py-1.5 rounded-full text-sm text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" style="margin-bottom:0">&larr;</a>
                <h1 class="ml-1 text-xl font-semibold text-gray-900">{{result.payruns[0]?.employer}}</h1>
            </div>
            <p class="mt-2 text-sm text-gray-700">Click on a pay run to upload the CSV with the pay run entries. If the pay run is not provided in the list bellow, use the "Fetch&nbsp;Pay&nbsp;Runs" button on your right to fetch.</p>
        </div>
        <div class="mt-4 md:mt-0 flex" style="margin-bottom:0">
            <StatusSynced :date="getLatestSync()" />
            <button 
                @click="fetchPayRuns(result.payruns[0]?.employerId)" 
                :disabled="store.loadingFetched" 
                class="cursor-pointer inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 disabled:bg-indigo-400 disabled:cursor-not-allowed px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto" 
                style="margin-bottom:0"
            >
                <span>Fetch Pay Runs</span>
                <svg v-if="store.loadingFetched" class="animate-spin ml-1 h-3 w-3 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" style="margin-bottom:0">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </button>
        </div>
    </div>
    <div class="mt-8 flex flex-col w-full" v-if="result">
        <div class="-my-2 overflow-x-auto w-full">
            <div class="inline-block min-w-full py-2 align-middle">
                <div class="overflow-hidden shadow border border-solid border-gray-300 md:rounded-lg">

                    <!-- HEADINGS -->
                    <div class="grid grid-cols-5 lg:grid-cols-8 border-b border-solid border-gray-300">
                        <div class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Period</div>
                        <div class="hide lg:block px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Entries</div>
                        <div class="hide lg:block col-span-2 px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Dates</div>
                        <div class="hide lg:block px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Cost</div>
                        <div class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Pay Date</div>
                        <div class="col-span-2 px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Last synced</div>
                    </div>

                    <!-- CONTENT -->
                    <PayRunList :payrun-data="result.payruns" />
                </div>
            </div>
        </div>
    </div>
</template>