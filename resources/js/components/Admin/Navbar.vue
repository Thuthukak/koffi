<template>
  <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm px-3 py-2 relative z-40">
    <div class="container-fluid d-flex justify-content-between align-items-center">
      <!-- Left Section: Sidebar Toggle (Desktop only) -->
      <div class="d-flex align-items-center">
        <button 
          v-if="!isMobile"
          @click="$emit('toggleSidebar')" 
          class="btn text-secondary me-3"
        >
          <font-awesome-icon :icon="icons.faBars" />
        </button>
        
        <!-- Mobile Logo -->
        <div v-if="isMobile" class="d-flex align-items-center">
          <div class="bg-primary text-white d-flex align-items-center justify-content-center rounded-lg me-2" style="width: 32px; height: 32px;">
            <span class="fw-bold small">KFI</span>
          </div>
          <h5 class="m-0">KOFI</h5>
        </div>
      </div>

      <!-- Right Section: Dark Mode, Language, Profile -->
      <div class="d-flex align-items-center gap-2 gap-md-3">
        <!-- Dark Mode Toggle -->
        <button @click="toggleDarkMode" class="btn text-secondary p-2">
          <font-awesome-icon :icon="darkMode ? icons.faSun : icons.faMoon" />
        </button>

        <!-- Language Switcher -->
        <button @click="changeLocale" class="btn text-secondary p-2">
          <font-awesome-icon :icon="icons.faGlobe" />
        </button>

        <!-- Profile Dropdown -->
        <div class="position-relative">
          <button 
            class="btn d-flex align-items-center p-1"
            @click="toggleDropdown"
            :disabled="loadingUser"
          >
            <div class="position-relative">
              <img 
                :src="user.avatar || defaultAvatar" 
                alt="Profile" 
                class="rounded-circle me-2" 
                width="35" 
                height="35"
                @error="handleImageError"
              />
              <div 
                v-if="loadingUser" 
                class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center bg-light rounded-circle"
                style="opacity: 0.8;"
              >
                <div class="spinner-border spinner-border-sm" role="status">
                  <span class="visually-hidden">Loading...</span>
                </div>
              </div>
            </div>
            <span class="d-none d-sm-inline text-dark">
              {{ loadingUser ? 'Loading...' : (user.name || 'User') }}
            </span>
            <font-awesome-icon :icon="icons.faChevronDown" class="ms-2 text-muted small" />
          </button>

          <!-- Dropdown Menu -->
          <ul 
            v-if="dropdownOpen" 
            class="dropdown-menu show position-absolute end-0 mt-2 shadow rounded border-0"
            style="min-width: 200px;"
          >
            <li class="px-3 py-2 border-bottom">
              <div class="d-flex align-items-center">
                <img 
                  :src="user.avatar || defaultAvatar" 
                  alt="Profile" 
                  class="rounded-circle me-2" 
                  width="40" 
                  height="40"
                  @error="handleImageError"
                />
                <div>
                  <div class="fw-semibold">{{ user.name || 'User' }}</div>
                  <small class="text-muted">{{ user.email || 'admin@kofi.com' }}</small>
                </div>
              </div>
            </li>
            <li>
              <button class="dropdown-item d-flex align-items-center py-2" @click="goToProfile">
                <font-awesome-icon :icon="icons.faUser" class="me-2 text-muted" /> 
                Profile
              </button>
            </li>
            <li>
              <button class="dropdown-item d-flex align-items-center py-2" @click="viewNotifications">
                <font-awesome-icon :icon="icons.faBell" class="me-2 text-muted" /> 
                Notifications
                <span v-if="notifications.count > 0" class="badge bg-danger rounded-pill ms-auto small">
                  {{ notifications.count }}
                </span>
              </button>
            </li>
            <li>
              <button class="dropdown-item d-flex align-items-center py-2" @click="goToSettings">
                <font-awesome-icon :icon="icons.faCog" class="me-2 text-muted" /> 
                Settings
              </button>
            </li>
            <li>
              <button class="dropdown-item d-flex align-items-center py-2" @click="goHome">
                <font-awesome-icon :icon="icons.faHome" class="me-2 text-muted" /> 
                Home
              </button>
            </li>
            <li><hr class="dropdown-divider my-1"></li>
            <li>
              <button class="dropdown-item d-flex align-items-center py-2 text-danger" @click="logout">
                <font-awesome-icon :icon="icons.faSignOutAlt" class="me-2" /> 
                Logout
              </button>
            </li>
          </ul>
        </div>
      </div>
    </div>

    <!-- Click outside to close dropdown -->
    <div 
      v-if="dropdownOpen" 
      class="position-fixed top-0 start-0 w-100 h-100"
      style="z-index: -1;"
      @click="dropdownOpen = false"
    ></div>
  </nav>
