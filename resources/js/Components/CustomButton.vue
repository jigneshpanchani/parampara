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

</script>

<template>
    <Link :href="href" class="flex justify-between items-center border border-gray-300 px-4 py-6 bg-white rounded-lg shadow-sm hover:shadow hover:border-gray-300">
        <h3 class="text-lg font-semibold leading-7 text-gray-900">
            <slot />
        </h3>
    </Link>
</template>
