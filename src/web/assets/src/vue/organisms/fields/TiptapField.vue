<script setup lang="ts">
    import editor from '~/vue/atoms/inputs/input--tiptap.vue'
    import { ref } from 'vue'

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
        content: ''
    })

    const richText = ref(props.content);
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

        <editor v-model:content="richText" />

        <input 
            type="hidden"
            :id="'fields-' + options.id"
            :name="options.name"
            :aria-describedby="'fields-' + options.name + '-instructions'"
            :value="richText"
        >
    </div>
    
</template>