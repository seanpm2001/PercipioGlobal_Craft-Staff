<script setup lang="ts">
import { ref, computed } from 'vue';
import { VueDraggableNext } from 'vue-draggable-next'
import {useMutation, useQuery} from '@vue/apollo-composable'
import { VARIANT_ELIGIBLE_EMPLOYEES, VARIANT, ADD_VARIANT_EMPLOYEE, REMOVE_VARIANT_EMPLOYEE } from '~/graphql/variants.ts'

const { result:employees, loading:loadingEmployees, refetch:refetchEligibleEmployees } = useQuery(VARIANT_ELIGIBLE_EMPLOYEES, {'policyId': parseInt(policyId)})
const { result:variant, loading:loadingVariant, refetch:refetchVariant, onResult } = useQuery(VARIANT, {'id': parseInt(variantId)})
const { mutate:addVariant, onDone:onDoneAdd } = useMutation(ADD_VARIANT_EMPLOYEE)
const { mutate:removeVariant, onDone:onDoneRemove } = useMutation(REMOVE_VARIANT_EMPLOYEE)
const list2 = ref([])

const handleRemove = (id) => {
    removeVariant({
        variantId: parseInt(variantId),
        employeeId: parseInt(id)
    })
}

const handleUpdate = (evt) => {
    if(evt?.added) {
        addVariant({
            variantId: parseInt(variantId),
            employeeId: parseInt(evt.added.element.id)
        })
    }
}

onResult(queryResult => {
    list2.value = queryResult.data.BenefitVariant.employees
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
    if (list2.value) {
        return list2.value.sort((a, b) => (a.personalDetails.firstName+''+a.personalDetails.lastName > b.personalDetails.firstName+''+b.personalDetails.lastName) ? 1 : -1)
    }

    return list2.value
})

</script>

<template>
    <div
        v-if="loadingEmployees || loadingVariant"
        class="fixed flex items-center justify-center w-screen h-screen inset-0 bg-gray-500 bg-opacity-75 transition-opacity z-10"
    >
        <svg
            class="animate-spin h-5 w-5 text-indigo-900"
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
    </div>

    <div class="w-full items-stretch mt-10 md:flex">
        <div class="flex-grow mb-10 md:mb-0">

            <div class="mb-4 text-center">
                <span class="font-primary text-lg font-bold block pb-2">Eligible employees</span>
            </div>

            <div :class="[
                loadingEmployees ? 'opacity-60 pointer-events-none' : ''
            ]">
                <div class="p-2 relative min-h-[50px]">
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
                                v-if="!list2.find(employee => employee.id == item.id)"
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

            <div class="mb-4 px-4">
                <span class="font-primary text-lg font-bold block pb-2">Employees added in variant</span>
            </div>

            <div :class="[
                'w-full bg-black bg-opacity-10 flex-grow rounded-xl',
                loadingVariant ? 'opacity-60 pointer-events-none' : ''
            ]">
                <div class="p-2 h-full w-full box-border relative min-h-[50px]">
                    <VueDraggableNext
                        v-if="list2.length > 0"
                        v-model="list2"
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