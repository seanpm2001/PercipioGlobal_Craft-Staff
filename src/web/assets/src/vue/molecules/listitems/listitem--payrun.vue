<script setup lang="ts">
    const props = defineProps({
        payrunData: Object,
    })
</script>

<template>
    <a 
        v-for="payrun in payrunData" :key="payrun.id"
        :href="`/admin/staff-management/pay-runs/${payrun.employerId}/${payrun.id}`" 
        :title="`Go to pay run ${payrun.period}/${payrun.taxYear}`" 
        class="grid grid-cols-5 lg:grid-cols-9 border-b border-solid border-gray-200" 
    >
        <div class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-indigo-800 sm:pl-6">
            <span :class="[
                'rounded-full w-5 h-5 fond-bold flex items-center justify-center',
                payrun.state == 'Open' ? 'border-2 border-solid border-emrald-500 text-emerald-500': '',
                payrun.state == 'Closed' ? 'border-2 border-solid border-green-500 text-white bg-emerald-500': ''
            ]">{{ payrun.period }}</span>
        </div>
        <div class="hide lg:block whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ payrun.employeeCount }}</div>
        <div class="hide lg:block col-span-2 whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ payrun.startDate }} - {{ payrun.endDate }}</div>
        <div class="hide lg:block whitespace-nowrap px-3 py-4 text-sm text-gray-500">£ {{ payrun.totalCost }}</div>
        <div class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ payrun.paymentDate }}</div>
        <div class="text-center lg:text-left px-3 py-3.5 text-sm font-semibold text-gray-900">

        {{payrun.importApproved}}
            <span v-if="payrun.importApproved == 1" class="block w-4 h-4 xl:inline-block xl:w-auto xl:h-auto rounded-full text-xs bg-emerald-400 text-white text-bold xl:px-4 xl:py-1"><span class="opacity-0 pointer-events-none xl:opacity-100">Approved</span></span>
            <span v-else-if="payrun.importApproved == 0" class="block w-4 h-4 xl:inline-block xl:w-auto xl:h-auto rounded-full text-xs bg-orange-300 text-orange-800 text-bold xl:px-4 xl:py-1"><span class="opacity-0 pointer-events-none xl:opacity-100">Pending</span></span>
            <span v-else class="block w-4 h-4 xl:inline-block xl:w-auto xl:h-auto rounded-full text-xs bg-gray-400 text-gray-800 text-bold xl:px-4 xl:py-1"><span class="opacity-0 pointer-events-none xl:opacity-100">Not applicable</span></span>
        </div>
        <div class="col-span-2 whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ payrun.dateUpdated }}</div>
    </a>
</template>