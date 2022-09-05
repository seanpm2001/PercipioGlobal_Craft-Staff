<script setup lang="ts">
import { useQuery } from '@vue/apollo-composable'
import { VARIANTS } from '~/graphql/variants.ts'

const { result, loading } = useQuery(VARIANTS)
</script>
<template>
    <div class="grid sm:grid-cols-2 md:grid-cols-3">
        <article v-for="variant in results">
            {{ variant.name }}
        </article>
    </div>
</template>