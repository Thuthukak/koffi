<template>
    <aside class="w-64 bg-gray-900 text-white p-5 h-screen flex flex-col">
      <!-- Logo Section -->
      <div class="flex items-center gap-3 mb-6">
        <div class="bg-indigo-500 text-white w-10 h-10 flex items-center justify-center rounded-lg">
          <span class="text-lg font-bold">DS</span>
        </div>
        <h1 class="text-lg font-semibold">DesignSeal</h1>
      </div>
  
      <!-- Navigation linkss -->
      <ul class="flex-grow">
        <li class="mb-4">
          <a href="/admin/dashboard" class="flex items-center gap-3 p-3 rounded-lg hover:bg-indigo-600">
            <font-awesome-icon :icon="['fas', 'home']" />
            <span>Dashboard</span>
          </a>
        </li>
        <li class="mb-4">
          <a href="/admin/onboarding" class="flex items-center gap-3 p-3 rounded-lg hover:bg-indigo-600">
            <font-awesome-icon :icon="['fas', 'folder-open']" />
            <span>Bookings</span>
          </a>
        </li>
        <li class="mb-4">
          <a href="/admin/notifications" class="flex items-center gap-3 p-3 rounded-lg hover:bg-indigo-600">
            <font-awesome-icon :icon="['fas', 'bell']" />
            <span>Projects</span>
          </a>
        </li>
        <li class="mb-4">
          <a href="/admin/analytics" class="flex items-center gap-3 p-3 rounded-lg hover:bg-indigo-600">
            <font-awesome-icon :icon="['fas', 'chart-pie']" />
            <span>Invoices</span>
          </a>
        </li>
        <li class="mb-4">
          <a href="/admin/likes" class="flex items-center gap-3 p-3 rounded-lg hover:bg-indigo-600">
            <font-awesome-icon :icon="['fas', 'heart']" />
            <span>Settings</span>
          </a>
        </li>
        <li class="mb-4">
          <a href="/admin/wallets" class="flex items-center gap-3 p-3 rounded-lg hover:bg-indigo-600">
            <font-awesome-icon :icon="['fas', 'wallet']" />
            <span>Subscription</span>
          </a>
        </li>
      </ul>
  
      <!-- Logout & Settings -->
      <div>
        <a href="/logout" class="flex items-center gap-3 p-3 rounded-lg hover:bg-red-600">
          <font-awesome-icon :icon="['fas', 'sign-out-alt']" />
          <span>Logout</span>
        </a>
      </div>
    </aside>
  </template>
  
  <script>
  import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
  
  
  export default {
    name: "Sidebar",
    components: {
      FontAwesomeIcon,
      
    },
  };
  </script>
  
  <style scoped>
  /* Ensure sidebar styles match the reference */
  aside {
    min-width: 260px;
  }
  </style>
  