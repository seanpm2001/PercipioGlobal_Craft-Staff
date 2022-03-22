<script setup lang="ts">
    import EmployerListItem from '~/vue/molecules/listitems/listitem--employer.vue'

    const props = defineProps({
        employers: Object,
    })
</script>

<template>
    <EmployerListItem v-for="employer in employers" :key="employer.id" :employer="employer" />
</template>