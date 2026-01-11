<template>
    <div class="relative">
        <button 
            @click="toggleNotifications" 
            type="button" 
            class="-m-2.5 p-2.5 text-gray-400 hover:text-gray-500 relative"
        >
            <span class="sr-only">View Notifications</span>
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                 stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
            </svg>
            <!-- Notification badge -->
            <span 
                v-if="unreadCount > 0" 
                class="absolute -top-1 -right-1 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform bg-red-600 rounded-full"
            >
                {{ unreadCount > 99 ? '99+' : unreadCount }}
            </span>
        </button>

        <!-- Notification dropdown -->
        <div 
            v-if="showNotifications" 
            class="absolute right-0 mt-2 w-80 bg-white border border-gray-200 rounded-lg shadow-lg z-50"
        >
            <div class="p-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Notifications</h3>
            </div>
            
            <div class="max-h-96 overflow-y-auto">
                <div v-if="notifications.length === 0" class="p-4 text-center text-gray-500">
                    No notifications found
                </div>
                
                <div v-else>
                    <div 
                        v-for="notification in notifications" 
                        :key="notification.id"
                        class="p-4 border-b border-gray-100 hover:bg-gray-50 cursor-pointer"
                        :class="{ 'bg-blue-50': !notification.read_at }"
                        @click="markAsRead(notification.id)"
                    >
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                <div class="w-2 h-2 bg-blue-600 rounded-full" v-if="!notification.read_at"></div>
                                <div class="w-2 h-2 bg-gray-300 rounded-full" v-else></div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-gray-900">
                                    {{ notification.data.message || 'New notification' }}
                                </p>
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ formatTime(notification.created_at) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div v-if="notifications.length > 0" class="p-4 border-t border-gray-200">
                <button 
                    @click="markAllAsRead"
                    class="w-full text-center text-sm text-blue-600 hover:text-blue-800 font-medium"
                >
                    Mark all as read
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import axios from 'axios'; // ✅ Import axios

const props = defineProps({
    notifications: {
        type: Array,
        default: () => []
    }
});

const showNotifications = ref(false);

const unreadCount = computed(() => {
    return props.notifications.filter(notification => !notification.read_at).length;
});

const toggleNotifications = () => {
    showNotifications.value = !showNotifications.value;
};

const markAsRead = async (notificationId) => {
    try {
        await axios.post(`/notifications/${notificationId}/mark-as-read`);
        const notification = props.notifications.find(n => n.id === notificationId);
        if (notification) notification.read_at = new Date().toISOString(); // ✅ Update locally
    } catch (error) {
        console.error('Failed to mark notification as read:', error);
    }
};

const markAllAsRead = async () => {
    try {
        await axios.post('/notifications/mark-all-as-read');
        props.notifications.forEach(n => {
            n.read_at = new Date().toISOString(); // ✅ Update all locally
        });
        showNotifications.value = false;
    } catch (error) {
        console.error('Failed to mark all as read:', error);
    }
};

const formatTime = (timestamp) => {
    const date = new Date(timestamp);
    const now = new Date();
    const diffInSeconds = Math.floor((now - date) / 1000);

    if (diffInSeconds < 60) {
        return 'Just now';
    } else if (diffInSeconds < 3600) {
        const minutes = Math.floor(diffInSeconds / 60);
        return `${minutes} minute${minutes > 1 ? 's' : ''} ago`;
    } else if (diffInSeconds < 86400) {
        const hours = Math.floor(diffInSeconds / 3600);
        return `${hours} hour${hours > 1 ? 's' : ''} ago`;
    } else {
        const days = Math.floor(diffInSeconds / 86400);
        return `${days} day${days > 1 ? 's' : ''} ago`;
    }
};

const handleClickOutside = (event) => {
    if (!event.target.closest('.relative')) {
        showNotifications.value = false;
    }
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});
</script>

