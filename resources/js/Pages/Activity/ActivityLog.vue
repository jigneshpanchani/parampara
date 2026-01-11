<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
    import axios from 'axios';

import { sortAndSearch } from '@/Composables/sortAndSearch.js';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import SearchableDropdown from '@/Components/SearchableDropdownNew.vue';
import SimpleDropdown from '@/Components/SimpleDropdown.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps(['activityLogs', 'user', 'userId', 'logNames']);

const form = useForm({});

const searchValue = ref('');
const userId = ref(props.userId);
const userName = ref('ALL Users');
const from_date = ref('');
const to_date = ref('');
const logName = ref('');

const sentinel = ref(null);
const logs = ref([...props.activityLogs]);
const hasMore = ref(true);
const loading = ref(false);

watch(
  () => props.activityLogs,
  (newLogs) => {
    logs.value = [...newLogs];
  }
);

const loadMore = async () => {
  if (!hasMore.value || loading.value) return;
  loading.value = true;
  const offset = logs.value.length;
  const params = {
    offset: offset,
    search: searchValue.value,
    causer_id: userId.value,
    from_date: from_date.value,
    to_date: to_date.value,
    log_name: logName.value,  // consistently use logName
  };
  try {
    const response = await axios.get(route('logs.loadMore'), { params });
    const newLogs = response.data.activityLogs;
    logs.value = logs.value.concat(newLogs);
    hasMore.value = response.data.hasMore;
  } catch (error) {
    console.error("Error loading more logs", error);
  } finally {
    loading.value = false;
  }
};



let observer = null;
onMounted(() => {
  observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        loadMore();
      }
    });
  });
  if (sentinel.value) observer.observe(sentinel.value);
});
onUnmounted(() => {
  if (observer && sentinel.value) observer.unobserve(sentinel.value);
});

// Filtering functions:
const handleSearchChange = (value, userId, from_date, to_date, logName) => {
  searchValue.value = value;
  form.get(route('logs', {
    search: value,
    causer_id: userId,
    from_date: from_date,
    to_date: to_date,
    log_name: logName
  }), { preserveState: true });
};

const handleStartDate = () => {
  handleSearchChange(searchValue.value, userId.value, from_date.value, to_date.value, logName.value);
};

const handleToDate = () => {
  handleSearchChange(searchValue.value, userId.value, from_date.value, to_date.value, logName.value);
};

const setUser = (id, name) => {
  userId.value = id;
  userName.value = name;
  handleSearchChange(searchValue.value, userId.value, from_date.value, to_date.value, logName.value);
};

const handleLogNameChange = (value) => {
  logName.value = value;
  handleSearchChange(searchValue.value, userId.value, from_date.value, to_date.value, logName.value);
};

const formatDate = (dateString) => {
  const date = new Date(dateString);
  const options = { year: 'numeric', month: 'short', day: 'numeric' };
  return date.toLocaleDateString('en-US', options);
};

const colorMap = {
  created: { color: 'text-green-500', svgColor: '#22C55E' },
  updated: { color: 'text-blue-500', svgColor: '#3B82F6' },
  deleted: { color: 'text-red-500', svgColor: '#EF4444' },
  received: { color: 'text-yellow-500', svgColor: '#F59E0B' },
  payment: { color: 'text-purple-500', svgColor: '#8B5CF6' },
  default: { color: 'text-gray-500', svgColor: '#9CA3AF' },
};

// IMPORTANT: Compute groupedLogs from the reactive `logs` variable, not props.activityLogs
const groupedLogs = computed(() => {
  return logs.value.reduce((acc, log) => {
    const date = formatDate(log.created_at);
    if (!acc[date]) {
      acc[date] = [];
    }
    acc[date].push(log);
    return acc;
  }, {});
});

