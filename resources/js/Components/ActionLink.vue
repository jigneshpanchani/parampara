<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    href: {
        type: String,
        required: true,
    }
});
</script>

<template>
    <Link
        :href="href"
        class="flex space-x-2 block px-3 py-2 text-gray-700 hover:bg-gray-100 focus:outline-none transition duration-150 ease-in-out w-full"
    >
    <slot name="svg" />
    <slot name="text" />
    </Link>
</template>


<style>
.hover\:bg-gray-100:hover{
    background: #e3e9f1bd !important;
}
</style>
