<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: {
        type: String,
        required: true,
    },
    active: {
        type: Boolean,
    },
});

const classes = computed(() =>



    props.active
        ? 'block bg-indigo-600 text-white group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold transition duration-150 ease-in-out'
        : 'block text-gray-700 hover:text-white hover:bg-indigo-600 group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold transition duration-150 ease-in-out'
);
</script>

<template>
    <Link :href="href" :class="classes">
        <slot name="svg" />
        <slot name="name" />
    </Link>
</template>

<style>

.bg-indigo-600 {
    background: rgb(50 68 210) !important;
}

.hover\:bg-indigo-600:hover {
    background: rgb(50 68 210) !important;
}

</style>
