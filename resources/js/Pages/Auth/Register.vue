<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    first_name: '',
    last_name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role_id: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};

defineProps({
    roles: {
        type: Array,
    },
});
</script>

<template>
    <GuestLayout>
        <Head title="Register" />
        <h2 class="text-center text-2xl font-bold leading-9 tracking-tight text-gray-900">Register here</h2>
        <form @submit.prevent="submit">
            <div class="mt-2 flex space-x-2">
                <div class="w-1/2">
                    <InputLabel for="first_name" value="First Name" />
                    <TextInput
                        id="first_name"
                        type="text"
                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 sm:text-sm sm:leading-6"
                        v-model="form.first_name"
                        required
                        autofocus
                        autocomplete="first_name"
                    />
                    <InputError class="mt-2" :message="form.errors.first_name" />
                </div>
                <div class="w-1/2">
                    <InputLabel for="last_name" value="Last Name" />
                    <TextInput
                        id="last_name"
                        type="text"
                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 sm:text-sm sm:leading-6"
                        v-model="form.last_name"
                        required
                        autofocus
                        autocomplete="last_name"
                    />
                    <InputError class="mt-2" :message="form.errors.last_name" />
                </div>
            </div>
            <div class="mt-2">
                <InputLabel for="email" value="Email" />

                <TextInput
                    id="email"
                    type="email"
                    class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 sm:text-sm sm:leading-6"
                    v-model="form.email"
                    required
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-2">
                <InputLabel for="password" value="Password" />

                <TextInput
                    id="password"
                    type="password"
                    class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 sm:text-sm sm:leading-6"
                    v-model="form.password"
                    required
                    autocomplete="new-password"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-2">
                <InputLabel for="password_confirmation" value="Confirm Password" />

                <TextInput
                    id="password_confirmation"
                    type="password"
                    class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 sm:text-sm sm:leading-6"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                />

                <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>

            <div class="mt-2">
                <InputLabel for="role_id" value="Role" />
                    <select v-model="form.role_id"
                        class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                        <option v-for="role in roles" :value="role.id" :key="role.id">{{ role.name }}</option>
                    </select>
                <InputError class="mt-2" :message="form.errors.role_id" />
            </div>

            <div class="mt-4">
                <PrimaryButton class="" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Register
                </PrimaryButton>
            </div>
            <div class="mt-2 flex items-center space-x-1">
                <span class="text-sm text-gray-700">Already have an account ?</span>
                <Link
                :href="route('login')"
                class="font-semibold text-indigo-600 text-sm hover:text-indigo-500"
                >Login
                </Link>
            </div>
        </form>
    </GuestLayout>
</template>
