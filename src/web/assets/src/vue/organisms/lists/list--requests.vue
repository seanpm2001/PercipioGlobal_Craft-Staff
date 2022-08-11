<script setup lang="ts">
import ListItem from '~/vue/molecules/listitems/listitem--request.vue'

const props = defineProps({
    requests: Object,
})
</script>

<template>
    <ListItem v-for="request in requests" :key="request.id" :request="request" />
</template>