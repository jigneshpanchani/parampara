<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { onMounted , ref} from 'vue';
import { Head , useForm, usePage} from '@inertiajs/vue3';

const isLoading = ref(false);

const startLoading = () => {
    isLoading.value = true;

};

const stopLoading = () => {
    isLoading.value = false;
};

onMounted(() => {
    isLoading.value = true;
    setTimeout(() => {
        isLoading.value = false;
    }, 1000);
});

</script>

<template>
    <Head title="Dashboard" />
    <div  v-if="isLoading" class="loading-spinner">
        <svg class="animate-spin h-10 w-10 text-gray-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0c-4.418 0-8 3.582-8 8z"></path>
        </svg>
    </div>
</template>

<style>
.loading-spinner {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100%;
}
</style>

