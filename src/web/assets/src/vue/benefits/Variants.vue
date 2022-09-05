<script setup lang="ts">
import { useQuery } from '@vue/apollo-composable'
import { VARIANTS } from '~/graphql/variants.ts'

const { result, loading } = useQuery(VARIANTS)
</script>
<template>
    <div class="grid sm:grid-cols-2 md:grid-cols-3">
        <article class="bg-white shadow rounded-lg overflow-hidden no-underline flex flex-col" v-for="variant in result?.BenefitVariants">
            <header class="flex-grow flex flex-col w-full justify-start m-0 mr-0 box-border p-6" style="margin-right:0!important;">
                <h2 class="text-base mb-1 pt-0 w-full" style="margin-right:0!important;">{{ variant.name }}</h2>
                <h3 class="font-light text-4xl text-indigo-900 mt-2 mb-4 w-full" style="margin-right:0!important;">
                    £ {{ variant?.totalRewardsStatement?.monetaryValue ?? '-' }}
                </h3>

            </header>
        </article>
    </div>
</template>