<script setup>
import {computed} from 'vue';

const props = defineProps({
    isSorted: {
        type: Boolean,
        default: false,
    },
    direction: {
        type: String,
        default: 'asc',
    },
});
</script>

<template>
  <span v-if="isSorted">
    <svg v-if="direction === 'asc'" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline-block ml-2" fill="none" viewBox="0 0 24 24" stroke="#0000FF">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
    </svg>
    <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline-block ml-2" fill="none" viewBox="0 0 24 24" stroke="#0000FF">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
    </svg>
  </span>
</template>
