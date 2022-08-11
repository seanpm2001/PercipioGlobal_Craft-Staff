<script setup lang="ts">
import { ref, watch } from 'vue'
import { useQuery } from '@vue/apollo-composable'
import { REQUESTS } from '~/graphql/requests.ts'
import LoadingList from '~/vue/molecules/listitems/listitem--loading.vue'
import RequestList from '~/vue/organisms/lists/list--requests.vue'

const pagination = ref({
    currentPage: 0,
    hitsPerPage: 30,
    total: 0
})
const { result, loading, fetchMore, onResult } = useQuery(REQUESTS, {
    limit: pagination.value.hitsPerPage,
    offset: pagination.value.currentPage * pagination.value.hitsPerPage
})

onResult(queryResult => {
    pagination.value.total = queryResult.data.RequestCount
})

const onLoadMore = () => {
    pagination.value.currentPage += 1

    fetchMore({
        variables: {
            limit: pagination.value.hitsPerPage,
            offset: pagination.value.currentPage * pagination.value.hitsPerPage
        },
        updateQuery: (previousResult, { fetchMoreResult }) => {
            // No new feed posts
            if (!fetchMoreResult) return previousResult

            // Concat previous feed with new feed posts
            return {
                ...previousResult,
                Requests: [
                    ...previousResult.Requests,
                    ...fetchMoreResult.Requests
                ]
            }
        },
    })
}
</script>

<template>

    <div class="sm:flex">
        <div class="sm:flex-auto">
            <h1 class="text-xl font-semibold text-gray-900">Requests</h1>
            <p class="mt-2 text-sm text-gray-700">Select a request to handle the employees request.</p>
        </div>
        <!--div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
            <button type="button" class="inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto">Add user</button>
        </div-->
    </div>
    <div class="mt-8 flex flex-col w-full">
        <div class="-my-2 overflow-x-auto w-full">
            <div class="inline-block min-w-full py-2 align-middle">
                <div class="overflow-hidden shadow border border-solid border-gray-300 md:rounded-lg">

                    <!-- HEADINGS -->
                    <div class="grid grid-cols-11 border-b border-solid border-gray-300">
                        <div class="col-span-2 py-3.5 pl-4 pr-3 sm:pl-6 text-left text-sm font-semibold text-gray-900">Employee</div>
                        <div class="col-span-2 px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Company</div>
                        <div class="col-span-2 px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Request Type</div>
                        <div class="col-span-2 px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Requested Date</div>
                        <div class="col-span-2 px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Administered By</div>
                        <div class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status</div>
                    </div>

                    <LoadingList v-if="loading" />
                    <RequestList v-if="result" :requests="result.Requests" />
                </div>
            </div>
        </div>
        <button
            v-if="result?.Requests?.length !== pagination.total"
            @click="onLoadMore"
            class="cursor-pointer mt-6 inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 disabled:bg-indigo-400 disabled:cursor-not-allowed px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto"
        >
            <span>Load more ({{ pagination.total - result?.Requests?.length }})</span>
            <svg v-if="loadig" class="animate-spin ml-1 h-3 w-3 text-white mb-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
        </button>
    </div>

</template>