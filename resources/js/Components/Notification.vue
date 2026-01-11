<template>
    <div class="relative">
        <button @click="toggleNotifications" class="relative">
            <svg class="w-6 h-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
            </svg>
            <span v-if="unreadNotificationsCount > 0" class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-red-100 transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full">{{ unreadNotificationsCount }}</span>
        </button>
        <div v-if="showNotifications" class="absolute right-0 mt-2 w-64 bg-white border border-gray-200 rounded-lg shadow-lg">
            <div v-if="notifications.length === 0" class="p-4 text-sm text-gray-600">No new notifications</div>
            <div v-else class="divide-y divide-gray-200">
                <div v-for="notification in notifications" :key="notification.id" class="p-4">
                    {{ notification.message }}
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            showNotifications: false,
            notifications: [],
        };
    },
    computed: {
        unreadNotificationsCount() {
            return this.notifications.length;
        }
    },
    methods: {
        toggleNotifications() {
            this.showNotifications = !this.showNotifications;
            if (this.showNotifications) {
                // Fetch notifications when opening the notifications dropdown
                this.fetchNotifications();
            }
        },
        fetchNotifications() {
            // Make an AJAX request to fetch notifications
            // Example using Axios:
            axios.get('/notifications')
                .then(response => {
                    this.notifications = response.data;
                })
                .catch(error => {
                    console.error('Error fetching notifications:', error);
                });
        }
    }
};
</script>
