<script setup lang="ts">
import { ref } from 'vue'
import { useMutation } from '@vue/apollo-composable'
import { UPDATE_REQUEST } from '~/graphql/requests.ts'

const form = ref(null)
const loading = ref(false)
const showModal = ref(false)
const { mutate, error, onDone, onError } = useMutation(UPDATE_REQUEST)

onError(() => {
    loading.value = false
})

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
    showModal.value = false

    mutate({
        id: parseInt(window.request.request.id),
        adminId: parseInt(window.request.admin),
        status: status,
        note: formValues.get('note')
    })
}

const toggleModal = (evt, state) => {
    evt.preventDefault()
    showModal.value = state
}

</script>

<template>

    <span class="text-xs font-bold text-gray-400 block mb-2">Note</span>
    <form @submit="preventSubmit" ref="form">
        <textarea class="block w-full bg-gray-50 p-4 rounded-lg border border-solid border-gray-200 box-border mb-6" name="note" placeholder="Type a note as additional information when approving or declining"></textarea>

        <div v-if="error" class="bg-red-100 mb-0 flex-grow p-2 rounded-md mb-6">
            <p>Staffology {{ error }}</p>
        </div>

        <div class="text-right mb-0 justify-end">
            <button v-if="!loading" @click="(evt) => handleSubmit(evt, 'declined')" class="cursor-pointer inline-block bg-red-300 text-red-900 font-bold mr-2 py-2 px-3 text-sm rounded-lg cursor-pointer">Decline</button>
            <button v-if="!loading" @click="(evt) => toggleModal(evt, true)" class="cursor-pointer inline-block bg-emerald-300 text-emerald-900 font-bold mr-2 py-2 px-3 text-sm rounded-lg cursor-pointer">Approve</button>
            <svg v-if="loading" class="mb-0 animate-spin ml-1 h-5 w-5 text-gray-500 mb-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
        </div>
    </form>

    <div v-if="showModal" class="relative z-10" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

        <div class="fixed z-10 inset-0 overflow-y-auto">
            <div class="flex items-end sm:items-center justify-center min-h-full p-4 text-center sm:p-0">
                <div class="relative bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:max-w-lg sm:w-full sm:p-6">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <!-- Heroicon name: outline/exclamation -->
                            <svg class="h-6 w-6 text-red-600 mb-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Confirmation on approval</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">Are you sure you want to approve this request? By approving, the data in Staffology will be updated. This action cannot be undone.</p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse items-center">
                        <button type="button" @click="(evt) => handleSubmit(evt, 'approved')" class="mt-3 cursor-pointer inline-block bg-emerald-300 text-emerald-900 font-bold mr-2 py-2 px-3 text-sm rounded-lg cursor-pointermt">Approve</button>
                        <button type="button" @click="(evt) => toggleModal(evt, false)" class="mt-3 cursor-pointer inline-block bg-gray-300 text-gray-900 font-bold mr-2 py-2 px-3 text-sm rounded-lg cursor-pointermt">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>