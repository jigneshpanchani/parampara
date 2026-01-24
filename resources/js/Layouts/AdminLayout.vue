<script setup>
import {ref, onMounted, onUnmounted} from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import SideMenu from '@/Components/SideMenu.vue';
import ToastNotificationVue from '@/Components/ToastNotification.vue'
import ToastNotificationSuccessVue from '@/Components/ToastNotificationSuccess.vue'
import ToastNotificationErrorVue from '@/Components/ToastNotificationError.vue'
import ToastNotificationWarningVue from '@/Components/ToastNotificationWarning.vue'
import ActionLink from '@/Components/ActionLink.vue';
import NotificationDropdown from '@/Components/NotificationDropdown.vue';
import {Link, useForm} from '@inertiajs/vue3';

const logoSrc = ref('/img/logo-svg.svg');
const form = useForm({});
const updateLogoSrc = (value) => {
    logoSrc.value = value;
};

// Mobile sidebar state
const sidebarOpen = ref(false);

const toggleSidebar = () => {
    sidebarOpen.value = !sidebarOpen.value;
};

const closeSidebar = () => {
    sidebarOpen.value = false;
};

// Close sidebar when clicking outside on mobile
const handleClickOutside = (event) => {
    const sidebar = document.getElementById('mobile-sidebar');
    const hamburger = document.getElementById('hamburger-button');

    if (sidebar && !sidebar.contains(event.target) && !hamburger.contains(event.target)) {
        closeSidebar();
    }
};

// Handle escape key
const handleEscape = (event) => {
    if (event.key === 'Escape') {
        closeSidebar();
    }
};

onMounted(async () => {
    try {
        const response = await fetch('/api/logo');
        if (response.ok) {
            const data = await response.json();
            if (data.logoUrl) {
                updateLogoSrc('/storage/' + data.logoUrl);
            } else {
                updateLogoSrc('/img/logo-svg.svg');
            }
        }
    } catch (error) {
        console.error('Error fetching logo:', error);
    }

    // Add event listeners for mobile sidebar
    document.addEventListener('click', handleClickOutside);
    document.addEventListener('keydown', handleEscape);
});

onUnmounted(() => {
    // Remove event listeners
    document.removeEventListener('click', handleClickOutside);
    document.removeEventListener('keydown', handleEscape);
});

const props = defineProps({
    notifications: {
        type: Array,
        default: () => []
    }
});

const hasPermission = (permission) => {
//    return this.$store.state.user.permissions.includes(permission);
};

</script>