const generateLogProperties = (log) => {
  const properties = log.properties || {};
  const eventType = log.event || "";
  const eventColor = colorMap[eventType]?.color || colorMap.default.color;

  const hasData = Object.keys(properties).some((key) => {
    return properties[key] && Object.keys(properties[key]).length > 0;
  });

  if (!hasData) return "";

  let descriptionHtml = "";

  // Handle Deleted Event (Show all old values)
  if (eventType === "deleted" && properties.old) {
    descriptionHtml += '<div class="space-y-1">';
    Object.keys(properties.old).forEach((key) => {
      const oldValue = properties.old[key] ?? "NA";
      const deletedValueClass = `bg-red-100 ${eventColor} px-3 py-1 rounded text-sm`;

      descriptionHtml += `
        <div class="flex justify-between items-center">
          <div class="${eventColor} font-medium text-sm">${key}</div>
          <span class="${deletedValueClass}">${oldValue}</span>
        </div>
      `;
    });
    descriptionHtml += "</div>";
    return descriptionHtml;
  }

  if (properties.attributes) {
    descriptionHtml += '<div class="space-y-1">';
    Object.keys(properties.attributes).forEach((key) => {
      const oldValue = properties.old?.[key] ?? null;
      const newValue = properties.attributes[key] ?? "NA";

      const oldValueDisplay =
        eventType === "updated" && oldValue === null ? "NA" : oldValue;

      const oldValueClass = "bg-red-100 text-red-600 px-3 py-1 rounded text-sm";
      const newValueClass = "bg-green-100 text-green-600 px-3 py-1 rounded text-sm";

      descriptionHtml += `
        <div class="flex justify-between items-center">
          <div class="${eventColor} font-medium text-sm">${key}</div>
          <div class="flex items-center gap-2">
            ${
              eventType === "updated"
                ? `<span class="${oldValueClass}">${oldValueDisplay}</span>
                   <span class="text-gray-500 text-sm">→</span>`
                : ""
            }
            <span class="${newValueClass}">${newValue}</span>
          </div>
        </div>
      `;
    });
    descriptionHtml += "</div>";
  }

  Object.keys(properties).forEach((key) => {
    if (key !== "attributes" && key !== "old") {
      if (descriptionHtml === "") {
        descriptionHtml += '<div class="space-y-1">';
      }

      let value = properties[key];
      if (typeof value === "string") {
        value = value.replace(/^"|"$/g, "");
      }

      descriptionHtml += `
        <div class="flex justify-between items-center">
          <div class="${eventColor} font-medium text-sm">${key}</div>
          <div class="bg-gray-100 text-gray-800 px-3 py-1 rounded text-sm">${value}</div>
        </div>
      `;
    }
  });

  return descriptionHtml ? `<div class="space-y-1">${descriptionHtml}</div>` : "";
};

const isModalOpen = ref(false);
const selectedLog = ref(null);

const closeModal = () => {
  isModalOpen.value = false;
  selectedLog.value = null;
};

const toggleDetails = (id) => {
  selectedLog.value = logs.value.find(log => log.id === id);
  isModalOpen.value = true;
};

const getInitials = (firstName = '', lastName = '') => {
  const firstInitial = firstName.charAt(0).toUpperCase() || '';
  const lastInitial = lastName.charAt(0).toUpperCase() || '';
  return `${firstInitial}${lastInitial}` || 'U';
};

// --- Go to Top Button Logic ---
const showScrollButton = ref(false);

const scrollToTop = () => {
  window.scrollTo({ top: 0, behavior: 'smooth' });
};

const checkScroll = () => {
  // Show button when scrolled down 300px or more
  showScrollButton.value = window.pageYOffset > 300;
};

onMounted(() => {
  window.addEventListener('scroll', checkScroll);
});
onUnmounted(() => {
  window.removeEventListener('scroll', checkScroll);
});
</script>

