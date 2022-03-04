<script setup lang="ts">
  import { defaultClient } from '~/js/composables/useApolloClient'
  import { DefaultApolloClient } from '@vue/apollo-composable'
  import { provide } from 'vue'

  provide(DefaultApolloClient, apolloClient)

</script>

<template>
  <div class="p-8 bg-sky-400">
    Hello
  </div>
</template>