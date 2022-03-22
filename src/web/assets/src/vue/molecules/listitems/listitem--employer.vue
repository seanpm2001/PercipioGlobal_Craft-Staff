<script setup lang="ts">
    const props = defineProps({
        employer: Object,
    })
</script>

<template>
    {{ employer }}
    <a 
        :href="`/admin/staff-management/pay-runs/${employer.id}`" 
        :title="`Go to pay runs of ${employer.name}`" 
        class="grid grid-cols-6 border-b border-solid border-gray-200" 
    >
        <div class="col-span-2 whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-indigo-800 sm:pl-6 flex">
            <div class="object-cover object-center w-6 h-6 rounded-full overflow-hidden mb-0">
                <img :src="employer.logoUrl" class="w-full h-full" />
            </div>
            <span style="margin-bottom:0">{{ employer.name }}</span>
        </div>
        <div class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ employer.crn }}</div>
        <div class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ employer.employeeCount }}</div>
        <div class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ employer.currentYear }}</div>
        <div class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ employer.synced }}</div>
    </a>
</template>