<template>
  <Head title="Activity Logs" />
  <AdminLayout>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">User Activity</h1>
        <div class="ml-6 flex space-x-6 mt-4 sm:mt-0 w-64">
          <div class="flex focus-within:shadow-lg relative rounded-lg border border-gray-200 w-full">
            <svg class="pointer-events-none absolute inset-y-0 left-0 h-full w-5 text-gray-400 ml-2" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
              <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd"></path>
            </svg>
            <input id="search-field"  @input="handleSearchChange($event.target.value, userId, from_date, to_date, logName)" class="rounded-lg block h-full w-full border-0 pl-8 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm" placeholder="Search..." type="search" name="search">
          </div>
        </div>
      </div>
    <div class="mt-4 p-6 bg-white shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
      <div class="flex mb-2">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3 6h18M4 10h16M5 14h14M6 18h12" />
        </svg>
        <InputLabel for="customer_id" value="Filters" />
      </div>
      <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-12 items-center">
        <div class="sm:col-span-3">
          <InputLabel for="date" value="From Date" />
          <input
            v-model="from_date"
            class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
            type="date"
            @change="handleStartDate"
          />
        </div>
        <div class="sm:col-span-3">
          <InputLabel for="date" value="To Date" />
          <input
            v-model="to_date"
            class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
            type="date"
            @change="handleToDate"
          />
        </div>
        <div class="sm:col-span-3">
          <InputLabel for="log_name" value="Task" />
          <div class="relative mt-2">
            <SearchableDropdown
            :options="[ { id: '', name: 'All Task' }, ...logNames.map(log => ({ id: log, name: log })) ]"
            v-model="logName"
            @onchange="handleLogNameChange"
            />
          </div>
        </div>
        <div class="sm:col-span-3">
          <InputLabel for="customer_id" value="Users" />
          <div class="relative mt-2">
            <SearchableDropdown
              :options="user"
              v-model="userId"
              @onchange="setUser"
            />
          </div>
        </div>

      </div>
    </div>
    <div class="mt-6 w-full">
        <div v-if="Object.keys(groupedLogs).length === 0" class="text-center whitespace-nowrap p-10 text-sm font-semibold text-gray-900">
          No activity logs found.
        </div>
        <div v-for="(group, date) in groupedLogs" :key="date" class="mb-8">
          <h2 class="text-lg font-bold text-gray-800 mb-4">{{ date }}</h2>
          <div v-for="log in group" :key="log.id" class="flex items-start mb-4">
            <div class="relative px-4 py-4 bg-white rounded-lg shadow-md w-full">
              <button   v-if="log.log_name !== 'Number Setting Update'" @click="toggleDetails(log.id)" class="absolute top-1/2 right-0 transform -translate-y-1/2 px-6 text-gray-500 hover:text-gray-700 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-6 w-6" x-tooltip="tooltip">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                    </svg>
              </button>
              <div class="flex items-start space-x-4 items-center">
                    <div class="w-10 h-10">
                        <svg
                            v-if="log.event === 'sent'"
                            xmlns="http://www.w3.org/2000/svg"
                            width="40"
                            height="40"
                            viewBox="0 0 48 48"
                            fill="none"
                            >
                            <circle cx="24" cy="24" r="22" fill="url(#grad1)" stroke="#28c62c" stroke-width="3" />
                            <g transform="translate(10, 12)" stroke="#FFFFFF" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="2" y="4" width="24" height="16" rx="3" ry="3" fill="none" />
                                <path d="M3 5l10 10 12-10" />
                            </g>
                            <defs>
                                <linearGradient id="grad1" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" style="stop-color:#3ADF5B; stop-opacity:1" />
                                    <stop offset="100%" style="stop-color:#28c62c; stop-opacity:1" />
                                </linearGradient>
                            </defs>
                        </svg>
                        <svg v-else width="40" height="40" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="20" cy="20" r="20" :fill="colorMap[log.event]?.svgColor || '#3B82F6'" />
                            <text
                            x="50%"
                            y="50%"
                            dominant-baseline="middle"
                            text-anchor="middle"
                            fill="#FFF"
                            font-size="14"
                            font-weight="bold"
                            font-family="Arial, sans-serif"
                            >
                            {{ getInitials(log.causer?.first_name, log.causer?.last_name) }}
                            </text>
                        </svg>
                    </div>
                <div class="flex flex-col justify-between w-full">
                  <div class="flex items-start">
                    <p class="text-gray-700">
                      <span class="" v-if="log.description" v-html="log.description"></span>
                      <span v-if="log.log_name !== 'PaymentPaid' && log.log_name !== 'Payment-Receive' && log.log_name !== 'Send-Invoice-Email'" class="text-md font-semibold ml-1">
                        {{ log.causer?.first_name || 'Unknown' }} {{ log.causer?.last_name || '' }}
                      </span>
                    </p>
                  </div>
                  <div class="text-sm text-gray-500">
                    {{ new Date(log.created_at).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' }) }}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="flex justify-center my-4">
          <div v-if="loading" class="loader"></div>
        </div>
        <div ref="sentinel" class="h-1"></div>
      </div>

    <button
        v-if="showScrollButton"
        @click="scrollToTop"
        class="fixed bottom-5 right-5 bg-blue-600 hover:bg-blue-700 text-white p-3 rounded-full shadow-lg"
    >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
        </svg>
    </button>
    <Modal :show="isModalOpen" @close="closeModal">
      <div class="p-6">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Details</h2>
        <p :class="colorMap[selectedLog?.event]?.color || 'text-gray-700'" class="text-md mb-4 font-semibold">
          {{ selectedLog?.event }} details:
        </p>
        <div class="overflow-y-auto p-4 mb-4 border rounded-lg shadow-sm bg-white" style="max-height:340px;" v-html="generateLogProperties(selectedLog)"></div>
        <div class="mt-4 flex justify-end">
          <SecondaryButton @click="closeModal">Close</SecondaryButton>
        </div>
      </div>
    </Modal>
  </AdminLayout>
</template>

<style scoped>
.loader {
  border: 4px solid rgba(0, 0, 0, 0.1);
  width: 36px;
  height: 36px;
  border-radius: 50%;
  border-left-color: #3B82F6;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}
</style>
