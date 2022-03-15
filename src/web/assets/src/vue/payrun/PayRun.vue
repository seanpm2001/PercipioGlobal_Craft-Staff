<script setup lang="ts">
    import { defaultClient } from '~/js/composables/useApolloClient'
    import { DefaultApolloClient } from '@vue/apollo-composable'
    import { provide } from 'vue'
    import { usePayRunStore } from '~/js/stores/payrun'
    import EmployerListItem from '~/vue/molecules/listitems/listitem--employer.vue'
    //import inputSearch from '~/vue/atoms/inputs/input--search.vue'

    const employers = [
        { name: 'Acme Limited (Demo)', logoUrl: 'https://prodstaffologystorage.blob.core.windows.net/images/generic-logo.png', crn: '123456', employeeCount: '5', currentPayRun: 'Year2021/12', synced: '15/04/2022 - 18:30' },
        // More people...
    ]

    provide(DefaultApolloClient, defaultClient)

    const store = usePayRunStore()

</script>

<template>

    <div class="sm:flex">
        <div class="sm:flex-auto">
            <h1 class="text-xl font-semibold text-gray-900">Employers</h1>
            <p class="mt-2 text-sm text-gray-700">A list of all the employers on The Harding Hub.</p>
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

                    <!-- CONTENT -->
                    <div class="grid grid-cols-6 border-b border-solid border-gray-200" v-for="employer in employers">
                        <a href="#" class="col-span-2 whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-indigo-800 sm:pl-6 flex">
                            <div class="object-cover object-center w-6 h-6 rounded-full overflow-hidden">
                                <img :src="employer.logoUrl" class="w-full h-full" />
                            </div>
                            <span>{{ employer.name }}</span>
                        </a>
                        <div class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ employer.crn }}</div>
                        <div class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ employer.employeeCount }}</div>
                        <div class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ employer.currentPayRun }}</div>
                        <div class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ employer.synced }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>