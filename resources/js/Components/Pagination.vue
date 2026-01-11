<script setup>
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';
const props = defineProps(['links']);

</script>


<template>

  <div v-if="links.length > 1">
    <div class="flex flex-wrap justify-end isolate rounded-md">
      <template v-for="(link, p) in links" :key="p">
        <div v-if="link.url === null">
            <Link v-html="link.label" :href="'#'" class="inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 bg-white hover:bg-gray-50 focus:z-20 focus:outline-offset-0">
            </Link>
        </div>
          <div v-else>
            <Link v-html="link.label" :href="link.url" :class="{ 'bg-indigo-600 text-white hover:bg-indigo-600': link.active }" class="bg-white inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0">
            </Link>
          </div>
      </template>
    </div>
  </div>
</template>


