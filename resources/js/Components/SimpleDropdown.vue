<template>
    <div class="">
        <button type="button" class="w-full rounded-md border-0 bg-white py-1.5 pl-3 pr-12 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" @click="toggleDropdown">
            <div class="flex items-start">
                <span class="block truncate">{{ selectedOption.name }}</span>
            </div>
        </button>
        <ul v-show="isOpen" class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm cursor-pointer">
            <li v-for="(option, index) in options" :key="index" @click="selectOption(option.name, option.id)" @mouseenter="highlightOption(index)"
                :class="{ 'text-white bg-indigo-600': highlightedOption === index, 'text-gray-900': highlightedOption !== index }"
                class="relative cursor-default select-none py-2 pl-3 pr-9 cursor-pointer" tabindex="-1" role="option">
                <span class="block truncate" :class="{ 'font-semibold': option.name === selectedOption.name }">{{ option.name }}</span>
                <span class="absolute inset-y-0 right-0 flex items-center pr-4" v-if="option.name === selectedOption.name"
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
import { ref, onBeforeMount, watch, onUnmounted, defineProps, defineEmits } from 'vue';

const props = defineProps(['options', 'modelValue']);
const emits = defineEmits(['onchange']);

const selectedOption = ref({ name: props.options[0].name, id: props.options[0].id });
const isOpen = ref(false);
const highlightedOption = ref(-1);

const toggleDropdown = () => {
    isOpen.value = !isOpen.value;
};

const selectOption = (name, id) => {
    selectedOption.value = { name, id };
    isOpen.value = false;
    emits('onchange', id, name);
};

const highlightOption = index => {
    highlightedOption.value = index;
};

onBeforeMount(() => {
    const selecteOption = props.options.find(option => option.id == props.modelValue);
    selectedOption.value = selecteOption;
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
</script>
