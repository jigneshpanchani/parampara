<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';

import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen loginview bg-slate-100 flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
        <!-- <div>
            <Link href="/">
                <ApplicationLogo class="w-60 fill-current text-gray-500" />
            </Link>
        </div> -->

        <div
            class="w-full sm:max-w-md mt-6 p-6 bg-white shadow overflow-hidden sm:rounded-lg border-gray-300"
        >
            <slot />
        </div>
    </div>
</template>

<style scoped>
.loginview {
    /* background: url('/uploads/companyprofile/bg-image.webp') no-repeat center center fixed !important;
    background-size: cover!important; */
}
</style>
