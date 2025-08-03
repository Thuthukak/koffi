<template>
  <nav class="bg-shape-primary shadow-md sticky top-0 z-50">
    <div class="container mx-auto px-4">
      <!-- Desktop Navigation -->
      <div class="hidden md:flex justify-between items-center py-4">
        <!-- Logo -->
        <a href="/" class="text-xl font-bold">
          <img 
            src=""
            alt="Logo" 
            class="w-40 h-auto" />
        </a>

        <!-- Navigation Links -->
        <div class="flex space-x-6 items-center">
          <a href="/" class="nav-item">Home</a>
          <a href="/bookings" class="nav-item">Bookings</a>
          <a href="/grooming" class="nav-item">Grooming</a>
          <a href="/#contact" class="nav-item">Contact Us</a>
          
          <a href="/admin/auth" class="bg-blue-500 px-4 py-2 rounded text-white hover:bg-blue-600 transition-colors">
            Barber Login
          </a>
        </div>
      </div>

      <!-- Mobile Navigation -->
      <div class="md:hidden flex justify-between items-center py-3">
        <!-- Mobile Logo -->
        <a href="/" class="text-xl font-bold">
          <img 
            src=""
            alt="Logo" 
            class="w-32 h-auto" />
        </a>

        <!-- Mobile Right Section -->
        <div class="flex items-center space-x-3">
          <!-- Mobile Quote Button -->
          <a href="/admin/auth" class="bg-blue-500 px-4 py-2 rounded text-white hover:bg-blue-600 transition-colors">
            Barber Login
          </a>
          
          <!-- Mobile Menu Button -->
          <button @click="toggleMobileMenu" class="p-2 rounded-lg hover:bg-gray-100 transition">
            <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path v-if="!isMobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
              <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
      </div>

      <!-- Mobile Menu -->
      <div v-if="isMobileMenuOpen" class="md:hidden bg-white border-t border-gray-200">
        <div class="py-2 space-y-1">
          <a href="/" class="mobile-nav-item">Home</a>
          <a href="/bookings" class="nav-item">Bookings</a>
          <a href="/grooming" class="nav-item">Grooming</a>
          <a href="/#contact" class="nav-item">Contact Us</a>
        </div>
      </div>
    </div>
  </nav>

</template>

<script>

export default {
  name: 'Navbar',
  data() {
    return {
      isDropdownOpen: false,
      isMobileMenuOpen: false,
      isMobileServicesOpen: false,
      isModalOpen: false
    };
  },
  methods: {
    toggleDropdown() {
      this.isDropdownOpen = !this.isDropdownOpen;
    },
    toggleMobileMenu() {
      this.isMobileMenuOpen = !this.isMobileMenuOpen;
      // Close services dropdown when mobile menu closes
      if (!this.isMobileMenuOpen) {
        this.isMobileServicesOpen = false;
      }
    },
    toggleMobileServices() {
      this.isMobileServicesOpen = !this.isMobileServicesOpen;
    },
    openQuoteModal() {
      this.isModalOpen = true;
    },
    closeQuoteModal() {
      this.isModalOpen = false;
    },
    // Close dropdowns when clicking outside
    handleClickOutside(event) {
      // Check if click is outside the dropdown container
      if (!event.target.closest('.dropdown-container')) {
        this.isDropdownOpen = false;
      }
    }
  },
  mounted() {
    document.addEventListener('click', this.handleClickOutside);
  },
  beforeDestroy() {
    document.removeEventListener('click', this.handleClickOutside);
  }
};
</script>

<style scoped>
.bg-shape-primary {
  background-color: #ffffff !important;
}

/* Desktop Navigation Styles */
.nav-item {
  color: #374151;
  padding: 0.5rem 0.75rem;
  border-radius: 0.5rem;
  transition: all 0.2s ease;
  text-decoration: none;
}

.nav-item:hover {
  color: #2563eb;
  background-color: #f9fafb;
}

.dropdown-item {
  display: block;
  padding: 0.5rem 1rem;
  color: #374151;
  text-decoration: none;
  transition: all 0.2s ease;
}

.dropdown-item:hover {
  background-color: #f3f4f6;
  color: #2563eb;
}

/* Mobile Navigation Styles */
.mobile-nav-item {
  display: block;
  padding: 0.75rem 1rem;
  color: #374151;
  border-bottom: 1px solid #f3f4f6;
  text-decoration: none;
  transition: all 0.2s ease;
}

.mobile-nav-item:hover {
  background-color: #f9fafb;
  color: #2563eb;
}

.mobile-services-button {
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: space-between;
  text-align: left;
  background: none;
  border: none;
  font-size: inherit;
  cursor: pointer;
}

.mobile-sub-item {
  display: block;
  padding: 0.5rem 1.5rem;
  color: #6b7280;
  text-decoration: none;
  transition: all 0.2s ease;
}

.mobile-sub-item:hover {
  background-color: #f3f4f6;
  color: #2563eb;
}

/* Remove default link styling */
a {
  text-decoration: none !important;
}

/* Smooth animations */
.transition-transform {
  transition: transform 0.2s ease-in-out;
}

.rotate-180 {
  transform: rotate(180deg);
}

/* Ensure dropdown appears above other content */
.dropdown {
  position: relative;
}

/* Mobile menu animation */
.mobile-menu-enter-active,
.mobile-menu-leave-active {
  transition: all 0.3s ease;
}

.mobile-menu-enter,
.mobile-menu-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}
</style>