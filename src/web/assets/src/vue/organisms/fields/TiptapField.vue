<script setup lang="ts">
    import editor from '~/vue/atoms/inputs/input--tiptap.vue'

    interface Options {
        id: string,
        instructions: string,
        label: string,
        name: string,
    }

    interface Props {
        options?: Options,
        content?: string,
    } 

    const props = withDefaults(defineProps<Props>(), {
        content: '<p>I’m running Tiptap with Vue.js. 🎉</p>'
    })
</script>

<template>

    <div
        :id="'fields-' + options.id + '-field'"
        class="field" 
    >

        <div class="heading">
            <label
                :id="'fields-' + options.id + '-label'"
                :class="options.required ? 'required' : ''"
            >
                {{ options.label }}
            </label>
        </div>

        <div
            :id="'fields-' + options.id + '-instructions'"
            class="instructions">
            <span>
                {{ options.instructions }}
            </span>
        </div>

        <editor v-model="content" />

        <input 
            type="hidden"
            :id="'fields-' + options.id"
            :name="'fields-' + options.name"
            :aria-describedby="'fields-' + options.name + '-instructions'"
            :value="content"
        >
    </div>
    
</template>