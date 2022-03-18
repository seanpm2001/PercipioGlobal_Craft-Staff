<script setup lang="ts">
    import { defaultClient } from '~/js/composables/useApolloClient'
    import { DefaultApolloClient } from '@vue/apollo-composable'
    import { provide } from 'vue'
    import { usePayRunStore } from '~/js/stores/payrun'
    import PayRunStats from '~/vue/molecules/stats/stats--payrun.vue'
    import FormImport from '~/vue/organisms/forms/form--import.vue'

    provide(DefaultApolloClient, defaultClient)

    const store = usePayRunStore()

    const payrun = {
        id: 12605, 
        employer: 'Acme Limited (Demo)', 
        employerId: 12600, 
        period: 12, 
        taxYear: 'Year2021', 
        startDate: '2022-02-01 ', 
        endDate: '2022-02-28', 
        employeeCount: 4, 
        state: 'Open', 
        paymentDate: '2022-03-25', 
        dateUpdated: '2022-03-16 17:16:49', 
        totalNet:1234, 
        totalCost:1234, 
        totalTax:124, 
        importApproved:'0'
    }

    const downloadTemplate = () => {
        const url = `/admin/staff-management/pay-runs/download-template/${payrun.id}`
        const popout = window.open(url)
    }

</script>

<template>

    <div class="md:flex items-start">
        <div class="flex-grow pr-4" style="margin-bottom:0">
            <div class="flex items-center">
                <a :href="`/admin/staff-management/pay-runs/${payrun.employerId}`" :title="`Back to ${payrun.employer}`" class="inline-flex items-center px-2.5 py-1.5 rounded-full text-sm text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" style="margin-bottom:0">&larr;</a>
                <h1 class="ml-2 text-xl font-semibold text-gray-900">{{ payrun.taxYear }} / {{ payrun.period }}</h1>
            </div>
            <p class="mt-2 text-sm text-gray-700">A detailed view of the figures on this pay run. You can download the latest figures by clicking "Download&nbsp;Latest&nbsp;Pay&nbsp;Run&nbsp;Entries&nbsp;Template". You can upload the CSV by clicking "Upload&nbsp;CSV&nbsp;To&nbsp;Staffology", make sure you have the same headings in the CSV to upload. The CSV gets directly uploaded to Staffology.</p>
        </div>
        <div class="mt-4 md:mt-0 flex" style="margin-bottom:0">
            <span class="mt-4 md:mt-0 text-xs inline-flex mr-2 flex-grow" style="margin-bottom:0">Last Synced: 03/03/2022 10:09</span>
            <button type="button" class="inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto" style="margin-bottom:0">Fetch Pay Run</button>
        </div>
    </div>

    <div class="mt-8">
        <PayRunStats :payrun="payrun" />
    </div>

    <div class="mt-8 flex">
        <div class="sm:flex-auto" style="margin-bottom:0">
            <h2>Uploaded Pay Run Entries</h2>
            <p class="mt-2 text-sm text-gray-700">Last Synced: 03/03/2022 10:09</p>
        </div>
        <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none space-x-2" style="margin-bottom:0">
            <button @click="downloadTemplate" class="cursor-pointer inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Download Latest Pay Run Entries Template</button>
            <FormImport :payrun="payrun" />            
        </div>
    </div>      
</template>