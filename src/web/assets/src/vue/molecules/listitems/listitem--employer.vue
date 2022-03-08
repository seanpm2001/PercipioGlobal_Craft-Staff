<script setup lang="ts">
    const props = defineProps({
        employerData: Object,
    })
</script>

<template>
    <li>
        <a class="flex flex-col w-full hover:bg-gray-50 hover:no-underline py-2">

            <div class="flex items-center w-full">

                <h5 class="text-lg font-medium text-indigo-600">
                    Company Name
                </h5>

                <div class="ml-auto flex-shrink-0 flex">
                    <p class="px-4 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                        Pay Run: 11
                    </p>
                </div>

            </div>

            <div class="sm:flex w-full">

                <div class="sm:flex space-x-8">
                    <div class="flex flex-col items-start">
                        <span class="font-bold text-xs">
                            CRN
                        </span>
                        <span class="text-sm">
                            123456789
                        </span>
                    </div>
                    
                    <div class="flex flex-col items-start">
                        <span class="font-bold text-xs">
                            Employees
                        </span>
                        <span class="text-sm">
                            216
                        </span>
                    </div>
                </div>

                <div class="ml-auto flex items-center text-sm text-gray-500 sm:mt-0 sm:pr-2">
                    <p>
                        Last synced on
                        <time datetime="2020-01-07">
                            January 7, 2020
                        </time>
                    </p>
                </div>
            </div>
        </a>
    </li>
</template>