<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import TextArea from '@/Components/TextArea.vue';
import SvgLink from '@/Components/ActionLink.vue';
import SearchableDropdown from '@/Components/SearchableDropdown.vue';
import { Head , Link, useForm, usePage } from '@inertiajs/vue3';


const userData = usePage().props.data;

defineProps({
    data: {
        type: Object,
    },
    roles: {
        type: Array,
    },
});

const form = useForm({
    first_name: userData.first_name,
    last_name: userData.last_name,
    email: userData.email,
    role_id: userData.role_id,
    contact_no: userData.contact_no,
    dob: userData.dob,
    address: userData.address,
    id: userData.id
});

const setRole = (id, name) => {
    form.role_id = id;
};

</script>

<template>
    <Head title="Users" />

    <AdminLayout>
        <div class="animate-top">
        <div class="bg-white p-4 shadow sm:p-6 sm:rounded-lg border">
            <h2 class="text-2xl font-semibold leading-7 text-gray-900">Edit User</h2>
            <form @submit.prevent="form.patch(route('users.update'))">
                <div class="border-b border-gray-900/10 pb-12">
                    <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-6">
                        <div class="sm:col-span-2">
                            <InputLabel for="first_name" value="First Name" />
                            <TextInput
                                id="first_name"
                                type="text"
                                v-model="form.first_name"
                                autocomplete="first_name"
                            />
                            <InputError class="" :message="form.errors.first_name" />
                        </div>
                        <div class="sm:col-span-2">
                            <InputLabel for="last_name" value="Last Name" />
                            <TextInput
                                id="last_name"
                                type="text"
                                v-model="form.last_name"
                                required
                                autocomplete="last_name"
                            />
                            <InputError class="" :message="form.errors.last_name" />
                        </div>
                        <div class="sm:col-span-2">
                            <InputLabel for="dob" value="Date of Birth" />
                            <input
                                class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                               type="date" v-model="form.dob"
                            />
                            <InputError class="" :message="form.errors.dob" />
                        </div>
                        <div class="sm:col-span-2">
                            <InputLabel for="email" value="Email" />
                            <TextInput
                                id="email"
                                type="email"
                                v-model="form.email"
                                required
                                autocomplete="username"
                            />
                            <InputError class="" :message="form.errors.email" />
                        </div>
                        <div class="sm:col-span-2">
                            <InputLabel for="contact_no" value="Contact No" />
                            <TextInput
                                id="contact_no"
                                type="text"
                                :numeric="true"
                                maxLength="10"
                                v-model="form.contact_no"
                            />
                            <InputError class="" :message="form.errors.contact_no" />
                        </div>
                        <div class="sm:col-span-2">
                            <InputLabel for="role_id" value="Role" />
                            <div class="relative mt-2">
                                <SearchableDropdown :options="roles"
                                v-model="form.role_id"
                                @onchange="setRole"
                                 required
                                />
                            </div>
                            <InputError class="" :message="form.errors.role_id" />
                        </div>
                        <div class="sm:col-span-6">
                            <InputLabel for="address" value="Address" />
                            <TextArea
                                id="address"
                                :rows="4"
                                type="text"
                                v-model="form.address"
                                autocomplete="address"
                            />
                            <InputError class="" :message="form.errors.address" />
                        </div>
                    </div>
                </div>
                <div class="flex mt-6 items-center justify-between">

                    <div class="ml-auto flex items-center justify-end gap-x-6">
                    <SvgLink :href="route('users.index')">
                          <template #svg>
                             <button type="button" class="text-sm font-semibold leading-6 text-gray-900">Cancel</button>
                          </template>
                    </SvgLink>

                    <PrimaryButton :disabled="form.processing">Update</PrimaryButton>



                    <Transition
                        enter-active-class="transition ease-in-out"
                        enter-from-class="opacity-0"
                        leave-active-class="transition ease-in-out"
                        leave-to-class="opacity-0"
                    >
                        <p v-if="form.recentlySuccessful" class="text-sm text-gray-600">Saved.</p>
                    </Transition>
                    </div>
                </div>
            </form>
        </div>
        </div>
    </AdminLayout>
</template>
