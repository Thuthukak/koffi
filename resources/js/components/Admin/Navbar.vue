<template>
    <nav class="bg-white shadow p-4 flex justify-between items-center dark:bg-gray-900">
      <!-- Sidebar Toggle -->
      <button @click="$emit('toggleSidebar')" class="text-gray-700 dark:text-white text-2xl">
        <font-awesome-icon :icon="icons.faBars" />
      </button>
  
      <!-- Right: Profile & Actions -->
      <div class="flex items-center space-x-4 relative">
        <!-- Dark Mode Toggle -->
        <button @click="toggleDarkMode" class="text-gray-600 dark:text-white">
          <font-awesome-icon :icon="darkMode ? icons.faSun : icons.faMoon" />
        </button>
  
        <!-- Language Switcher -->
        <button @click="changeLocale" class="text-gray-600 dark:text-white">
          <font-awesome-icon :icon="icons.faGlobe" />
        </button>
  
        <!-- Profile Section -->
        <div class="relative">
          <button @click="toggleDropdown" class="flex items-center space-x-2 focus:outline-none">
            <img :src="user.avatar" alt="Profile" class="w-10 h-10 rounded-full border">
            <div class="hidden sm:block">
              <p class="text-sm font-semibold dark:text-white">{{ user.name }}</p>
              <p class="text-xs text-gray-500 dark:text-gray-400">{{ user.role }}</p>
            </div>
          </button>
  
          <!-- Dropdown Menu -->
          <div
            v-if="dropdownOpen"
            class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 shadow-lg rounded-lg py-2"
          >
            <button @click="goToProfile" class="dropdown-item">
              <font-awesome-icon :icon="icons.faUser" class="mr-2" /> Profile
            </button>
            <button @click="viewNotifications" class="dropdown-item">
              <font-awesome-icon :icon="icons.faBell" class="mr-2" /> Notifications
            </button>
            <button @click="goToSettings" class="dropdown-item">
              <font-awesome-icon :icon="icons.faCog" class="mr-2" /> Settings
            </button>
            <button @click="goHome" class="dropdown-item">
              <font-awesome-icon :icon="icons.faHome" class="mr-2" /> Home
            </button>
            <hr class="border-gray-300 dark:border-gray-600" />
            <button @click="logout" class="dropdown-item text-red-500">
              <font-awesome-icon :icon="icons.faSignOutAlt" class="mr-2" /> Logout
            </button>
          </div>
        </div>
      </div>
    </nav>
  </template>
  
  <script>
  import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
  import { faBars, faMoon, faSun, faGlobe, faUser, faBell, faCog, faHome, faSignOutAlt } from "@fortawesome/free-solid-svg-icons";
  
  export default {
    name: "Navbar",
    components: { FontAwesomeIcon },
    data() {
      return {
        darkMode: false,
        dropdownOpen: false,
        user: {
          name: "John Doe",
          role: "Admin",
          avatar: "https://i.pravatar.cc/100", // Placeholder image
        },
        icons: { faBars, faMoon, faSun, faGlobe, faUser, faBell, faCog, faHome, faSignOutAlt },
      };
    },
    methods: {
      toggleDarkMode() {
        this.darkMode = !this.darkMode;
        document.documentElement.classList.toggle("dark", this.darkMode);
      },
      changeLocale() {
        console.log("Change language");
      },
      toggleDropdown() {
        this.dropdownOpen = !this.dropdownOpen;
      },
      goToProfile() {
        console.log("Go to Profile");
      },
      viewNotifications() {
        console.log("View Notifications");
      },
      goToSettings() {
        console.log("Go to Settings");
      },
      goHome() {
        console.log("Go to Home");
      },
      logout() {
        console.log("Logging out...");
      },
    },
  };
  </script>
  
  <style scoped>
  /* Dropdown Styling */
  .dropdown-item {
    display: flex;
    align-items: center;
    width: 100%;
    padding: 10px;
    text-align: left;
    font-size: 14px;
    color: #333;
    transition: background 0.2s;
  }
  
  .dropdown-item:hover {
    background-color: #f1f1f1;
  }
  
  .dark .dropdown-item {
    color: #fff;
  }
  
  .dark .dropdown-item:hover {
    background-color: #2d3748;
  }
  </style>
  