</template>

<script>
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { 
  faBars, 
  faMoon, 
  faSun, 
  faGlobe, 
  faUser, 
  faBell, 
  faCog, 
  faHome, 
  faSignOutAlt,
  faChevronDown 
} from "@fortawesome/free-solid-svg-icons";
import axios from "axios";


export default {
  name: "Navbar",
  components: { FontAwesomeIcon },
  props: {
    sidebarCollapsed: {
      type: Boolean,
      default: false,
    },
    isMobile: {
      type: Boolean,
      default: false,
    },
  },
  emits: ['toggleSidebar'],
  data() {
    return {
      darkMode: false,
      dropdownOpen: false,
      loadingUser: true,
      user: {
        name: "",
        email: "",
        avatar: "",
      },
      notifications: {
        count: 0,
      },
      defaultAvatar: "https://ui-avatars.com/api/?name=User&background=6366f1&color=fff&size=100",
      icons: { 
        faBars, 
        faMoon, 
        faSun, 
        faGlobe, 
        faUser, 
        faBell, 
        faCog, 
        faHome, 
        faSignOutAlt,
        faChevronDown 
      },
    };
  },
  async mounted() {
    await this.fetchUserData();
    this.checkDarkMode();
  },
  methods: {
    async fetchUserData() {
      try {
        this.loadingUser = true;
        const response = await axios.get('/api/user/data');
        
        if (response.data && response.data.user) {
          this.user = {
            name: response.data.user.name || 'User',
            email: response.data.user.email || '',
            avatar: response.data.user.avatar || response.data.user.profile_photo_url || '',
          };
          
          // Update default avatar with user's name
          if (!this.user.avatar && this.user.name) {
            this.defaultAvatar = `https://ui-avatars.com/api/?name=${encodeURIComponent(this.user.name)}&background=6366f1&color=fff&size=100`;
          }
        }
      } catch (error) {
        console.error('Failed to fetch user data:', error);
        // Set fallback data
        this.user = {
          name: 'Admin',
          email: 'admin@kofi.com',
          avatar: '',
        };
      } finally {
        this.loadingUser = false;
      }
    },
    
    handleImageError(event) {
      // Fallback to default avatar if image fails to load
      event.target.src = this.defaultAvatar;
    },
    
    checkDarkMode() {
      // Check if dark mode is stored in localStorage or system preference
      const savedMode = localStorage.getItem('darkMode');
      if (savedMode !== null) {
        this.darkMode = savedMode === 'true';
      } else {
        this.darkMode = window.matchMedia('(prefers-color-scheme: dark)').matches;
      }
      this.applyDarkMode();
    },
    
    toggleDarkMode() {
      this.darkMode = !this.darkMode;
      localStorage.setItem('darkMode', this.darkMode.toString());
      this.applyDarkMode();
    },
    
    applyDarkMode() {
      if (this.darkMode) {
        document.documentElement.classList.add("dark");
        document.body.classList.add("bg-dark", "text-light");
      } else {
        document.documentElement.classList.remove("dark");
        document.body.classList.remove("bg-dark", "text-light");
      }
    },
    
    changeLocale() {
      console.log("Change language");
      // Implement language switching logic here
    },
    
    toggleDropdown() {
      this.dropdownOpen = !this.dropdownOpen;
    },
    
    goToProfile() {
      this.dropdownOpen = false;
      this.$router.push('/admin/profile');
    },
    
    viewNotifications() {
      this.dropdownOpen = false;
      console.log("View Notifications");
    },
    
    goToSettings() {
      this.dropdownOpen = false;
      this.$router.push('/admin/settings');
    },
    
    async goHome() {
        this.dropdownOpen = false;
        try {
            await axios.get('/'); // Direct URL instead of route helper
            window.location.href = '/'; // If you want to redirect
        } catch (error) {
            console.error('Error:', error);
        }
    },
    
    async logout() {
      try {
        await axios.post("/logout");
        window.location.href = "/";
      } catch (error) {
        console.error('Logout error:', error);
        // Fallback logout
        window.location.href = "/";
      }
    },
  },
};
</script>

<style scoped>
.dropdown-menu {
  display: block;
  opacity: 0;
  visibility: hidden;
  transition: all 0.2s ease-in-out;
  transform: translateY(-10px);
}

.dropdown-menu.show {
  opacity: 1;
  visibility: visible;
  transform: translateY(0);
}

.dropdown-item {
  transition: background-color 0.2s ease-in-out;
}

.dropdown-item:hover {
  background-color: #f8f9fa;
}

.btn:focus {
  box-shadow: none;
}

/* Avatar loading animation */
.spinner-border-sm {
  width: 1rem;
  height: 1rem;
}
</style>