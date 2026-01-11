<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    modelValue: {
        type: [String, Number, Boolean],
        required: true,
    },
    options: {
        type: Array,
        required: true,
    },
});

const emit = defineEmits(['update:modelValue']);

const handleChange = (optionValue) => {
    emit('update:modelValue', optionValue);
};

const isSelected = (optionValue) => {
    return props.modelValue === optionValue;
};
</script>

<template>
    <div class="flex space-x-4">
        <div class="flex items-center" v-for="option in options" :key="option.value">
            <input
                type="radio"
                :value="option.value"
                :id="option.value"
                v-model="props.modelValue"
                @change="handleChange(option.value)"
                class="border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
            />
            <label :for="option.value" class="ml-2 cursor-pointer">{{ option.label }}</label>
        </div>
    </div>
</template>
