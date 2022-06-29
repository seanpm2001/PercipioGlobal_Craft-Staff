<script lang="ts">
    import { defineComponent } from 'vue';
    import BenifitTypeSelect from '~/vue/atoms/inputs/input--benefit-type.vue'
    export default defineComponent({
        components: {
            'benefit-type-field': BenifitTypeSelect,
        },
    });
</script>