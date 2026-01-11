<script setup>
import { ref, onMounted } from 'vue';
import { Link , useForm} from '@inertiajs/vue3';

const logoSrc = ref('/img/logo-svg.svg');
const form = useForm({});
const updateLogoSrc = (value) => {
    logoSrc.value = value;
};

onMounted(async () => {
    try {
        const response = await fetch('/api/logo');
        if (response.ok) {
            const data = await response.json();
            if (data.logoUrl) {
                updateLogoSrc('/storage/'+data.logoUrl);
            } else {
                updateLogoSrc('/img/logo-svg.svg');
            }
        }
    } catch (error) {
        console.error('Error fetching logo:', error);
    }
});

</script>


<template>
   <img :src="logoSrc" alt="LOGO">
</template>
