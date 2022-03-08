<script setup lang="ts">
  import { defaultClient } from '~/js/composables/useApolloClient'
  import { DefaultApolloClient } from '@vue/apollo-composable'
  import { provide } from 'vue'
  import { usePayRunStore } from '~/js/stores/payrun'
  import EmployerListItem from '~/vue/molecules/listitems/listitem--employer.vue'
  //import inputSearch from '~/vue/atoms/inputs/input--search.vue'

  provide(DefaultApolloClient, defaultClient)

  const store = usePayRunStore()

</script>

<template>

  <div class="bg-white py-8 border-b border-solid border-gray-200">

    <div class="max-w-md w-full">
      <label for="search" class="sr-only">Search</label>
      <div class="relative">
        <div class="pointer-events-none absolute inset-y-0 left-0 pl-3 flex items-center">
          <!-- Heroicon name: solid/search -->
          <svg class="flex-shrink-0 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
          </svg>
        </div>
        <input name="search" id="search" class="block w-full bg-white border border-gray-300 rounded-md py-2 pl-10 pr-3 text-sm placeholder-gray-500 focus:outline-none focus:text-gray-900 focus:placeholder-gray-400 focus:ring-1 focus:ring-gray-900 focus:border-gray-900 sm:text-sm" placeholder="Search" type="search">
      </div>
    </div>

  </div>

  <div class="bg-white overflow-hidden">

      <ul role="list" class="divide-y divide-gray-400">
        <EmployerListItem />
        <EmployerListItem />
        <EmployerListItem />
      </ul>

  </div>
</template>