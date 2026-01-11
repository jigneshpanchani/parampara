<template>
  <div class="w-full items-center">
    <template v-if="fileType === 'pdf'">
      <iframe :src="fileUrl" width="100%" height="500px" style="max-width: 100%; max-height: 500px; overflow-y: auto;"></iframe>
    </template>
    <template v-else-if="fileType === 'image'">
      <img :src="fileUrl" alt="Image" style="max-width: 100%; max-height: 500px; overflow-y: auto;">
    </template>
    <template v-else>
      <p>No Image</p>
    </template>
  </div>
</template>

<script setup>
import { defineProps, computed } from 'vue';


const props = defineProps({
    fileUrl: {
        type: String,
    }
});

const fileType = computed(() => {
  const extension = props.fileUrl.split('.').pop().toLowerCase();
  if (extension === 'pdf') {
    return 'pdf';
  } else if (['jpg', 'jpeg', 'png'].includes(extension)) {
    return 'image';
  } else {
    return 'unsupported';
  }
});

</script>
