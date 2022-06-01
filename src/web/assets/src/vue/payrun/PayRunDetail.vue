<script setup lang="ts">
    import { useQuery } from '@vue/apollo-composable'
    import { reactive, ref } from 'vue'

    import { fetchPayRun } from '~/js/composables/useAxiosClient'
    import { usePayRunStore } from '~/stores/payrun'
    import { PAYRUN } from '~/graphql/payrun.ts'

    import PayRunStats from '~/vue/molecules/stats/stats--payrun.vue'
    import LoadingStats from '~/vue/molecules/stats/stats--loading.vue'
    import BannerError from '~/vue/molecules/banners/banner--error.vue'
    import StatusSynced from '~/vue/molecules/status/status--synced.vue'
    import FormImport from '~/vue/organisms/forms/form--import.vue'
    import ListLogs from '~/vue/organisms/lists/list--logs.vue'

    const payRunId = window.location.href.split('/').pop()
    const taxYear = reactive({
        current: window.location.href.split('/').at(-2)
    })

    const { result, loading } = useQuery(
        PAYRUN, 
        { id: payRunId },
        { pollInterval: 5000 }
    )
    const store = usePayRunStore()
    const error = ref(window?.validation?.error)

    const downloadTemplate = (id) => {
        const url = `/admin/staff-management/pay-runs/download-template/${id}`
        const popout = window.open(url)
    }

    const handleClose = () => {
        error.value = ''
    }  

</script>

<template>

    <div class="md:flex items-start">
        <div class="flex-grow pr-4" style="margin-bottom:0">
            <div class="flex items-center">
                <a :href="`/admin/staff-management/pay-runs/${result?.payrun?.employerId}/${taxYear.current}`" title="Go back to overview" class="inline-flex no-underline items-center px-2.5 py-1.5 rounded-full text-sm text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" style="margin-bottom:0">&larr;</a>
                <h1 class="ml-2 text-xl font-semibold text-gray-900">{{ result?.payrun?.taxYear }} / {{ result?.payrun?.period }}</h1>
            </div>
            <p class="mt-2 text-sm text-gray-700">Download the current pay run CSV using the Download button below. When uploading, ensure table headings and the file format (CSV) remain unchanged.</p>
        </div>
        <div class="mt-4 md:mt-0 flex" style="margin-bottom:0">
            <StatusSynced :date="result?.payrun?.dateSynced" />
            <button 
                @click="fetchPayRun(result?.payrun?.id, taxYear.current)"
                :disabled="store.loadingFetched" 
                class="cursor-pointer inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 disabled:bg-indigo-400 disabled:cursor-not-allowed px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto" 
                style="margin-bottom:0"
            >
                <span>Refresh Pay Run</span>
                <svg v-if="store.loadingFetched" class="animate-spin ml-1 h-3 w-3 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" style="margin-bottom:0">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </button>
        </div>
    </div>

    <div class="mt-8">
        <LoadingStats v-if="!result?.payrun"/>
        <PayRunStats :payrun="result?.payrun" v-if="result?.payrun" />
    </div>

    <div class="mt-8 flex">

        <BannerError v-if="error" :error="error" @close="handleClose" />

        <div class="sm:flex-auto" style="margin-bottom:0">
            <h2>Uploaded Pay Run Entries</h2>
            <span class="mt-4 md:mt-0 text-xs inline-flex mr-2 flex-grow" style="margin-bottom:0">
                Last Synced: {{ result?.payrun?.dateSynced }}
            </span>
        </div>
        <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none space-x-2" style="margin-bottom:0" v-if="result?.payrun">
            <button @click="downloadTemplate(result?.payrun?.id)" class="cursor-pointer inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Download Latest Pay Run Template</button>
            <FormImport :payrun="result?.payrun" />            
        </div>

        <ListLogs :payrun="result?.payrun?.id" />
    </div> 

</template>