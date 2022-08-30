<script setup lang="ts">
import {ref} from 'vue'
const id = ref(policyId)
</script>
<template>
    <a :href="`/admin/staff-management/benefits/policy/${id}/delete`" class="cursor-pointer inline-flex items-center justify-center rounded-md border border-transparent bg-red-600 disabled:bg-red-400 disabled:cursor-not-allowed px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 sm:w-auto">Delete Policy</a>
</template>