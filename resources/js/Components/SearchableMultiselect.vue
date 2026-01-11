<template>
  <div>
    <input id="comobox" type="text" placeholder="Search..." role="comobox"
        v-model="searchTerm"
        @input="filterOptions"
         @focus="openBox"
         autocomplete="off"
        class="w-full rounded-md border-0 bg-white py-1.5 pl-3 pr-12 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">

      <button type="button" class="absolute inset-y-0 right-0 flex items-center rounded-r-md px-2 focus:outline-none"
        @click="openBox">
            <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M10 3a.75.75 0 01.55.24l3.25 3.5a.75.75 0 11-1.1 1.02L10 4.852 7.3 7.76a.75.75 0 01-1.1-1.02l3.25-3.5A.75.75 0 0110 3zm-3.76 9.2a.75.75 0 011.06.04l2.7 2.908 2.7-2.908a.75.75 0 111.1 1.02l-3.25 3.5a.75.75 0 01-1.1 0l-3.25-3.5a.75.75 0 01.04-1.06z" clip-rule="evenodd" />
            </svg>
      </button>
    <ul
        v-if="isOpen && filteredOptions.length"
        class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm"
        id="options" role="listbox" aria-labelledby="comobox"
       >
      <li class="relative cursor-default select-none py-2 pl-3 pr-9" v-for="(option, index) in filteredOptions" :key="index"
      @click="toggleSelection(option)"
      @mouseenter="highlightOption(index)"
        :class="{ 'text-white bg-indigo-600': highlightedOption === index, 'text-gray-900': highlightedOption !== index }" tabindex="-1" role="option"
      >
        <span class="block truncate">{{ option.name }}</span>
        <span  v-if="isSelected(option)" class="absolute inset-y-0 right-0 flex items-center pr-4"
        :class="{ 'text-white': highlightedOption === index, 'text-indigo-600': highlightedOption !== index }">
                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                </svg>
        </span>
      </li>
    </ul>
  </div>
</template>

<script setup>
import { ref, computed, defineProps, defineEmits, onUnmounted , onMounted } from 'vue';

const props = defineProps({
  options: { type: Array, required: true },
  modelValue: { type: Array, default: () => [] }
});

const emits = defineEmits(['onchange']);

const selectedOptions = ref(props.modelValue);
const searchTerm = ref('');
const isOpen = ref(false);
const highlightedOption = ref(-1);

const highlightOption = index => {
  highlightedOption.value = index;
};


const filteredOptions = computed(() => {
  return props.options.filter(option => option.name.toLowerCase().includes(searchTerm.value.toLowerCase()));
});

const filterOptions = () => {
  isOpen.value = true;
};

const toggleSelection = (option) => {
  const index = selectedOptions.value.findIndex((selectedOption) => selectedOption.id === option.id);
  if (index === -1) {
    selectedOptions.value.push(option);
  } else {
    selectedOptions.value.splice(index, 1);
  }
  emits('onchange',selectedOptions.value);
  document.addEventListener('click', closeDropdown);
};

const isSelected = (option) => {
  return selectedOptions.value.some((selectedOption) => selectedOption.id === option.id);
};

const openBox = () => {
  isOpen.value = true;
};

onMounted(() => {
//   document.addEventListener('click', closeDropdown);
});

onUnmounted(() => {
  document.removeEventListener('click', closeDropdown);
});

const closeDropdown = event => {
  if (!event.target.closest('.relative')) {
    isOpen.value = false;
  }
};
</script>
