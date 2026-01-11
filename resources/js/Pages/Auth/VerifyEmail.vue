<script setup>
import { computed } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    status: {
        type: String,
    },
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(() => props.status === 'verification-link-sent');
</script>

<template>
    <GuestLayout>
        <Head title="Email Verification" />
        <h2 class="text-center text-2xl font-bold leading-9 tracking-tight text-gray-900">Email Verification</h2>
        <div class="mb-4 mt-2 text-sm text-gray-500">
            Thanks for signing up! Before getting started, could you verify your email address by clicking on the link
            we just emailed to you? If you didn't receive the email, we will gladly send you another.
        </div>

        <div class="mb-4 font-medium text-sm text-green-500" v-if="verificationLinkSent">
            A new verification link has been sent to the email address you provided during registration.
        </div>

        <form @submit.prevent="submit">
            <div class="mt-4">
                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Resend Verification Email
                </PrimaryButton>
            </div>
            <div class="text-center items-center mt-4">
                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="font-semibold text-indigo-600 text-sm hover:text-indigo-500"
                    >Log Out</Link
                >
            </div>
        </form>
    </GuestLayout>
</template>
