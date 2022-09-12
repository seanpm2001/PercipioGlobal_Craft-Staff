<script setup lang="ts">
import { useQuery } from '@vue/apollo-composable'
import { VARIANTS } from '~/graphql/variants.ts'

const { result, loading } = useQuery(VARIANTS, {'policyId': parseInt(policyId)})
</script>
<template>

    <svg
        v-if="loading"
        class="animate-spin mr-3 h-5 w-5 text-indigo-900"
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        viewBox="0 0 24 24"
    >
        <circle
            class="opacity-25"
            cx="12"
            cy="12"
            r="10"
            stroke="currentColor"
            stroke-width="4"
        />
        <path
            class="opacity-75"
            fill="currentColor"
            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
        />
    </svg>

    <div class="grid sm:grid-cols-2 md:grid-cols-3" v-if="result">
        <a :href="`/admin/staff-management/benefits/variant/${variant.id}`" class="bg-white shadow rounded-lg overflow-hidden no-underline p-4" v-for="variant in result?.BenefitVariants">
            <h2 class="text-base mb-1 pt-0 w-full" style="margin-right:0!important;">{{ variant.name }}</h2>
            <h3 class="font-light text-4xl text-indigo-900 mt-2 mb-4 w-full" style="margin-right:0!important;">
                £ {{ variant?.totalRewardsStatement?.monetaryValue ?? '-' }}
            </h3>
            <p>{{ variant?.employees?.length ?? 0 }} employees attached</p>
<!--            <div>-->
<!--                <a :href="`/admin/staff-management/benefits/policy/${variant?.policy?.id}/variants/${variant.id}`" class="cursor-pointer inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 disabled:bg-indigo-400 disabled:cursor-not-allowed px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto">Manage</a>-->
<!--                <a :href="`/admin/staff-management/benefits/policy/${variant?.policy?.id}/variants/employees/${variant.id}`" class="cursor-pointer inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 disabled:bg-indigo-400 disabled:cursor-not-allowed px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto">Manage Employees</a>-->
<!--            </div>-->
        </a>
    </div>
</template>