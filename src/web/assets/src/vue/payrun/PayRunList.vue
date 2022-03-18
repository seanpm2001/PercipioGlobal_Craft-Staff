<script setup lang="ts">
    import { defaultClient } from '~/js/composables/useApolloClient'
    import { DefaultApolloClient } from '@vue/apollo-composable'
    import { provide } from 'vue'
    import { usePayRunStore } from '~/js/stores/payrun'
    import PayRunList from '~/vue/molecules/listitems/listitem--payrun.vue'

    const payruns = [
        { id: 12605, employer: 'Acme Limited (Demo)', employerId: 12600, period: 12, taxYear: 'Year2021', startDate: '2022-02-01 ', endDate: '2022-02-28', employeeCount: 4, state: 'Open', paymentDate: '2022-03-25', dateUpdated: '2022-03-16 17:16:49', totalCost:1234},
        { id: 12605, employer: 'Acme Limited (Demo)', employerId: 12600, period: 11, taxYear: 'Year2021', startDate: '2022-02-01 ', endDate: '2022-02-28', employeeCount: 4, state: 'Closed', paymentDate: '2022-03-25', dateUpdated: '2022-03-16 17:16:49', totalCost:1234 },
        // More people...
    ]

    provide(DefaultApolloClient, defaultClient)

    const store = usePayRunStore()

</script>

<template>

    <div class="md:flex items-start">
        <div class="flex-grow pr-4" style="margin-bottom:0">
            <div class="flex items-center">
                <a href="/admin/staff-management/pay-runs" :title="`Back to ${payruns[0]?.employer}`" class="inline-flex items-center px-2.5 py-1.5 rounded-full text-sm text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" style="margin-bottom:0">&larr;</a>
                <h1 class="ml-1 text-xl font-semibold text-gray-900">{{payruns[0]?.employer}}</h1>
            </div>
            <p class="mt-2 text-sm text-gray-700">Click on a pay run to upload the CSV with the pay run entries. If the pay run is not provided in the list bellow, use the "Fetch&nbsp;Pay&nbsp;Runs" button on your right to fetch.</p>
        </div>
        <div class="mt-4 md:mt-0 flex" style="margin-bottom:0">
            <span class="mt-4 md:mt-0 text-xs inline-flex mr-2 flex-grow" style="margin-bottom:0">Last Synced: 03/03/2022 10:09</span>
            <button type="button" class="inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto" style="margin-bottom:0">Fetch Pay Runs</button>
        </div>
    </div>
    <div class="mt-8 flex flex-col w-full">
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
                    <PayRunList :payrun-data="payruns" />
                </div>
            </div>
        </div>
    </div>
</template>