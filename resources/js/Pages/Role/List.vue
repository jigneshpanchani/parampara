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

const props = defineProps(['roles', 'permissions']);
const form = useForm({});

const modalVisible = ref(false);
const selectedRoleId = ref(null);

const openDeleteModal = (roleId) => {
  selectedRoleId.value = roleId;
  modalVisible.value = true;
};

const closeModal = () => {
    modalVisible.value = false;
};

const deleteRole = () => {
    form.delete(route('roles.destroy',{id:selectedRoleId.value}), {
        onSuccess: () => closeModal()
    });
};

</script>

<template>
    <Head title="Role-Permission" />

    <AdminLayout>
        <div class="animate-top">
        <div class="flex justify-between items-center">
            <div class="items-start">
                <h1 class="text-2xl font-semibold leading-7 text-gray-900">Roles & Permissions</h1>
            </div>
            <div class="flex space-x-6 mt-4 sm:ml-10 sm:mt-0 sm:flex-none">
               <div class="flex justify-end w-20">
                    <CreateButton :href="route('setting')">
                        Back
                    </CreateButton>
                </div>
                <div class="flex justify-end" v-if="permissions.canCreateRoles">
                    <CreateButton :href="route('roles.create')">
                        Add Role
                    </CreateButton>
                </div>
            </div>
        </div>
        <div class="mt-8 overflow-x-auto sm:rounded-lg">
            <div class="shadow sm:rounded-lg">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50" style="border-top: 3px solid white;">
                        <tr class="border-b-2">
                            <th scope="col" class="px-4 py-4 text-sm font-semi bold text-gray-900 cursor-pointer">ROLE NAME</th>
                            <th scope="col" class="px-4 py-4 text-sm font-semi bold text-gray-900 cursor-pointer"></th>
                            <th scope="col" class="px-4 py-4 text-sm font-semi bold text-gray-900 cursor-pointer"></th>
                            <th scope="col" class="px-4 py-4 text-sm font-semi bold text-gray-900 cursor-pointer"></th>
                            <th scope="col" class="px-4 py-4 text-sm font-semi bold text-gray-900 cursor-pointer"></th>
                            <th scope="col" class="px-4 py-4 text-sm font-semi bold text-gray-900 cursor-pointer"></th>
                            <th scope="col" class="px-4 py-4 text-sm font-semi bold text-gray-900 cursor-pointer"></th>
                            <th scope="col" class="px-4 py-4 text-sm font-semi bold text-gray-900 cursor-pointer">ACTION</th>
                        </tr>
                    </thead>
                    <tbody v-if="props.roles && (props.roles.length > 0)">
                        <tr class="odd:bg-white even:bg-gray-50 border-b" v-for="(roleData, index) in props.roles" :key="roleData.id">
                            <td class="px-4 py-2.5 font-medium text-gray-900 whitespace-nowrap text-gray-900">{{ roleData.name }}</td>
                            <td class="px-4 py-2.5 font-medium text-gray-900 whitespace-nowrap text-gray-900"></td>
                            <td class="px-4 py-2.5 font-medium text-gray-900 whitespace-nowrap text-gray-900"></td>
                            <td class="px-4 py-2.5 font-medium text-gray-900 whitespace-nowrap text-gray-900"></td>
                            <td class="px-4 py-2.5 font-medium text-gray-900 whitespace-nowrap text-gray-900"></td>
                            <td class="px-4 py-2.5 font-medium text-gray-900 whitespace-nowrap text-gray-900"></td>
                            <td class="px-4 py-2.5 font-medium text-gray-900 whitespace-nowrap text-gray-900"></td>
                            <td class="items-center px-4 py-2.5">
                                <div class="flex items-center justify-start gap-4">
                                    <Dropdown :align="'right'" width="48">
                                        <template #trigger>
                                            <button type="button" title="Open details" class="p-1 rounded dark:text-gray-600 hover:bg-gray-100 focus:bg-gray-100">
                                                <svg viewBox="0 0 24 24" class="w-4 h-4 fill-current">
                                                    <path d="M12 6a2 2 0 110-4 2 2 0 010 4zm0 8a2 2 0 110-4 2 2 0 010 4zm-2 6a2 2 0 104 0 2 2 0 00-4 0z"></path>
                                                </svg>
                                            </button>
                                        </template>
                                        <template #content>
                                            <ActionLink v-if="permissions.canEditRoles" :href="route('roles.edit',{id:roleData.id})">
                                                <template #svg>
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5" x-tooltip="tooltip">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125"/>
                                                    </svg>
                                                </template>
                                                <template #text>
                                                    <span class="text-sm text-gray-700 leading-5">Edit</span>
                                                </template>
                                            </ActionLink>
                                            <button type="button" v-if="permissions.canDeleteRoles" @click="openDeleteModal(roleData.id)" class="flex space-x-2 block px-3 py-2 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 w-full">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5" x-tooltip="tooltip">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                                                </svg>
                                                <span class="text-sm text-gray-700 leading-5">Delete</span>
                                            </button>
                                        </template>
                                    </Dropdown>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                    <tbody v-else>
                    <tr class="bg-white">
                        <td colspan="3" class="text-center whitespace-nowrap p-10 text-sm font-semibold text-gray-900">
                            No data found.
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        </div>
        <Modal :show="modalVisible" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">
                    Are you sure you want to delete this Role & Permissions?
                </h2>
                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeModal"> Cancel</SecondaryButton>
                    <DangerButton class="ml-3" @click="deleteRole">
                        Delete
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </AdminLayout>

</template>
