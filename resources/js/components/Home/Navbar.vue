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
          <a href="/bookings" class="nav-item">Bookings</a>
          <a href="/faq" class="nav-item">Grooming</a>
          <a href="/contact-us" class="nav-item">Contact Us</a>
  
          <!-- Auth Buttons -->
          <a href="/admin/auth" class="bg-blue-500 px-4 py-2 rounded text-white">
            Barber Login
          </a>
        </div>
      </div>
    </nav>   
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
  