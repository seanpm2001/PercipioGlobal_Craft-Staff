<script setup lang="ts">
    import { fetchDataEmployers, fetchDataEmployees, fetchDataPayRuns, fetchDataPayRunEntries } from '~/js/composables/useAxiosClient'
    import { useFetchStore } from '~/stores/fetch'

    const store = useFetchStore()
</script>

<template>
    <article class="w-full flex border-b border-gray-200 border-solid py-4">
        <div class="flex-grow mb-0">
            <h3 class="mb-0">Fetch all the employers from Staffology</h3>
            <p class="mt-1">This will fetch all the employers registered on Staffology for this environment.</p>
        </div>
        <button
            :disabled="store.fetchingEmployers"
            @click="fetchDataEmployers"
            class="mb-0 cursor-pointer inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 disabled:bg-indigo-400 disabled:cursor-not-allowed px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto"
        >
            <span>Fetch Employers</span>
            <svg
                v-if="store.fetchingEmployers"
                class="animate-spin ml-1 h-3 w-3 text-white"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                style="margin-bottom:0"
            >
                <circle
                    class="opacity-25"
                    cx="12"
                    cy="12"
                    r="10"
                    stroke="currentColor"
                    stroke-width="4"
                />
                <path
                    class="opacity-75"
                    fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                />
            </svg>
        </button>
    </article>
    <article class="w-full flex border-b border-gray-200 border-solid py-4">
        <div class="flex-grow mb-0">
            <h3 class="mb-0">Fetch all the employees from Staffology</h3>
            <p class="mt-1">This will grab all the employers on our system and fetch their employees on Staffology for this environment.</p>
        </div>
        <button
            :disabled="store.fetchingEmployees"
            @click="fetchDataEmployees"
            class="mb-0 cursor-pointer inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 disabled:bg-indigo-400 disabled:cursor-not-allowed px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto"
        >
            <span>Fetch Employees</span>
            <svg
                v-if="store.fetchingEmployees"
                class="animate-spin ml-1 h-3 w-3 text-white"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                style="margin-bottom:0"
            >
                <circle
                    class="opacity-25"
                    cx="12"
                    cy="12"
                    r="10"
                    stroke="currentColor"
                    stroke-width="4"
                />
                <path
                    class="opacity-75"
                    fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                />
            </svg>
        </button>
    </article>
    <article class="w-full flex border-b border-gray-200 border-solid py-4">
        <div class="flex-grow mb-0">
            <h3 class="mb-0">Fetch all the pay runs from Staffology</h3>
            <p class="mt-1">This will grab all the employers on our system and fetch the pay runs for the current tax year on Staffology for this environment.</p>
        </div>
        <button
            :disabled="store.fetchingPayRun"
            @click="fetchDataPayRuns"
            class="mb-0 cursor-pointer inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 disabled:bg-indigo-400 disabled:cursor-not-allowed px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto"
        >
            <span>Fetch Pay Runs</span>
            <svg
                v-if="store.fetchingPayRun"
                class="animate-spin ml-1 h-3 w-3 text-white"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                style="margin-bottom:0"
            >
                <circle
                    class="opacity-25"
                    cx="12"
                    cy="12"
                    r="10"
                    stroke="currentColor"
                    stroke-width="4"
                />
                <path
                    class="opacity-75"
                    fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                />
            </svg>
        </button>
    </article>
    <article class="w-full flex py-4">
        <div class="flex-grow mb-0">
            <h3 class="mb-0">Fetch all the pay run entries from Staffology</h3>
            <p class="mt-1">This will grab all the pay runs on our system and fetch the pay run entries on Staffology for this environment.</p>
        </div>
        <button
            :disabled="store.fetchingPayRunEntries"
            @click="fetchDataPayRunEntries"
            class="mb-0 cursor-pointer inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 disabled:bg-indigo-400 disabled:cursor-not-allowed px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto"
        >
            <span>Fetch Pay Run Entries</span>
            <svg
                v-if="store.fetchingPayRunEntries"
                class="animate-spin ml-1 h-3 w-3 text-white"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                style="margin-bottom:0"
            >
                <circle
                    class="opacity-25"
                    cx="12"
                    cy="12"
                    r="10"
                    stroke="currentColor"
                    stroke-width="4"
                />
                <path
                    class="opacity-75"
                    fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                />
            </svg>
        </button>
    </article>
</template>