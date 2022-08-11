<script setup lang="ts">
import { useQuery } from '@vue/apollo-composable'
import { REQUESTS } from '~/graphql/requests.ts'
import LoadingList from '~/vue/molecules/listitems/listitem--loading.vue'
import RequestList from '~/vue/organisms/lists/list--requests.vue'

const { result, loading } = useQuery(REQUESTS)

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
    </div>

</template>