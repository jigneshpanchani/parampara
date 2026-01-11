<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import SvgLink from '@/Components/ActionLink.vue';
import {Head, Link, usePage} from '@inertiajs/vue3';
import Checkbox from '@/Components/Checkbox.vue';
import { ref, computed, watch  } from 'vue';
import {useForm} from 'laravel-precognition-vue-inertia';

const form = useForm('post', '/roles', {
    name: '',
    permissions: [],
});

const submit = () => form.submit({
    preserveScroll: true,
    onSuccess: () => form.reset(),
});

const showDetails = ref({});

const toggleDetails = (name) => {
    showDetails.value[name] = !showDetails.value[name];
};

const updatePermissions = (isChecked, roleId) => {
    if (isChecked) {
        form.permissions.push(roleId); // Add role name to permissions array
    } else {
        const index = form.permissions.indexOf(roleId);
        if (index !== -1) {
            form.permissions.splice(index, 1); // Remove role name from permissions array
        }
    }
    updateParentCheckbox(roleId);
};

const props = defineProps(['data']);

const checkAll = (event, roles) => {
    const isChecked = event.target.checked;
    roles.forEach(role => {
        if (isChecked && !form.permissions.includes(role.id)) {
            form.permissions.push(role.id);
        } else if (!isChecked && form.permissions.includes(role.id)) {
            const index = form.permissions.indexOf(role.id);
            if (index > -1) {
                form.permissions.splice(index, 1);
            }
        }
    });
};

const allChecked = computed(() => {
    const result = {};
    Object.keys(props.data).forEach(module => {
        const allSelected = props.data[module].every(role => form.permissions.includes(role.id));
        result[module] = allSelected;
    });
    return result;
});

const updateParentCheckbox = (roleId) => {
    for (const module in props.data) {
        const allSelected = props.data[module].every(role => form.permissions.includes(role.id));
        allChecked.value[module] = allSelected;
    }
};

watch(form.permissions, (newVal, oldVal) => {
    for (const module in props.data) {
        updateParentCheckbox(module);
    }
}, { deep: true });

</script>

<template>
    <Head title="Role & Permission"/>
    <AdminLayout>
        <div class="animate-top bg-white p-4 shadow sm:p-6 sm:rounded-lg border">
            <h2 class="text-2xl font-semibold leading-7 text-gray-900">Add New Role & Permission </h2>
            <form @submit.prevent="submit" class="">
                <div class="border-b border-gray-900/10 pb-12">
                    <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-12">
                        <div class="sm:col-span-12 grid grid-cols-6 gap-6">
                            <div class="col-span-2">
                                <InputLabel for="name" value="Role Name"/>
                                <TextInput
                                    id="name"
                                    type="text"
                                    v-model="form.name"
                                    @change="form.validate('name')"
                                />
                                <InputError v-if="form.invalid('name')" :message="form.errors.name"/>
                            </div>
                        </div>
                        <div class="sm:col-span-12">
                            <InputLabel for="name" value="Select Permission"/>
                        </div>
                        <div class="sm:col-span-3" v-for="(roles, name) in data" :key="name">
                            <div class="flex justify-between items-center border px-4 py-2 bg-gray-50 rounded-lg">
                                <h3 class="flex items-center text-lg font-semibold leading-7 text-gray-900 space-x-2">
                                    <Checkbox :checked="allChecked[name]" @change="checkAll($event, roles)" />
                                    <span>{{ name }}</span>
                                </h3>
                                <div class="cursor-pointer">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-5 w-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="border border-t-0 rounded-b-lg">
                                <div v-for="(role, index) in roles" :key="index" class="flex justify-between items-center px-4 py-1 border-b last:border-b-0">
                                    <div class="text-sm font-semibold leading-6 text-gray-900 p-1">
                                        {{ role.name }}
                                    </div>
                                    <div class="flex justify-end p-1">
                                        <Checkbox
                                            :checked="form.permissions.includes(role.id)"
                                            @update:checked="(isChecked) => updatePermissions(isChecked, role.id)"
                                            name="permissions"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex mt-6 items-center justify-between">
                    <div class="ml-auto flex items-center justify-end gap-x-6">
                        <SvgLink :href="route('roles.index')">
                            <template #svg>
                                <button type="button" class="text-sm font-semibold leading-6 text-gray-900">Cancel
                                </button>
                            </template>
                        </SvgLink>
                        <PrimaryButton :disabled="form.processing">Save</PrimaryButton>
                        <Transition enter-active-class="transition ease-in-out"
                                    enter-from-class="opacity-0"
                                    leave-active-class="transition ease-in-out"
                                    leave-to-class="opacity-0">
                            <p v-if="form.recentlySuccessful" class="text-sm text-gray-600">Saved.</p>
                        </Transition>
                    </div>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
