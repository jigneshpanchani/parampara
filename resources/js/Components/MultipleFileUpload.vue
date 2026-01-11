<template>
  <div class="col-span-full">
    <div class="mt-2">
      <input
        type="file"
        :id="inputId"
        class="hidden"
        ref="fileInput"
        @change="handleFileInput"
        :name="inputName"
        multiple
      >
      <div class="flex flex-col gap-x-2">
        <label for="photo" @click="$refs.fileInput.click()" class="rounded-md bg-white w-20 px-4 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 cursor-pointer hover:bg-gray-50">
            Upload
        </label>
        <span v-for="file in selectedFiles" v-if="selectedFiles.length" :key="file.name" class="text-sm text-gray-900">{{ file.name }}</span>
        <span v-if="errorMessage" class="text-sm text-red-500">{{ errorMessage }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, defineProps, defineEmits } from 'vue';

const emits = defineEmits(['files']);

const selectedFiles = ref([]);
const errorMessage = ref('');

defineProps({
  inputId: String,
  inputName: String
});

const handleFileInput = (event) => {
  const files = event.target.files;
  const validTypes = ['application/pdf', 'image/jpeg', 'image/jpg'];

  const filteredFiles = Array.from(files).filter(file => validTypes.includes(file.type));

  const invalidFiles = Array.from(files).filter(file => !validTypes.includes(file.type));

  if (invalidFiles.length > 0) {
    errorMessage.value = 'Only PDF, JPG, and JPEG files are allowed.';
  } else {
    errorMessage.value = '';
  }

  selectedFiles.value = filteredFiles;

  emits('files', selectedFiles.value);
};
</script>



