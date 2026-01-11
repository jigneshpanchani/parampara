<script setup>
import { ref , defineProps} from 'vue';

const props = defineProps({
    userId: { type: [String, Number], required: true },
    visible: { type: Boolean, required: true },
})

const deleteUser = () => {
  // Implement your delete logic here using the userId
  // For example, you can make an API request to delete the user
  //console.log('Deleting user with ID:', userId.value);
//   closeModal();
};

const closeModal = () => {
    visible.value = false;
};

</script>

<template>
  <div v-if="visible" class="fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center">
    <!-- Modal content goes here -->
    <div class="bg-white p-4 rounded">
      <h2 class="text-lg font-semibold mb-4">Are you sure you want to delete this user?</h2>
      <div class="flex justify-end">
        <button type="button" @click="deleteUser">Delete</button>
        <button type="button" @click="closeModal" class="ml-2">Cancel</button>
      </div>
    </div>
  </div>
</template>


