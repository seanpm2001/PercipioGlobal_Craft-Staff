<script setup lang="ts">
import { ref } from 'vue'

const form = ref(null)

const preventSubmit = evt => {
    evt.preventDefault()
}

const handleSubmit = status => {
    const formValues = new FormData(form.value)
    formValues.append('state', status)

    console.log(window.request)
}

</script>

<template>
    <span class="text-xs font-bold text-gray-400 block mb-2">Note</span>
    <form @submit="preventSubmit" ref="form">
        <textarea class="block w-full bg-gray-50 p-4 rounded-lg border border-solid border-gray-200 box-border mb-6" name="note" placeholder="Type a not as additional information when approving or declining"></textarea>

        <div class="col-span-4 text-right">
            <button @click="() => handleSubmit('decline')" class="cursor-pointer inline-block bg-red-300 text-red-900 font-bold mr-2 py-2 px-3 text-sm rounded-lg cursor-pointer">Decline</button>
            <button @click="() => handleSubmit('accept')" class="cursor-pointer inline-block bg-emerald-300 text-emerald-900 font-bold mr-2 py-2 px-3 text-sm rounded-lg cursor-pointer">Approve</button>
        </div>
    </form>
</template>