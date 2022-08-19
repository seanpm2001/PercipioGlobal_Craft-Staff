<script setup lang="ts">
import { defineProps } from 'vue'

interface Props {
    request: Object,
}

defineProps<Props>()

</script>

<template>
    <a
        v-if="request"
        :href="`/admin/staff-management/requests/${request.id}`"
        :title="`Go to request ${request.id}`"
        class="grid grid-cols-11 border-b border-solid border-gray-200 no-underline hover:bg-gray-200"
    >
        <div class="col-span-2 flex items-center whitespace-nowrap pl-4 pr-3 sm:pl-6 py-4 text-sm text-gray-500">{{ request?.employee?.personalDetails?.firstName ? (request.employee.personalDetails.firstName + ' ' + request.employee.personalDetails.lastName) : '-' }}</div>
        <div class="col-span-2 flex items-center whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ request?.employer ? request.employer : '-' }}</div>
        <div class="col-span-2 flex items-center whitespace-nowrap px-3 py-4 text-sm text-gray-500 capitalize">{{ request?.type ? request.type.replace('_', ' ') : '-' }}</div>
        <div class="col-span-2 flex items-center whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ request?.dateCreated ? request.dateCreated : '-' }}</div>
        <div class="col-span-2 flex items-center whitespace-nowrap px-3 py-4 text-sm text-gray-500">
            {{ request?.admin ? request.admin : '-' }}
            <span v-if="request?.admin && request?.dateAdministered" class="block">
                on the {{ request?.dateAdministered ? request.dateAdministered : '-' }}
            </span>
        </div>
        <div class="flex items-center whitespace-nowrap px-3 pr-3 py-4 text-sm">
            <span :class="[
                'rounded-2xl text-xs font-bold px-3 py-1 mb-0',
                request?.status === 'pending' ? 'bg-yellow-300 text-yellow-900' : '',
                request?.status === 'approved' ? 'bg-emerald-300 text-emerald-900' : '',
                request?.status === 'declined' ? 'bg-red-300 text-red-900' : '',
                request?.status === 'canceled' ? 'bg-gray-300 text-gray-900' : ''
            ]">
                <span>{{ request?.status ? request.status.slice(0, 2) : '-' }}</span>
                <span class="hidden lg:inline">{{ request?.status ? request.status.slice(2, request.status.length) : '' }}</span>
            </span>
        </div>
    </a>
</template>