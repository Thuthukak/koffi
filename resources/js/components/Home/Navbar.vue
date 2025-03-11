<template>
    <nav class="bg-shape-primary shadow-md sticky top-0 z-50">
      <div class="container mx-auto flex justify-between items-center py-4 px-6">
        <!-- Logo -->
        <a href="#" class="text-xl font-bold">
            <img src="#" alt="Logo" style="width: 200px; height: auto;" />
        </a>
  
        <!-- Navigation -->
        <div class="hidden md:flex space-x-6 items-center">
          <a href="/" class="nav-item">Home</a>
          <a href="#" class="nav-item">Bookings</a>
          <a href="/faq" class="nav-item">Grooming</a>
          <a href="/contact-us" class="nav-item">Contact Us</a>
  
          <!-- Auth Buttons -->
          <!-- <button @click="openLoginModal" class="bg-blue-500  px-4 py-2 rounded-md">
            Login
          </button>
          <button @click="openQuestionnaireModal" class="bg-gray-800  px-4 py-2 rounded">
            Register
          </button> -->
        </div>
  
        <!-- Mobile Menu Button -->
        <button @click="toggleMobileMenu" class="md:hidden">
          <i class="fa fa-bars text-xl"></i>
        </button>
      </div>
  
      <!-- Mobile Menu -->
      <div v-if="isMobileMenuOpen" class="md:hidden bg-white shadow-md">
        <a href="/" class="block px-6 py-2">Home</a>
        <a href="#" class="block px-6 py-2">About Us</a>
        <a href="/faq" class="nav-item">FAQs</a>
        <a href="/contact-us" class="block px-6 py-2">Contact Us</a>
        <button @click="openLoginModal" class="bg-blue-600 px-4 py-2 rounded">
          Login
        </button>
        <button @click="openQuestionnaireModal" class="bg-gray-800  px-4 py-2 rounded">
          Register
        </button>
      </div>
    </nav>

    <!-- Modals -->
   
  </template>
  
  <script>


  import { ref } from "vue"; 

  export default {
    components: { },

    
  };
  </script>
  
  <style scoped>

  .dropdown {
    @apply relative;
    
  }
  .bg-shape-primary{
    background-color: #383838 !important;
  }
  .hover{
    color:#e89c35
  }
  a {
  text-decoration: none;
  }

  </style>
  