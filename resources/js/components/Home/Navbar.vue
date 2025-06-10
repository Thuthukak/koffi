<template>
    <nav class="bg-shape-primary shadow sticky top-0 z-50">
      <div class="container mx-auto flex justify-between items-center py-4 px-6">
        <!-- Logo -->
        <a href="#" class="text-xl font-bold">
            <img src="#" alt="KOFI" style="width: 200px; height: auto;" />
        </a>
 
        <!-- Desktop Navigation -->
        <div class="hidden md:flex space-x-6 items-center">
          <a href="/" class="nav-item">Home</a>
          <a href="/bookings" class="nav-item">Bookings</a>
          <a href="/grooming" class="nav-item">Grooming</a>
          <a href="/#contact" class="nav-item">Contact Us</a>
 
          <!-- Auth Buttons -->
          <a href="/admin/auth" class="bg-blue-500 px-4 py-2 rounded text-white hover:bg-blue-600 transition-colors">
            Barber Login
          </a>
        </div>

        <!-- Mobile Menu Button -->
        <button 
          @click="toggleMobileMenu" 
          class="md:hidden flex flex-col justify-center items-center w-8 h-8 space-y-1"
          aria-label="Toggle mobile menu"
        >
          <span 
            class="block w-6 h-0.5 bg-gray-800 transition-all duration-300"
            :class="{ 'rotate-45 translate-y-2': isMobileMenuOpen }"
          ></span>
          <span 
            class="block w-6 h-0.5 bg-gray-800 transition-all duration-300"
            :class="{ 'opacity-0': isMobileMenuOpen }"
          ></span>
          <span 
            class="block w-6 h-0.5 bg-gray-800 transition-all duration-300"
            :class="{ '-rotate-45 -translate-y-2': isMobileMenuOpen }"
          ></span>
        </button>
      </div>

      <!-- Mobile Navigation Menu -->
      <div 
        class="md:hidden overflow-hidden transition-all duration-300 ease-in-out"
        :class="{ 'max-h-0': !isMobileMenuOpen, 'max-h-96': isMobileMenuOpen }"
      >
        <div class="px-6 pt-2 pb-4 space-y-3 bg-white border-t border-gray-200">
          <a 
            href="/" 
            class="block nav-item py-2 hover:text-orange-500 transition-colors"
            @click="closeMobileMenu"
          >
            Home
          </a>
          <a 
            href="/bookings" 
            class="block nav-item py-2 hover:text-orange-500 transition-colors"
            @click="closeMobileMenu"
          >
            Bookings
          </a>
          <a 
            href="/grooming" 
            class="block nav-item py-2 hover:text-orange-500 transition-colors"
            @click="closeMobileMenu"
          >
            Grooming
          </a>
          <a 
            href="/#contact" 
            class="block nav-item py-2 hover:text-orange-500 transition-colors"
            @click="closeMobileMenu"
          >
            Contact Us
          </a>
          <a 
            href="/admin/auth" 
            class="block bg-blue-500 px-4 py-2 rounded text-white text-center hover:bg-blue-600 transition-colors mt-4"
            @click="closeMobileMenu"
          >
            Barber Login
          </a>
        </div>
      </div>
    </nav>  
</template>
 
<script>
import { ref } from "vue";

export default {
  setup() {
    const isMobileMenuOpen = ref(false);

    const toggleMobileMenu = () => {
      isMobileMenuOpen.value = !isMobileMenuOpen.value;
    };

    const closeMobileMenu = () => {
      isMobileMenuOpen.value = false;
    };

    return {
      isMobileMenuOpen,
      toggleMobileMenu,
      closeMobileMenu
    };
  }
};
</script>
 
<style scoped>
.bg-shape-primary {
  background-color: #ffffff !important;
}

.hover {
  color: #e89c35;
}

a {
  text-decoration: none;
}

.nav-item {
  color: #374151;
  font-weight: 500;
  transition: color 0.3s ease;
}

.nav-item:hover {
  color: #e89c35;
}

/* Ensure smooth animations */
.transition-all {
  transition-property: all;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}
</style>