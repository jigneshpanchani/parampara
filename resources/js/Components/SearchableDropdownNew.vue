<template>
    <div>
        <input id="combobox"
               type="text"
               placeholder="Search..."
               role="combobox"
               v-model="searchTerm"
               @input="filterOptions"
               @focus="openBox"
               autocomplete="off"
               class="w-full rounded-md border-0 bg-white py-1.5 pl-3 pr-7 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">

        <button type="button" class="absolute inset-y-0 right-0 flex items-center rounded-r-md px-2 focus:outline-none"
                @click="openBox">
            <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M10 3a.75.75 0 01.55.24l3.25 3.5a.75.75 0 11-1.1 1.02L10 4.852 7.3 7.76a.75.75 0 01-1.1-1.02l3.25-3.5A.75.75 0 0110 3zm-3.76 9.2a.75.75 0 011.06.04l2.7 2.908 2.7-2.908a.75.75 0 111.1 1.02l-3.25 3.5a.75.75 0 01-1.1 0l-3.25-3.5a.75.75 0 01.04-1.06z" clip-rule="evenodd"/>
            </svg>
        </button>
        <ul
            v-if="isOpen && filteredOptions.length"
            class="absolute z-10 mt-1 max-h-40 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm cursor-pointer"
            id="options" role="listbox" aria-labelledby="combobox"
        >
            <li
                class="relative cursor-default select-none py-2 pl-3 pr-9 cursor-pointer" v-for="(option, index) in filteredOptions"
                :key="index"
                @click="selectOption(option.name, option.id)"
                @mouseenter="highlightOption(index)"
                :class="{
                    'text-white bg-indigo-600': highlightedOption === index,
                    'text-gray-900': highlightedOption !== index
                }"
                tabindex="-1"
                role="option"
            >
                <span class="block truncate" :class="{ 'font-semibold': option.name === searchTerm}">
                    {{ option.name }}
                </span>
                <span class="absolute inset-y-0 right-0 flex items-center pr-4"
                      :class="{ 'text-white': highlightedOption === index, 'text-indigo-600': highlightedOption !== index }"
                      v-if="option.name === searchTerm">
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd"/>
                    </svg>
              </span>
            </li>
        </ul>
    </div>
</template>

<script setup>
import {ref, computed, watch, onMounted, onUnmounted, defineProps, defineEmits} from 'vue';

const props = defineProps(['options', 'modelValue', 'editMode']);
const emits = defineEmits(['onchange']);

const filteredOptions = ref(props.options);
const searchTerm = ref('');
const isOpen = ref(false);
const highlightedOption = ref(-1);
const hasModifiedInput = ref(false); // Flag to track if input is modified after selection


const filterOptions = () => {
    const regex = new RegExp(searchTerm.value, 'i');
    filteredOptions.value = props.options.filter(option => regex.test(option.name));
    // isOpen.value = false;
};

const clearSearchTerm = () => {
    searchTerm.value = ''; // Clear the input
    filterOptions(); // Re-filter the options when the search term is cleared
};

const selectOption = (name, id) => {
    searchTerm.value = name;
    isOpen.value = false;
    hasModifiedInput.value = false; // Reset flag on selection
    emits('onchange', id, name);
};

const openBox = () => {
//   isOpen.value = true;
    if (!props.editMode) {
        isOpen.value = true;
    }
    if (!hasModifiedInput.value) {
        clearSearchTerm();  // Call the method to clear the search term and filter options
    }
    // searchTerm.value = '';
};

const highlightOption = index => {
    highlightedOption.value = index;
};

watch(() => props.options, () => {
    filterOptions();
});

watch(() => props.modelValue, (newValue) => {
    if (newValue === '') {
        // searchTerm.value = '';
        clearSearchTerm(); // Clear search term if model value is empty

    }
});

onMounted(() => {
    const selectedOption = props.options.find(option => option.id === props.modelValue);
    if (selectedOption) {
        searchTerm.value = selectedOption.name;
        hasModifiedInput.value = false; // Reset flag to no modification after initial selection
    } else {
        // searchTerm.value = '';
        clearSearchTerm();
    }
    document.addEventListener('click', closeDropdown);
});

onUnmounted(() => {
    document.removeEventListener('click', closeDropdown);
});

const closeDropdown = event => {
    if (!event.target.closest('.relative')) {
        isOpen.value = false;
    }
};

const onInputChange = (event) => {
    if (!hasModifiedInput.value) {
        hasModifiedInput.value = true;  // Set flag to true when user starts typing
    }
    searchTerm.value = event.target.value;
};

</script>
