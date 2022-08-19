<script setup lang="ts">
import { ref } from 'vue'
import { useMutation } from '@vue/apollo-composable'
import { UPDATE_REQUEST } from '~/graphql/requests.ts'

const form = ref(null)
const loading = ref(false)
const { mutate, onDone } = useMutation(UPDATE_REQUEST)

onDone( queryResult => {
    window.location.reload(false)
})

const preventSubmit = evt => {
    evt.preventDefault()
}

const handleSubmit = (evt, status) => {
    evt.preventDefault()

    const formValues = new FormData(form.value)
    loading.value = true

    mutate({
        id: parseInt(window.request.request.id),
        adminId: parseInt(window.request.admin),
        status: status,
        note: formValues.get('note')
    })
}

</script>

<template>
    <span class="text-xs font-bold text-gray-400 block mb-2">Note</span>
    <form @submit="preventSubmit" ref="form">
        <textarea class="block w-full bg-gray-50 p-4 rounded-lg border border-solid border-gray-200 box-border mb-6" name="note" placeholder="Type a not as additional information when approving or declining"></textarea>

        <div class="col-span-4 text-right">
            <button v-if="!loading" @click="(evt) => handleSubmit(evt, 'declined')" class="cursor-pointer inline-block bg-red-300 text-red-900 font-bold mr-2 py-2 px-3 text-sm rounded-lg cursor-pointer">Decline</button>
            <button v-if="!loading" @click="(evt) => handleSubmit(evt, 'approved')" class="cursor-pointer inline-block bg-emerald-300 text-emerald-900 font-bold mr-2 py-2 px-3 text-sm rounded-lg cursor-pointer">Approve</button>
            <svg v-if="loading" class="mb-0 animate-spin ml-1 h-5 w-5 text-gray-500 mb-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
        </div>
    </form>
</template>