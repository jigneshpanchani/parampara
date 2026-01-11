<template>
  <input
    class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
    type="date"
    :value="modelValue"
    @input="handleInput"
    autocomplete="off"
    ref="input"
  />
</template>

<script setup>
import { onMounted, ref } from 'vue';

const props = defineProps({
    modelValue: {
        required: true,
    },
});

const emits = defineEmits(['update:modelValue']);

const input = ref(null);

const handleInput = (event) => {
    const inputValue = event.target.value;
    emits('update:modelValue', inputValue);
};

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>
