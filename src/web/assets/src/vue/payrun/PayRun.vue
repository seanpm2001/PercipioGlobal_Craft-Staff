<script setup lang="ts">
  import { defaultClient } from '~/js/composables/useApolloClient'
  import { DefaultApolloClient } from '@vue/apollo-composable'
  import { provide } from 'vue'
  import { usePayRunStore } from '~/js/stores/payrun'

  provide(DefaultApolloClient, defaultClient)

  const store = usePayRunStore()

</script>

<template>
  <div class="p-8 bg-sky-400">
    Store: {{ store.employeeCount }}
  </div>
</template>