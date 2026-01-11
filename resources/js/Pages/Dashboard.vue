<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { onBeforeMount, ref, computed } from "vue";
import SimpleDropdown from '@/Components/SimpleDropdown.vue';
import BarChart from '@/Components/BarChart.vue';
import PieChart from '@/Components/PieChart.vue';
import MultiLineChart from '@/Components/MultiLineChart.vue';
import Pagination from '@/Components/Pagination.vue';
import { Head , useForm} from '@inertiajs/vue3';
import ActionLink from '@/Components/ActionLink.vue';

const form = useForm({});

const props = defineProps([
    "permissions",
]);

onBeforeMount(async () => {
    localStorage.setItem("permissions", JSON.stringify(props.permissions));
});

</script>

<template>
    <Head title="Dashboard" />

    <AdminLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-700 leading-tight">
                Dashboard
            </h2>
        </template>
        <div class="animate-top">
            <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center gap-4 mb-6">
                <div class="items-start">
                    <h1 class="text-xl lg:text-2xl font-semibold leading-7 text-gray-900">Dashboard</h1>
                </div>
            </div>
            <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                <div class="w-full cursor-pointer hover:shadow-md shadow rounded-lg">
                    <div class="w-full bg-white border text-indigo-600 rounded-lg flex items-center p-4 lg:p-6">
                        <svg class="w-12 h-12 lg:w-16 lg:h-16 fill-current mr-4 flex-shrink-0" viewBox="0 0 24 24">
                            <circle cx="6" cy="8" r="3"/>
                            <path d="M2 18v-1.5c0-1.8 2.5-3.5 4.5-3.5 1 0 2 .2 2.8.6-1.1.8-1.8 2.1-1.8 3.4V18H2z"/>
                            <circle cx="12" cy="7" r="4"/>
                            <path d="M12 12c-3.5 0-7 1.8-7 4v2h14v-2c0-2.2-3.5-4-7-4z"/>
                            <circle cx="18" cy="8" r="3"/>
                            <path d="M22 18v-1.5c0-1.8-2.5-3.5-4.5-3.5-1 0-2 .2-2.8.6 1.1.8 1.8 2.1 1.8 3.4V18H22z"/>
                        </svg>
                        <div class="text-gray-700 min-w-0">
                            <p class="font-semibold text-2xl lg:text-3xl">14</p>
                            <p class="text-sm lg:text-base">Total Customers</p>
                        </div>
                    </div>
                </div>
                <div class="w-full cursor-pointer hover:shadow-md shadow rounded-lg">
                    <div class="w-full bg-white border text-indigo-600 rounded-lg flex items-center p-4 lg:p-6">
                        <svg class="w-12 h-12 lg:w-16 lg:h-16 fill-current mr-4 flex-shrink-0" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="6" width="18" height="12" rx="2" ry="2" stroke="currentColor" fill="none"/>
                            <path d="M6 10h12M6 14h12" stroke="currentColor"/>
                            <path d="M9 6V4h6v2M10 18v2h4v-2" stroke="currentColor"/>
                        </svg>
                        <div class="text-gray-700 min-w-0">
                            <p class="font-semibold text-2xl lg:text-3xl">15</p>
                            <p class="text-sm lg:text-base">Total Products</p>
                        </div>
                    </div>
                </div>
                <div class="w-full cursor-pointer hover:shadow-md shadow rounded-lg">
                    <div class="w-full bg-white border text-indigo-600 rounded-lg flex items-center p-4 lg:p-6">
                        <svg class="w-12 h-12 lg:w-16 lg:h-16 fill-current mr-4 flex-shrink-0" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M3 21v-13c0-1.1.9-2 2-2h5V4c0-1.1.9-2 2-2h6c1.1 0 2 .9 2 2v17h2v2H1v-2h2zm2 0h4v-3h2v3h4v-5H5v5zm10-10h2v2h-2v-2zm0-3h2v2h-2v-2zm0-3h2v2h-2v-2z"/>
                        </svg>
                        <div class="text-gray-700 min-w-0">
                            <p class="font-semibold text-2xl lg:text-3xl">15</p>
                            <p class="text-sm lg:text-base">Total Companies</p>
                        </div>
                    </div>
                </div>
                <div class="w-full cursor-pointer hover:shadow-md shadow rounded-lg">
                    <div class="w-full bg-white border text-indigo-600 rounded-lg flex items-center p-4 lg:p-6">
                        <svg class="w-12 h-12 lg:w-16 lg:h-16 fill-current mr-4 flex-shrink-0" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M6 2c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6H6zm7 7V3.5L18.5 9H13zM8 12h8v2H8v-2zm0 4h5v2H8v-2zm0-8h8v2H8V8z"/>
                        </svg>
                        <div class="text-gray-700 min-w-0">
                            <p class="font-semibold text-2xl lg:text-3xl">45</p>
                            <p class="text-sm lg:text-base">Total Quotations</p>
                        </div>
                    </div>
                </div>
                <div class="w-full cursor-pointer hover:shadow-md shadow rounded-lg">
                    <div class="w-full bg-white border text-indigo-600 rounded-lg flex items-center p-4 lg:p-6">
                        <svg class="w-12 h-12 lg:w-16 lg:h-14 fill-current mr-4 flex-shrink-0" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M3 3h2l1.6 6.6 1.4 5.4c.2.8.9 1.4 1.7 1.4h7c.8 0 1.5-.6 1.7-1.4l1.6-6.6H7.2l-.5-2H21V5H6.2L5.8 3H3zm5 16c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm10 0c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zM9 10h8v2H9v-2zm0 4h5v2H9v-2z"/>
                        </svg>
                        <div class="text-gray-700 min-w-0">
                            <p class="font-semibold text-2xl lg:text-3xl">15</p>
                            <p class="text-sm lg:text-base">Total Orders</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>
    .video-background {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 140%;
        object-fit: cover;
    }
</style>
