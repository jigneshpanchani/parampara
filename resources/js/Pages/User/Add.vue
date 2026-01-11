<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import TextArea from '@/Components/TextArea.vue';
import SearchableDropdown from '@/Components/SearchableDropdown.vue';
import SvgLink from '@/Components/ActionLink.vue';
import { Head , Link, usePage } from '@inertiajs/vue3';

import { useForm } from 'laravel-precognition-vue-inertia';

const form = useForm('post', '/users', {
    role_id:'',
    first_name: '',
    last_name: '',
    email: '',
    contact_no: '',
    dob: '',
    address: '',
    password: '',
});

const submit = () => form.submit({
 preserveScroll: true,
 onSuccess: () => form.reset(),
});

defineProps({
    roles: {
        type: Array,
    },
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
            <h2 class="text-2xl font-semibold leading-7 text-gray-900">Add New User</h2>
            <form @submit.prevent="submit" class="">
                <div class="border-b border-gray-900/10 pb-12">
                    <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-6">
                        <div class="sm:col-span-2">
                            <InputLabel for="first_name" value="First Name" />
                            <TextInput
                                id="first_name"
                                type="text"
                                v-model="form.first_name"
                                autocomplete="first_name"
                                @change="form.validate('first_name')"
                            />
                            <InputError  v-if="form.invalid('first_name')" class="" :message="form.errors.first_name" />
                        </div>
                        <div class="sm:col-span-2">
                            <InputLabel for="last_name" value="Last Name" />
                            <TextInput
                                id="last_name"
                                type="text"
                                v-model="form.last_name"
                                autocomplete="last_name"
                                @change="form.validate('last_name')"
                            />
                            <InputError  v-if="form.invalid('last_name')" class="" :message="form.errors.last_name" />
                        </div>
                        <div class="sm:col-span-2">
                            <InputLabel for="dob" value="Date of Birth" />
                            <!-- Add the datepicker component -->
                            <!-- <DatePicker v-model="form.dob"
                             @change="handleDateChange"
                             type="date"
                             /> -->
                            <input
                                class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                               type="date"  v-model="form.dob"   @change="form.validate('dob')"
                            />
                            <InputError v-if="form.invalid('dob')" class="" :message="form.errors.dob" />
                        </div>
                        <div class="sm:col-span-3">
                            <InputLabel for="email" value="Email" />
                            <TextInput
                                id="email"
                                type="email"
                                v-model="form.email"
                                autocomplete="email"
                                @change="form.validate('email')"
                            />

                            <InputError v-if="form.invalid('email')" class="" :message="form.errors.email" />
                        </div>
                        <div class="sm:col-span-3">
                            <InputLabel for="password" value="Password" />

                            <TextInput
                                id="password"
                                type="password"
                                v-model="form.password"
                                autocomplete="password"
                                @change="form.validate('password')"
                            />

                            <InputError v-if="form.invalid('password')" class="" :message="form.errors.password" />
                        </div>
                        <div class="sm:col-span-3">
                            <InputLabel for="contact_no" value="Contact No" />
                            <TextInput
                                id="contact_no"
                                type="text"
                                :numeric="true"
                                maxLength="10"
                                v-model="form.contact_no"
                                @change="form.validate('contact_no')"
                            />
                            <InputError  v-if="form.invalid('contact_no')" class="" :message="form.errors.contact_no" />
                        </div>

                        <div class="sm:col-span-3">
                            <InputLabel for="role_id" value="Role" />
                            <div class="relative mt-2">
                                <SearchableDropdown :options="roles"
                                v-model="form.role_id"
                                @onchange="setRole"
                                :class="{ 'error rounded-md': form.errors.company_id }"
                                />
                            </div>
                            <InputError v-if="form.invalid('role_id')" class="" :message="form.errors.role_id" />
                        </div>
                        <div class="sm:col-span-6">
                            <InputLabel for="address" value="Address" />
                            <TextArea
                                id="address"
                                type="text"
                                :rows="4"
                                v-model="form.address"
                                autocomplete="address"
                                @change="form.validate('address')"
                            />
                            <InputError  v-if="form.invalid('address')" class="" :message="form.errors.address" />
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

                    <PrimaryButton :disabled="form.processing">Save</PrimaryButton>



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
