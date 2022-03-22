<script setup lang="ts">
    import { useQuery } from '@vue/apollo-composable'
    import { EMPLOYERS } from '~/graphql/employers.ts'
    //import EmployerListItem from '~/vue/molecules/listitems/listitem--employer.vue'
    import EmployerList from '~/vue/organisms/lists/list--employers.vue'
    //import inputSearch from '~/vue/atoms/inputs/input--search.vue'

    const { result, loading } = useQuery(EMPLOYERS)
</script>

<template>

    <div class="sm:flex">
        <div class="sm:flex-auto">
            <h1 class="text-xl font-semibold text-gray-900">Employers</h1>
            <p class="mt-2 text-sm text-gray-700">Click on a company from whom you want to upload pay run entries from.</p>
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
                    <div class="grid grid-cols-6 border-b border-solid border-gray-300">
                        <div class="col-span-2 py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Company</div>
                        <div class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">CRN</div>
                        <div class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Employee count</div>
                        <div class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Current Pay Run</div>
                        <div class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Last synced</div>
                    </div>

                    <div v-if="loading " class="flex items-center justify-center p-4 border-b border-solid border-gray-200">
                        <svg class="animate-spin mr-3 h-5 w-5 text-indigo-900" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="text-base">
                            Loading data ...
                        </span>
                    </div>

                    <EmployerList v-if="result" :employers="result.employers" />
                </div>
            </div>
        </div>
    </div>

</template>