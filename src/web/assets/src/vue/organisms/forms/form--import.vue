<script setup lang="ts">
    const props = defineProps({
        payrun: Object,
    })

    const csrf = window.api.csrf

</script>

<template>
    <form method="post" accept-charset="UTF-8" enctype="multipart/form-data" class="inline-flex">
        <input type="hidden" name="CRAFT_CSRF_TOKEN" :value="csrf" />
        <input type="hidden" name="action" value="staff-management/pay-run/import">
        <input type="hidden" name="payRunId" :value="payrun?.id" />
        <input type="hidden" name="employerId" :value="payrun?.employerId" />
        <input type="file" class="fixed top-full" name="file" id="attendee-csv-file" accept=".csv" onchange="this.form.submit();" />
        <label for="attendee-csv-file" class="cursor-pointer inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto">Upload CSV To Staffology</label>
    </form>
</template>