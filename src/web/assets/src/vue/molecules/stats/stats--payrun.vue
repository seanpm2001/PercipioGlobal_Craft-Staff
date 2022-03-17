<script setup lang="ts">
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
                {{ payrun.paymentDate }}
            </dd>
        </div>
        <div class="px-4 py-5 bg-gray-200 shadow rounded-lg overflow-hidden sm:p-6">
            <dt class="text-sm font-medium text-gray-500 truncate text-center">
                Total Net
            </dt>
            <dd class="mt-1 text-2xl font-semibold text-gray-900 text-center">
                £ {{ payrun.totalCost }}
            </dd>
        </div>
        <div class="px-4 py-5 bg-gray-200 shadow rounded-lg overflow-hidden sm:p-6">
            <dt class="text-sm font-medium text-gray-500 truncate text-center">
                Total Tax
            </dt>
            <dd class="mt-1 text-2xl font-semibold text-gray-900 text-center">
                £ {{ payrun.totalTax }}
            </dd>
        </div>
        <div class="px-4 py-5 bg-gray-200 shadow rounded-lg overflow-hidden sm:p-6">
            <dt class="text-sm font-medium text-gray-500 truncate text-center">
                Total EE &amp; ER Nics
            </dt>
            <dd class="mt-1 text-2xl font-semibold text-gray-900 text-center">
                £ {{ payrun.totalTax }}
            </dd>
        </div>
        <div class="px-4 py-5 bg-gray-200 shadow rounded-lg overflow-hidden sm:p-6">
            <dt class="text-sm font-medium text-gray-500 truncate text-center">
                Total cost
            </dt>
            <dd class="mt-1 text-2xl font-semibold text-gray-900 text-center">
                £ {{ payrun.totalCost }}
            </dd>
        </div>
    </dl>
        
</template>