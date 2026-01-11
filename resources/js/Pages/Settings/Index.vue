<script setup>
import { ref, computed } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import CustomButton from '@/Components/CustomButton.vue';
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({});
const searchQuery = ref('');
const props = defineProps(['permissions']);

</script>

<template>
    <Head title="Settings" />

    <AdminLayout>
        <div class="animate-top">
            <div class="sm:flex sm:items-center">
                <div class="sm:flex-auto">
                    <h1 class="text-2xl font-semibold leading-7 text-gray-900">Settings</h1>
                </div>
                <div class="flex space-x-6 mt-4 sm:mt-0 w-64">
                    <div class="flex focus-within:shadow-lg relative rounded-lg border border-gray-200 w-full">
                        <svg
                            class="pointer-events-none absolute inset-y-0 left-0 h-full w-5 text-gray-400 ml-2"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                            aria-hidden="true"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z"
                                clip-rule="evenodd"
                            ></path>
                        </svg>
                        <input
                            id="search-field"
                            v-model="searchQuery"
                            class="rounded-lg block h-full w-full border-0 pl-8 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm"
                            placeholder="Search..."
                            type="search"
                            name="search"
                        />
                    </div>
                </div>
            </div>
            <div class="border-gray-900 mt-10" style="height: 500px;">
                <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-6">
                    <div class="sm:col-span-2" v-if="permissions.canPermissionsAdd">
                        <CustomButton :href="route('roles.index')">
                            <svg class="w-12 h-12 fill-current text-blue-600 inline-block" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 2l7 4v6c0 5-3 9-7 10-4-1-7-5-7-10V6l7-4z"/>
                                <circle cx="12" cy="10" r="3" fill="white"/>
                                <path d="M9 16c1-2 5-2 6 0" stroke="white" stroke-width="2" fill="none"/>
                            </svg>
                            <span class="font-semibold text-lg ml-4">Roles & Permissions</span>
                        </CustomButton>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
