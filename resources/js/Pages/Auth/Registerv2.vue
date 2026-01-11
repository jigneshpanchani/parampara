<script setup>

import { Head, Link, useForm } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen loginview flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
        <div>
            <Link href="/">
                <ApplicationLogo class="w-60 fill-current text-gray-500" />
            </Link>
        </div>
        <Head title="Register" />

        <div class="container">
            <h2>Access Denied</h2>
            <p>You do not have permission to access this page/event. </p>
            <p>Please contact your administrator if you believe this is an error.</p>
            <h4><a :href="route('login')">Return to Login</a></h4>
        </div>
        </div>
</template>

<style scoped>
    .loginview {
        /* background: url('/uploads/companyprofile/bg-image.webp') no-repeat center center fixed;
        background-size: cover; */
    }
    .container {
        max-width: 600px;
        margin: 0 auto;
        background-color: #fff;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    .container p {
        margin: 4px 0px;
    }
    h2 {
        font-size: 32px;
        font-weight: 600;
        margin: 0;
        color: #e74c3c;
    }
    a {
        text-decoration: none;
        color: rgb(79 70 229);
        font-weight: bold;
    }
    a:hover {
        text-decoration: underline;
    }
</style>

