<script setup lang="ts">
import { useQuery } from '@vue/apollo-composable'
import { EMPLOYERS } from '~/graphql/employers'
import LoadingList from '~/vue/molecules/listitems/listitem--loading.vue'
import EmployerList from '~/vue/organisms/lists/list--employers.vue'
//import inputSearch from '~/vue/atoms/inputs/input--search.vue'

const { result, loading } = useQuery(EMPLOYERS)
</script>

<template>
    <div class="sm:flex">
        <div class="sm:flex-auto">
            <h1 class="text-xl font-semibold text-gray-900">
                Employers
            </h1>
            <p class="mt-2 text-sm text-gray-700">
                Select a company below to begin bulk pay run management.
            </p>
        </div>
    </div>
    <div class="mt-8 flex flex-col w-full">
        <div class="-my-2 overflow-x-auto w-full">
            <div class="inline-block min-w-full py-2 align-middle">
                <div class="overflow-hidden shadow border border-solid border-gray-300 md:rounded-lg">
                    <!-- HEADINGS -->
                    <div class="grid grid-cols-6 border-b border-solid border-gray-300">
                        <div class="col-span-2 py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">
                            Company
                        </div>
                        <div class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                            CRN
                        </div>
                        <div class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                            Employee count
                        </div>
                        <div class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                            Current Pay Run
                        </div>
                        <div class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                            Last synced
                        </div>
                    </div>

                    <LoadingList v-if="loading" />

                    <EmployerList 
                        v-if="result" 
                        :employers="result.employers" 
                    />
                </div>
            </div>
        </div>
    </div>

</template>