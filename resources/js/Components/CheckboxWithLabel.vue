<script setup>
import { computed } from 'vue';

const emit = defineEmits(['update:checked']);

const props = defineProps({
    checked: {
        type: Array,
        default: () => [],  // Ensure it defaults to an empty array
    },
    value: {
        default: null,
    },
    label: {
        type: String,
        default: '',  // Default to an empty string if no label is provided
    },
});

const proxyChecked = computed({
    get() {
        // Check if props.checked is indeed an array
        return Array.isArray(props.checked) ? props.checked.includes(props.value) : false;
    },

    set(val) {
        // Ensure props.checked is always an array before manipulation
        const currentChecked = Array.isArray(props.checked) ? [...props.checked] : [];
        if (val) {
            if (!currentChecked.includes(props.value)) {
                currentChecked.push(props.value);
            }
        } else {
            const index = currentChecked.indexOf(props.value);
            if (index > -1) {
                currentChecked.splice(index, 1);
            }
        }
        emit('update:checked', currentChecked);
    },
});
</script>

<template>
    <label class="flex items-center space-x-2 mt-2 sm:col-span-3">
        <input
            type="checkbox"
            :value="value"
            v-model="proxyChecked"
            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
        />
        <div class="font-sm text-gray-900 font-medium">{{ label }}</div>
    </label>
</template>

<style scoped>
/* Optional styling for better alignment and appearance */
label {
    display: flex;
    align-items: center;
    cursor: pointer;
}

input[type="checkbox"] {
    margin-right: 8px; /* Space between checkbox and label */
}
</style>
