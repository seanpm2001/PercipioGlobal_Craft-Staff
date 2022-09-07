<script setup lang="ts">
import { ref, computed } from 'vue';
import { VueDraggableNext } from 'vue-draggable-next'
import {useMutation, useQuery} from '@vue/apollo-composable'
import { VARIANT_ELIGIBLE_EMPLOYEES, VARIANT, ADD_VARIANT_EMPLOYEE, REMOVE_VARIANT_EMPLOYEE } from '~/graphql/variants.ts'

const { result:employees, loading:loadingEmployees, refetch:refetchEligibleEmployees } = useQuery(VARIANT_ELIGIBLE_EMPLOYEES, {'policyId': parseInt(policyId)})
const { result:variant, loading:loadingVariant, refetch:refetchVariant, onResult } = useQuery(VARIANT, {'id': parseInt(variantId)})
const { mutate:addVariant, onDone:onDoneAdd } = useMutation(ADD_VARIANT_EMPLOYEE)
const { mutate:removeVariant, onDone:onDoneRemove } = useMutation(REMOVE_VARIANT_EMPLOYEE)
const selectedEmployees = ref([])

const handleRemove = (id) => {
    loadingVariant.value = true
    removeVariant({
        variantId: parseInt(variantId),
        employeeId: parseInt(id)
    })
}

const handleUpdate = (evt) => {
    if(evt?.added) {
        loadingVariant.value = true
        addVariant({
            variantId: parseInt(variantId),
            employeeId: parseInt(evt.added.element.id)
        })
    }
}

onResult(queryResult => {
    selectedEmployees.value = queryResult.data.BenefitVariant.employees
})

onDoneAdd( queryResult => {
    refetchEligibleEmployees()
    refetchVariant()
})

onDoneRemove( queryResult => {
    refetchEligibleEmployees()
    refetchVariant()
})

const sortedEmployees = computed(() => {
    if (employees.value) {
        return employees.value.BenefitVariantEligibleEmployees.sort((a, b) => (a.personalDetails.firstName > b.personalDetails.firstName) ? 1 : -1)
    }

    return employees.value
})

const sortedList = computed(() => {
    if (selectedEmployees.value) {
        return selectedEmployees.value.sort((a, b) => (a.personalDetails.firstName+''+a.personalDetails.lastName > b.personalDetails.firstName+''+b.personalDetails.lastName) ? 1 : -1)
    }

    return selectedEmployees.value
})

</script>

<template>
    <div class="w-full items-stretch mt-10 md:flex">
        <div class="flex-grow mb-10 md:mb-0">

            <div class="mb-4 justify-center flex pb-2">
                <span class="font-primary text-lg font-bold block mb-0 relative">
                    Eligible employees
                <svg
                    v-if="loadingEmployees"
                    class="animate-spin h-5 w-5 text-indigo-900 mb-0 absolute top-1/2 left-full -mt-2.5 ml-2"
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
                </span>
            </div>

            <div :class="[
                loadingEmployees ? 'opacity-60 pointer-events-none' : ''
            ]">
                <div class="p-2 relative min-h-[50px]">
                    <span v-if="(employees?.BenefitVariantEligibleEmployees ?? []).length === 0  && !loadingEmployees" class="absolute top-0 left-0 p-4">There are no eligible employees</span>
                    <VueDraggableNext
                        v-if="employees"
                        v-model="employees.BenefitVariantEligibleEmployees"
                        :group="{ name: 'employees', pull: 'clone', put: false }"
                        :sort="false"
                    >
                        <div
                            v-for="item in sortedEmployees"
                            :key="item.id"
                        >
                            <div
                                v-if="!selectedEmployees.find(employee => employee.id == item.id)"
                                class="bg-white rounded-xl mb-2 w-full px-4 py-2 box-border flex items-center cursor-move h-12"
                            >
                                <span class="text-blue-600 text-xl leading-tight mb-0">✥</span>
                                <span class="font-bold flex-grow mb-0">{{ item.personalDetails.firstName }} {{ item.personalDetails.lastName }}</span>
                            </div>
                        </div>
                    </VueDraggableNext>
                </div>
            </div>

        </div>

        <div class="pt-1 px-6 hide md:block">
            <span class="text-blue-600 inline-block text-xl">→</span>
        </div>

        <div class="flex-grow flex flex-col">

            <div class="mb-4 px-4 flex items-center pb-2">
                <span class="font-primary text-lg font-bold block mb-0 relative">
                    Employees added in variant
                    <svg
                        v-if="loadingVariant"
                        class="animate-spin h-5 w-5 text-indigo-900 mb-0 absolute top-1/2 left-full -mt-2.5 ml-2"
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
                </span>
            </div>

            <div :class="[
                'w-full bg-black bg-opacity-10 flex-grow rounded-xl',
                loadingVariant ? 'opacity-60 pointer-events-none' : ''
            ]">
                <div class="pt-2 px-2 h-full w-full box-border relative min-h-[50px]">
                    <span v-if="selectedEmployees.length === 0 && !loadingVariant" class="absolute top-0 left-0 p-4">There are no selected employees</span>
                    <VueDraggableNext
                        v-model="selectedEmployees"
                        :sort="false"
                        group="employees"
                        @change="handleUpdate"
                        class="h-full box-border relative"
                    >
                        <div class="sticky top-[52px]">
                            <div
                                v-for="item in sortedList"
                                :key="item.id"
                                class="bg-white rounded-xl mb-2 w-full px-4 py-2 box-border flex items-center cursor-move h-12"
                            >
                                <span class="text-blue-600 text-xl leading-tight mb-0">✥</span>
                                <span class="font-bold flex-grow mb-0">{{ item.personalDetails.firstName }} {{ item.personalDetails.lastName }}</span>
                                <button @click="() => handleRemove(item.id)" class="text-gray-600 cursor-pointer mb-0">✕</button>
                            </div>
                        </div>
                    </VueDraggableNext>
                </div>
            </div>
        </div>
    </div>
</template>