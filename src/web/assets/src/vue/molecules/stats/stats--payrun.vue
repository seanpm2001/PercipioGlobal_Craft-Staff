<script setup lang="ts">
    import { format } from '~/js/composables/useCurrencyFormat'

    const props = defineProps({
        payrun: Object,
    })

</script>

<template>

    <dl class="mt-5 grid grid-cols-2 md:grid-cols-3 gap-3 lg:gap-5 lg:grid-cols-5">
        <div class="px-4 py-5 bg-gray-200 shadow rounded-lg overflow-hidden sm:p-6">
            <dt class="text-sm font-medium text-gray-500 truncate text-center">
                Payment Date
            </dt>
            <dd class="mt-1 text-2xl font-semibold text-gray-900 text-center">
                {{ payrun?.paymentDate }}
            </dd>
        </div>
        <div class="px-4 py-5 bg-gray-200 shadow rounded-lg overflow-hidden sm:p-6">
            <dt class="text-sm font-medium text-gray-500 truncate text-center">
                Total pay
            </dt>
            <dd class="mt-1 text-2xl font-semibold text-gray-900 text-center">
                £ {{ format(payrun?.totals?.gross) }}
            </dd>
        </div>
        <div class="px-4 py-5 bg-gray-200 shadow rounded-lg overflow-hidden sm:p-6">
            <dt class="text-sm font-medium text-gray-500 truncate text-center">
                Tax
            </dt>
            <dd class="mt-1 text-2xl font-semibold text-gray-900 text-center">
                £ {{ format(payrun?.totals?.tax) }}
            </dd>
        </div>
        <div class="px-4 py-5 bg-gray-200 shadow rounded-lg overflow-hidden sm:p-6">
            <dt class="text-sm font-medium text-gray-500 truncate text-center">
                Employee NI
            </dt>
            <dd class="mt-1 text-2xl font-semibold text-gray-900 text-center">
                £ {{ format(payrun?.totals?.employeeNi) }}
            </dd>
        </div>
        <div class="px-4 py-5 bg-gray-200 shadow rounded-lg overflow-hidden sm:p-6">
            <dt class="text-sm font-medium text-gray-500 truncate text-center">
                Employer NI
            </dt>
            <dd class="mt-1 text-2xl font-semibold text-gray-900 text-center">
                £ {{ format(payrun?.totals?.employerNi) }}
            </dd>
        </div>
    </dl>
        
</template>