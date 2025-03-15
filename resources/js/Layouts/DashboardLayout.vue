<template>
    <div class="flex h-screen bg-gray-100">
      <Sidebar />
      <div class="flex-1 flex flex-col">
        <Navbar />
        <main class="p-6">
          <slot />
        </main>
      </div>
    </div>
  </template>
  
  <script>
  import Sidebar from "../components/Admin/Sidebar.vue";
  import Navbar from "../components/Admin/Navbar.vue";

  
  export default {
    name: 'DashboardLayout',
    components: { Sidebar, Navbar },
  };
  </script>