<script setup>
import { ref, onMounted, watch, computed, defineProps } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ActionLink from '@/Components/ActionLink.vue';
import CreateButton from '@/Components/CreateButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import Modal from '@/Components/Modal.vue';
import SwitchButton from '@/Components/SwitchButton.vue';
import Pagination from '@/Components/Pagination.vue';
import Dropdown from '@/Components/Dropdown.vue';
import { Head , useForm} from '@inertiajs/vue3';
import ArrowIcon from '@/Components/ArrowIcon.vue';
import { sortAndSearch } from '@/Composables/sortAndSearch.js';

const props = defineProps(['data', 'search', 'permissions']);
const { form, search, sort, fetchData, sortKey, sortDirection } = sortAndSearch('users.index');

const modalVisible = ref(false);
const selectedUserId = ref(null);

const columns = [
    { field: 'first_name',  label: 'NAME',      sortable: true, multiFieldSort: ['first_name', 'last_name'] },
    { field: 'email',       label: 'EMAIL',     sortable: true },
    { field: 'role_id',     label: 'ROLE',      sortable: true },
    { field: 'status',      label: 'STATUS',    sortable: false },
    { field: 'action',      label: 'ACTION',    sortable: false}
];

const openDeleteModal = (userId) => {
  selectedUserId.value = userId;
  modalVisible.value = true;
};

const closeModal = () => {
    modalVisible.value = false;
};

const deleteUser = () => {
    form.delete(route('users.destroy',{id:selectedUserId.value}), {
        onSuccess: () => closeModal()
    });
};

const handleSearchChange = (value) => {
    form.get(route('users.index', {search:value}),  {
        preserveState: true,
        // replace: true,
    });
};

const updateSwitchValue = (status,id) => {
    form.post(route('users.activation',{id:id, status:status}), {
    });
};

</script>

<template>
    <Head title="Users" />

    <AdminLayout>
        <div class="animate-top">
            <div class="flex justify-between items-center">
                <div class="items-start">
                    <h1 class="text-2xl font-semibold leading-7 text-gray-900">Users</h1>
                </div>
                <div class="flex justify-end">
                    <div class="flex space-x-6 mt-4 sm:mt-0 w-64">
                        <div class="flex focus-within:shadow-lg relative rounded-lg border border-gray-200 w-full">
                            <svg class="pointer-events-none absolute inset-y-0 left-0 h-full w-5 text-gray-400 ml-2" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd"></path></svg>
                                <input id="search-field" v-model="search" @input="fetchData" class="rounded-lg block h-full w-full border-0 pl-8 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm" placeholder="Search..." type="search" name="search">
                            </div>
                    </div>
                    <div class="mt-4 sm:ml-6 sm:mt-0 sm:flex-none" v-if="permissions.canCreateUser">
                        <div class="flex justify-end">
                            <CreateButton :href="route('users.create')">
                                    Add User
                            </CreateButton>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-8 overflow-x-auto sm:rounded-lg">
                <div class="shadow sm:rounded-lg">
                    <table class="w-full text-sm text-left rtl:text-right text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50" style="border-top: 3px solid white;">
                            <tr class="border-b-2">
                                <th v-for="(column, index) in columns" :key="index" scope="col" class="px-4 py-4 text-sm font-semi bold text-gray-900 cursor-pointer" @click="sort(column.field, column.sortable)">
                                    {{ column.label }}
                                    <ArrowIcon :isSorted="sortKey === column.field" :direction="sortDirection" v-if="column.sortable" />
                                </th>
                            </tr>
                        </thead>
                        <tbody v-if="data.data && (data.data.length > 0)">
                            <tr class="odd:bg-white even:bg-gray-50 border-b" v-for="(userData, index) in data.data" :key="userData.id">
                                <td class="px-4 py-2.5 font-medium text-gray-900 whitespace-nowrap min-w-36">
                                    {{ userData.first_name }} {{ userData.last_name }}
                                </td>
                                <td class="px-4 py-2.5 min-w-36">
                                    {{ userData.email }}
                                </td>
                                <td class="px-4 py-2.5 min-w-36">
                                {{ userData.roles[0].name }}
                                </td>
                                <td class="items-center px-4 py-2.5">
                                    <SwitchButton :switchValue="userData.status" :userId="userData.id" @updateSwitchValue="updateSwitchValue">
                                    </SwitchButton>
                                </td>
                                <td class="items-center px-4 py-2.5">
                                    <div class="flex items-center justify-start gap-4">
                                        <Dropdown align="right" width="48">
                                            <template #trigger>
                                                <button type="button" title="Open details" class="p-1 rounded hover:bg-gray-100 focus:bg-gray-100">
                                                    <svg viewBox="0 0 24 24" class="w-4 h-4 fill-current">
                                                        <path d="M12 6a2 2 0 110-4 2 2 0 010 4zm0 8a2 2 0 110-4 2 2 0 010 4zm-2 6a2 2 0 104 0 2 2 0 00-4 0z"></path>
                                                    </svg>
                                                </button>
                                            </template>
                                            <template #content>
                                                <ActionLink :href="route('users.edit',{id:userData.id})" v-if="permissions.canEditUser">
                                                    <template #svg>
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5" x-tooltip="tooltip">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125"
                                                                />
                                                        </svg>
                                                    </template>
                                                    <template #text>
                                                        <span class="text-sm text-gray-700 leading-5">
                                                            Edit
                                                        </span>
                                                    </template>
                                                </ActionLink>
                                                <button type="button" @click="openDeleteModal(userData.id)" class="flex space-x-2 block px-3 py-2 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 w-full" v-if="permissions.canDeleteUser">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5" x-tooltip="tooltip">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"
                                                        />
                                                    </svg>
                                                    <span class="text-sm text-gray-700 leading-5">
                                                        Delete
                                                    </span>
                                                </button>
                                            </template>
                                        </Dropdown>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                        <tbody v-else>
                            <tr class="bg-white">
                            <td colspan="9" class="text-center whitespace-nowrap p-10 text-sm font-semibold text-gray-900">
                                No data found.
                            </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <Pagination v-if="data.data && (data.data.length > 0)" class="mt-6" :links="data.links"></Pagination>
        </div>
        <Modal :show="modalVisible" @close="closeModal">
              <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">
                    Are you sure you want to delete?
                </h2>
                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeModal"> Cancel </SecondaryButton>
                    <DangerButton
                        class="ml-3"
                        @click="deleteUser"
                    >
                        Delete
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </AdminLayout>

</template>
