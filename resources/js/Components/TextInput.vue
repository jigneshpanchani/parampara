<script setup>
import { onMounted, ref } from 'vue';

const props = defineProps({
    modelValue: {
        required: true,
    },
    numeric: {
        type: Boolean,
        default: false,
    },
});

const emits = defineEmits(['update:modelValue']);

const input = ref(null);

const handleNumericInput = (field) => {
    return field.replace(/\D/g, '');
};

const handleInput = (event) => {
    const inputValue = event.target.value;
    const valueToEmit = props.numeric ? handleNumericInput(inputValue) : inputValue;
    if (valueToEmit == '') {
        input.value.value = '';
    }
    emits('update:modelValue', valueToEmit);
};

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
<!-- block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 -->
    <input
        class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
        :value="modelValue"
        @input="handleInput"
        autocomplete="off"
        ref="input"
    />
</template>