<template>
    <div>
        <!-- Static sidebar for desktop -->
        <div class="hidden lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-72 lg:flex-col border-t border-r">
            <div class="bg-white flex h-16 px-6 items-center px-10 shrink-0 w-full mt-2">
                <img class="h-10" :src="logoSrc" alt="LOGO">
            </div>
            <!-- Sidebar component, swap this element with another sidebar if you like -->
            <div class="flex grow flex-col gap-y-2 overflow-y-auto bg-gray-900 mt-2">
                <nav class="flex flex-1 flex-col px-6">
                    <ul role="list" class="flex flex-1 flex-col gap-y-7">
                        <li>
                            <ul role="list" class="-mx-2 space-y-1">
                                <li>
                                    <SideMenu v-if="$can('Dashboard')" :href="route('admin.dashboard.index')"
                                              :active="route().current('admin.dashboard.*')">
                                        <template #svg>
                                            <svg class="h-6 w-6 shrink-0"
                                                 :class="route().current('admin.dashboard.*') ? 'text-white' : 'text-gray-700 group-hover:text-white'"
                                                 fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/>
                                            </svg>
                                        </template>
                                        <template #name>Dashboard</template>
                                    </SideMenu>
                                </li>
                                <li>
                                    <SideMenu v-if="$can('List Users')" :href="route('users.index')"
                                              :active="(route().current('users.index') || route().current('users.create') || route().current('users.edit') )">
                                        <template #svg>
                                            <svg class="h-6 w-6 shrink-0"
                                                 :class="(route().current('users.index') || route().current('users.create') || route().current('users.edit')) ? 'text-white' : 'text-gray-700 group-hover:text-white'"
                                                 fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                                            </svg>
                                        </template>
                                        <template #name>Users</template>
                                    </SideMenu>
                                </li>
                                <li>
                                    <SideMenu v-if="$can('Activity Log')" :href="route('logs')"
                                                :active="(route().current('logs'))">
                                        <template #svg>
                                            <svg class="h-6 w-6 shrink-0"
                                                :class="(route().current('logs')) ? 'text-white' : 'text-gray-700 group-hover:text-white'"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <circle cx="8" cy="8" r="4"/>
                                                <path d="M3 20v-2a6 6 0 0 1 12 0v2"/>
                                                <rect x="14" y="6" width="6" height="10" rx="1"/>
                                                <path d="M16 8h2"/>
                                                <path d="M16 10h2"/>
                                                <path d="M16 12h2"/>
                                                <path d="M14 17l2 2 4-4"/>
                                            </svg>
                                        </template>
                                        <template #name>User Activity</template>
                                    </SideMenu>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </nav>
            </div>
            <div class="mt-auto px-4 mt-1 space-y-2">
                <!-- DB Backup Button -->
                <Link :href="route('admin.db-backup.download')" class="w-full">
                    <div class="flex items-center gap-x-3 rounded-md px-3 py-2 text-sm font-semibold leading-6 text-gray-700 hover:bg-indigo-600 hover:text-white group transition-colors duration-200">
                        <svg class="h-6 w-6 shrink-0 text-gray-700 group-hover:text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33A3 3 0 0116.5 19.5H6.75z"/>
                        </svg>
                        <span>💾 DB Backup</span>
                    </div>
                </Link>

                <SideMenu v-if="$can('List Setting')" :href="route('setting')" :active="(route().current('setting') || route().current('roles.permission') ||
                        route().current('roles.index') || route().current('roles.create') || route().current('roles.edit'))">
                    <template #svg>
                        <svg class="h-6 w-6 shrink-0 text-gray-700" :class="(route().current('setting') || route().current('roles.permission') ||
                                    route().current('roles.index') || route().current('roles.create') || route().current('roles.edit')) ? 'text-white' : 'text-gray-500 group-hover:text-white'"
                                fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </template>
                    <template #name>Settings</template>
                </SideMenu>

                <!-- Logout Button -->
                <Link :href="route('logout')" method="post" as="button" class="w-full">
                    <div class="flex items-center gap-x-3 rounded-md px-3 py-2 text-sm font-semibold leading-6 text-gray-700 hover:bg-gray-100 hover:text-gray-900 group transition-colors duration-200">
                        <svg class="h-6 w-6 shrink-0 text-gray-700 group-hover:text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 9V5.25A2.25 2.25 0 0110.5 3h6a2.25 2.25 0 012.25 2.25v13.5A2.25 2.25 0 0116.5 21h-6a2.25 2.25 0 01-2.25-2.25V15m-3 0l3-3m0 0l3 3m-3-3v12"/>
                        </svg>
                        <span>Logout</span>
                    </div>
                </Link>
            </div>
        </div>

        <!-- Mobile sidebar overlay -->
        <div
            v-show="sidebarOpen"
            class="relative z-50 lg:hidden"
            role="dialog"
            aria-modal="true"
        >
            <!-- Background backdrop -->
            <div
                class="fixed inset-0 bg-gray-900/80 transition-opacity duration-300"
                @click="closeSidebar"
            ></div>

            <!-- Sidebar panel -->
            <div class="fixed inset-0 flex">
                <div
                    id="mobile-sidebar"
                    class="relative mr-16 flex w-full max-w-xs flex-1 transform transition-transform duration-300 ease-in-out"
                    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
                >
                    <!-- Close button -->
                    <div class="absolute left-full top-0 flex w-16 justify-center pt-5">
                        <button
                            type="button"
                            class="-m-2.5 p-2.5 text-white hover:text-gray-300 transition-colors duration-200"
                            @click="closeSidebar"
                        >
                            <span class="sr-only">Close sidebar</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Mobile sidebar content -->
                    <div class="flex grow flex-col gap-y-5 bg-white px-6 pb-4 border-r">
                        <!-- Logo -->
                        <div class="flex h-16 shrink-0 items-center">
                            <img class="h-10 w-auto" :src="visionlogo" alt="LOGO">
                        </div>

                        <!-- Navigation -->
                        <nav class="flex flex-1 flex-col overflow-y-auto">
                            <ul role="list" class="flex flex-1 flex-col gap-y-7">
                                <li>
                                    <ul role="list" class="space-y-1">
                                        <li>
                                            <SideMenu v-if="$can('Dashboard')" :href="route('admin.dashboard.index')"
                                                    :active="route().current('admin.dashboard.*')">
                                                <template #svg>
                                                    <svg class="h-6 w-6 shrink-0"
                                                        :class="route().current('admin.dashboard.*') ? 'text-white' : 'text-gray-700 group-hover:text-white'"
                                                        fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/>
                                                    </svg>
                                                </template>
                                                <template #name>Dashboard</template>
                                            </SideMenu>
                                        </li>
                                        <li>
                                            <SideMenu v-if="$can('List Users')" :href="route('users.index')"
                                                    :active="(route().current('users.index') || route().current('users.create') || route().current('users.edit') )">
                                                <template #svg>
                                                    <svg class="h-6 w-6 shrink-0"
                                                        :class="(route().current('users.index') || route().current('users.create') || route().current('users.edit')) ? 'text-white' : 'text-gray-700 group-hover:text-white'"
                                                        fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                                                    </svg>
                                                </template>
                                                <template #name>Users</template>
                                            </SideMenu>
                                        </li>
                                        <li>
                                            <SideMenu v-if="$can('Activity Log')" :href="route('logs')"
                                                        :active="(route().current('logs'))">
                                                <template #svg>
                                                    <svg class="h-6 w-6 shrink-0"
                                                        :class="(route().current('logs')) ? 'text-white' : 'text-gray-700 group-hover:text-white'"
                                                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <circle cx="8" cy="8" r="4"/>
                                                        <path d="M3 20v-2a6 6 0 0 1 12 0v2"/>
                                                        <rect x="14" y="6" width="6" height="10" rx="1"/>
                                                        <path d="M16 8h2"/>
                                                        <path d="M16 10h2"/>
                                                        <path d="M16 12h2"/>
                                                        <path d="M14 17l2 2 4-4"/>
                                                    </svg>
                                                </template>
                                                <template #name>User Activity</template>
                                            </SideMenu>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </nav>

                        <!-- Mobile Logout Button -->
                        <div class="border-t pt-4 mt-auto">
                            <Link :href="route('logout')" method="post" as="button" class="w-full">
                                <div class="flex items-center gap-x-3 rounded-md px-3 py-2 text-sm font-semibold leading-6 text-gray-700 hover:bg-gray-100 hover:text-gray-900 group transition-colors duration-200">
                                    <svg class="h-6 w-6 shrink-0 text-gray-700 group-hover:text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 9V5.25A2.25 2.25 0 0110.5 3h6a2.25 2.25 0 012.25 2.25v13.5A2.25 2.25 0 0116.5 21h-6a2.25 2.25 0 01-2.25-2.25V15m-3 0l3-3m0 0l3 3m-3-3v12"/>
                                    </svg>
                                    <span>Logout</span>
                                </div>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:pl-72 border-t">
            <div class="sticky top-0 z-20 flex h-16 shrink-0 items-center gap-x-4 bg-white px-4 shadow sm:gap-x-6 sm:px-6 lg:px-8">
                <button
                    id="hamburger-button"
                    type="button"
                    class="-m-2.5 p-2.5 text-gray-700 lg:hidden hover:text-gray-900 transition-colors duration-200"
                    @click="toggleSidebar"
                >
                    <span class="sr-only">Open sidebar</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
                    </svg>
                </button>

                <!-- Separator -->
                <div class="h-6 w-px bg-gray-200 lg:hidden" aria-hidden="true"></div>

                <div class="flex flex-1 gap-x-4 self-stretch lg:gap-x-6 justify-between">
                    <div class="flex items-center">
                        <Dropdown align="left" width="48">
                            <template #trigger>
                                <div class="flex w-32">
                                    <a class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600" href="#"> Add New +</a>
                                </div>
                            </template>
                            <template #content>
                                <ActionLink :href="route('users.create')">
                                    <template #svg></template>
                                    <template #text>
                                        <span class="text-sm text-gray-700 leading-6">Add New User</span>
                                    </template>
                                </ActionLink>
                            </template>
                        </Dropdown>
                    </div>
                    <div class="flex items-center gap-x-4 lg:gap-x-6">
                        <NotificationDropdown :notifications="notifications" />

                        <!-- Separator -->
                        <div class="hidden lg:block lg:h-6 lg:w-px lg:bg-gray-200" aria-hidden="true"></div>

                        <!-- Profile dropdown -->
                        <div class="relative">
                            <Dropdown align="right" width="48">
                                <template #trigger>
                                    <button type="button" class="-m-1.5 flex items-center p-1.5" id="user-menu-button"
                                            aria-expanded="false" aria-haspopup="true">
                                        <span class="sr-only">Open User Menu</span>
                                        <img class="h-8 w-8 rounded-full bg-gray-50"
                                             src="https://img.freepik.com/premium-photo/avatar-resourcing-company_1254967-6696.jpg?size=626&ext=jpg&ga=GA1.1.259439899.1729255085&semt=ais_hybrid"
                                             alt="">
                                        <span class="hidden lg:flex lg:items-center">
                                <span class="ml-4 text-sm font-semibold leading-6 text-gray-900" aria-hidden="true">{{ $page.props.auth.user.name }}</span>
                                <svg class="ml-2 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/>
                                </svg>
                            </span>
                                    </button>
                                </template>
                                <template #content>
                                    <ActionLink :href="route('profile.edit')" as="button">
                                        <template #svg></template>
                                        <template #text>
                                            <span class="text-sm text-gray-700 leading-5">Your Profile</span>
                                        </template>
                                    </ActionLink>
                                    <ActionLink :href="route('logout')" method="post" as="button">
                                        <template #svg></template>
                                        <template #text>
                                            <span class="text-sm text-gray-700 leading-5">Sign Out</span>
                                        </template>
                                    </ActionLink>
                                </template>
                            </Dropdown>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Info flash message -->
            <div v-if="$page.props.flash.message">
                <ToastNotificationVue :message="$page.props.flash.message"/>
            </div>

            <!-- Success flash message -->
            <div v-if="$page.props.flash.success">
                <ToastNotificationSuccessVue :message="$page.props.flash.success"/>
            </div>

            <!-- Error flash message -->
            {{ $page.props.flash.error }}
            <div v-if="$page.props.flash.error">
                <ToastNotificationErrorVue :message="$page.props.flash.error"/>
            </div>

            <!-- Warning flash message -->
            <div v-if="$page.props.flash.warning">
                <ToastNotificationWarningVue :message="$page.props.flash.warning"/>
            </div>

            <main class="py-4 sm:py-6 lg:py-10 bg-gray-100">
                <div class="px-2 sm:px-4 lg:px-8 min-h-screen">
                    <slot/>
                </div>
            </main>
        </div>
    </div>
