<script setup>
import { ref, defineProps, defineEmits } from 'vue';

const props = defineProps(['switchValue','userId']);
const emits = defineEmits(['updateSwitchValue']);

const toggleSwitch = () => {
  emits('updateSwitchValue', !props.switchValue, props.userId);
};
</script>

<template>
  <button
    type="button"
    :class="{ 'bg-gray-200': !props.switchValue, 'bg-indigo-600': props.switchValue }"
    class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2"
    role="switch"
    :aria-checked="props.switchValue"
    @click="toggleSwitch"
  >
    <span class="sr-only">Use setting</span>
    <span
      :class="{ 'translate-x-0': !props.switchValue, 'translate-x-5': props.switchValue }"
      aria-hidden="true"
      class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
    ></span>
  </button>
</template>


