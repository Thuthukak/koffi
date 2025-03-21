<template>
    <div>
      <!-- booking text on left. book button on right -->
      <div class="d-flex justify-content-between align-items-center">
        <h2 class="mb-4">Services</h2>
        <button class="btn btn-primary" @click="openServiceAddModal">Add</button>
      </div>
      
  
      <table class="table table-bordered mt-4">
        <thead>
          <tr>
            <th>Name</th>
            <th>Description</th>
            <th>Price</th>
            <th>Duration</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(service, index) in services" :key="service.id">
            <td>{{ service.name }}</td>
            <td>{{ service.description }}</td>
            <td>{{ service.price }}</td>
            <td>{{ service.duration }}</td>
          </tr>
        </tbody>
      </table>
    </div>
    <ServiceAddModal :isOpen="showServiceAddModal" @close="closeServiceAddModal" />
  </template>
  
  <script>
  import axios from "axios";
  import ServiceAddModal from "@/components/Admin/Dashboard/ServiceAddModal.vue";
  export default {
    components: { ServiceAddModal },
    data() {
      return {
        services: [],
        showServiceAddModal: false
      };
    },
    mounted() {
      this.fetchServices();
    },
    methods: {
      async fetchServices() {
        try {
          const response = await axios.get("/api/services");
          this.services = response.data;
        } catch (error) {
          console.error("Error fetching services:", error);
        }
      },
      openServiceAddModal() {
        this.showServiceAddModal = true;
      },
      closeServiceAddModal() {
        this.showServiceAddModal = false;
      },
    },
  };
  </script>
  
  <style>
  /* You can add additional custom styling if needed */
  </style>
  