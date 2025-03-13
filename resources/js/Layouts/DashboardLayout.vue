<template>
    <div class="flex h-screen bg-gray-100">
      <DashSidebar />
      <div class="flex-1 flex flex-col">
        <DashNavbar />
        <main class="p-6">
          <slot />
        </main>
      </div>
    </div>
  </template>
  
  <script>
  import DashSidebar from "@/components/Admin/Dashboard/Sidebar.vue";
  import DashNavbar from "@/components/Admin/Dashboard/Navbar.vue";
  
  export default {
    name: 'DashboardLayout',
    components: { DashSidebar, DashNavbar },
  };
  </script>