<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import {onMounted} from "vue";

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
    canRegister: {
        type: Boolean,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};

onMounted(async () => {
    localStorage.removeItem('permissions');
});

</script>



<template>
    <GuestLayout>
        <Head title="Log in" />
        <div class="flex justify-center items-center">
            <Link href="/" class="">
                <ApplicationLogo class="w-60 fill-current text-gray-500" />
            </Link>
        </div>
        <h2 class="text-center text-2xl font-semibold leading-9 tracking-tight text-gray-900 mt-2">Sign in to your account</h2>
        <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
            {{ status }}
        </div>
        <form @submit.prevent="submit">
            <div class="">
                <InputLabel for="email" value="Email" />

                <TextInput
                    id="email"
                    type="email"
                    class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 sm:text-sm sm:leading-6"
                    v-model="form.email"
                    required
                    autofocus
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
                    autocomplete="current-password"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-2">
                <div class="mb-4 flex justify-between items-center">
                    <label class="flex items-center">
                        <Checkbox name="remember" v-model:checked="form.remember" />
                        <span class="ml-2 text-center text-sm text-gray-500">Remember me</span>
                    </label>
                    <Link
                        :href="route('password.request')"
                        class="font-semibold text-indigo-600 text-sm hover:text-indigo-500"
                    >
                        Forgot your password?
                    </Link>

                </div>
            </div>
                <PrimaryButton class="" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Log in
                </PrimaryButton>
            <div class="mt-2 flex items-center space-x-1">
                <span class="text-sm text-gray-700">Not have an account ?</span>
                <Link
                :href="route('register')"
                class="font-semibold text-indigo-600 text-sm hover:text-indigo-500"
                >Register
                </Link>
            </div>
        </form>

    </GuestLayout>
</template>


