<script setup lang="ts">
    import { format } from '~/js/composables/useCurrencyFormat'

    const currentYear = window.location.href.split('/').pop()

    const props = defineProps({
        payrunData: Object,
    })
    
</script>

<template>
    <a 
        v-for="payrun in payrunData" :key="payrun.id"
        :href="`/admin/staff-management/pay-runs/${payrun.employerId}/${currentYear}/${payrun.id}`" 
        :title="`Go to pay run ${payrun.period}/${payrun.taxYear}`" 
        class="grid grid-cols-4 lg:grid-cols-7 border-b border-solid border-gray-200 no-underline hover:bg-gray-200" 
    >
        <div class="flex items-center whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-indigo-800 sm:pl-6">
            <span :class="[
                'rounded-full w-5 h-5 fond-bold flex items-center justify-center mb-0',
                payrun.state == 'Open' ? 'border-2 border-solid border-emrald-500 text-emerald-500': '',
                payrun.state == 'Closed' ? 'border-2 border-solid border-green-500 text-white bg-emerald-500': '',
                payrun.state == 'Finalised' ? 'border-2 border-solid border-green-500 text-white bg-emerald-500': ''
            ]">{{ payrun.period }}</span>
        </div>
        <div class="hide lg:flex lg:items-center whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ payrun.employeeCount }}</div>
        <div class="hide lg:flex lg:items-center col-span-2 whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ payrun.startDate }} - {{ payrun.endDate }}</div>
        <div class="hide lg:flex lg:items-center whitespace-nowrap px-3 py-4 text-sm text-gray-500">£ {{ format(payrun.totals.totalCost) }}</div>
        <div class="flex items-center whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ payrun.paymentDate }}</div>
        <div class="flex items-center whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ payrun.dateSynced }}</div>
    </a>
</template>