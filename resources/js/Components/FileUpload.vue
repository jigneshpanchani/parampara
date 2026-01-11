<template>
  <div class="col-span-full">
    <div class="mt-2 flex items-center gap-x-3">
      <div class="h-16 w-16 rounded-full"  v-if="selectedFile">
        <img class="h-16 w-16 rounded-full" v-if="selectedFile" :src="selectedFileURL" alt="Selected Image">
      </div>
      <div class="h-16 w-16 rounded-full 11" v-else>
        <img class="h-16 w-16 rounded-full" :src="fileUrl" alt="">
      </div>
      <input
        type="file"
        :id="inputId"
        class="hidden"
        ref="fileInput"
        @change="handleFileInput"
        :name="inputName"
      >
      <div class="flex flex-col gap-x-2">
        <label for="photo" @click="$refs.fileInput.click()" class="rounded-md bg-white w-20 px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 cursor-pointer hover:bg-gray-50">
            Upload
        </label>
        <span v-if="selectedFile" class="text-sm text-gray-900">{{ selectedFile.name }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, defineProps, defineEmits } from 'vue';

const emits = defineEmits(['file']);

const selectedFile = ref('');
const selectedFileURL = ref('');

defineProps({
  inputId: String,
  inputName: String,
  fileUrl: String
});

const handleFileInput = (event) => {
  const file = event.target.files[0];
  selectedFile.value = file;
  selectedFileURL.value = URL.createObjectURL(file);
  emits('file', file);
};
</script>