</template>

<style>

::-webkit-scrollbar {
    display: none;
}

html {
    scrollbar-width: none;
}

.bg-gray-900 {
    background: #ffffff !important;
}

.hover\:bg-indigo-500:hover {
    background: rgb(62 81 228) !important;
}

.bg-indigo-600 {
    background: rgb(50 68 210) !important;
}

.bg-gray-100 {
    background: #e3e9f1bd !important;
}
.bg-gray-50 , tr:nth-child(even){
    background: rgb(249 250 260) !important;
}
.animate-top{
    position:relative;
    animation:animatetop 0.6s
}
@keyframes animatetop
    {from{top:-100px;opacity:0} to{top:0;opacity:1}}

/* Mobile responsive improvements */
@media (max-width: 1023px) {
    /* Ensure mobile sidebar is above everything */
    .mobile-sidebar-overlay {
        z-index: 9999;
    }

    /* Adjust header spacing on mobile */
    .mobile-header {
        padding-left: 1rem;
        padding-right: 1rem;
    }

    /* Improve touch targets on mobile */
    button {
        min-height: 44px;
        min-width: 44px;
    }
}

@media (max-width: 640px) {
    /* Extra small screens */
    .mobile-content {
        padding-left: 0.5rem;
        padding-right: 0.5rem;
    }

    /* Smaller text on mobile */
    .mobile-text {
        font-size: 0.875rem;
    }
}

/* Smooth transitions for responsive elements */
.responsive-transition {
    transition: all 0.3s ease-in-out;
}

/* Mobile-first approach for tables and cards */
@media (max-width: 768px) {
    .responsive-table {
        display: block;
        overflow-x: auto;
        white-space: nowrap;
    }

    .responsive-card {
        margin-bottom: 1rem;
        border-radius: 0.5rem;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
    }
}

</style>
