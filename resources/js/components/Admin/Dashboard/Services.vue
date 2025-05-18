<template>
  <div class="container py-4">
    <!-- Header with add button -->
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2>Manage Services</h2>
      <button class="btn btn-primary" @click="openAddModal">
        <i class="bi bi-plus-circle me-1"></i> Add Service
      </button>
    </div>

    <!-- Search bar -->
    <div class="mb-4">
      <div class="input-group">
        <span class="input-group-text bg-light">
          <i class="bi bi-search"></i>
        </span>
        <input 
          type="text" 
          class="form-control" 
          placeholder="Search services..." 
          v-model="searchQuery"
        >
      </div>
    </div>

    <!-- Loading state -->
    <div v-if="loading" class="text-center my-5">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
      <p class="mt-2">Loading services...</p>
    </div>

    <!-- No services message -->
    <div v-else-if="filteredServices.length === 0" class="text-center my-5">
      <i class="bi bi-inbox display-4 text-muted"></i>
      <p class="lead mt-3">No services found</p>
      <p class="text-muted" v-if="searchQuery">Try changing your search query</p>
      <button v-else class="btn btn-outline-primary mt-2" @click="openAddModal">
        Add your first service
      </button>
    </div>

    <!-- Services table -->
    <div v-else class="table-responsive">
      <table class="table table-hover border">
        <thead class="table-light">
          <tr>
            <th>Name</th>
            <th>Description</th>
            <th>Price</th>
            <th>Duration</th>
            <th class="text-center">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="service in filteredServices" :key="service.id" class="align-middle">
            <td class="fw-medium">{{ service.name }}</td>
            <td>
              <span :title="service.description">
                {{ truncateText(service.description, 100) }}
              </span>
            </td>
            <td>${{ service.price }}</td>
            <td>{{ service.duration }} min</td>
            <td class="text-center">
              <div class="btn-group">
                <button 
                  class="btn btn-outline-primary btn-sm" 
                  @click="openEditModal(service)"
                  title="Edit service"
                >
                  <i class="bi bi-pencil-square"></i>
                </button>
                <button 
                  class="btn btn-outline-danger btn-sm" 
                  @click="openDeleteModal(service)"
                  title="Delete service"
                >
                  <i class="bi bi-trash"></i>
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Add/Edit Modal -->
    <div class="modal fade" id="serviceModal" tabindex="-1" ref="serviceModal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ isEditing ? 'Edit Service' : 'Add New Service' }}</h5>
            <button type="button" class="btn-close" @click="closeModal"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="submitForm">
              <div class="mb-3">
                <label for="serviceName" class="form-label">Service Name</label>
                <input
                  type="text"
                  class="form-control"
                  id="serviceName"
                  v-model="formData.name"
                  required
                >
                <div v-if="errors.name" class="text-danger mt-1">{{ errors.name }}</div>
              </div>
              
              <div class="mb-3">
                <label for="serviceDescription" class="form-label">Description</label>
                <textarea
                  class="form-control"
                  id="serviceDescription"
                  rows="3"
                  v-model="formData.description"
                  required
                ></textarea>
                <div v-if="errors.description" class="text-danger mt-1">{{ errors.description }}</div>
              </div>
              
              <div class="row mb-3">
                <div class="col-md-6">
                  <label for="servicePrice" class="form-label">Price (R)</label>
                  <input
                    type="number"
                    class="form-control"
                    id="servicePrice"
                    v-model="formData.price"
                    min="0"
                    required
                  >
                  <div v-if="errors.price" class="text-danger mt-1">{{ errors.price }}</div>
                </div>
                
                <div class="col-md-6">
                  <label for="serviceDuration" class="form-label">Duration (minutes)</label>
                  <input
                    type="number"
                    class="form-control"
                    id="serviceDuration"
                    v-model="formData.duration"
                    min="1"
                    required
                  >
                  <div v-if="errors.duration" class="text-danger mt-1">{{ errors.duration }}</div>
                </div>
              </div>
              
              <div class="d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-secondary" @click="closeModal">Cancel</button>
                <button type="submit" class="btn btn-primary" :disabled="isSubmitting">
                  <span v-if="isSubmitting" class="spinner-border spinner-border-sm me-1"></span>
                  {{ isEditing ? 'Update' : 'Add' }} Service
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" ref="deleteModal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Delete Service</h5>
            <button type="button" class="btn-close" @click="closeDeleteConfirmModal"></button>
          </div>
          <div class="modal-body">
            <p>Are you sure you want to delete the service: <strong>{{ serviceToDelete?.name }}</strong>?</p>
            <p class="text-danger"><small>This action cannot be undone.</small></p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="closeDeleteConfirmModal">Cancel</button>
            <button type="button" class="btn btn-danger" @click="deleteService" :disabled="isDeleting">
              <span v-if="isDeleting" class="spinner-border spinner-border-sm me-1"></span>
              Delete
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Toast notification -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1050">
      <div 
        class="toast align-items-center text-white" 
        :class="toastClass" 
        role="alert" 
        aria-live="assertive" 
        aria-atomic="true"
        ref="toast"
      >
        <div class="d-flex">
          <div class="toast-body">
            {{ toastMessage }}
          </div>
          <button type="button" class="btn-close me-2 m-auto" @click="hideToast"></button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import { Modal, Toast } from 'bootstrap';

export default {
  data() {
    return {
      services: [],
      filteredServices: [],
      searchQuery: "",
      loading: true,
      isSubmitting: false,
      isDeleting: false,
      isEditing: false,
      serviceToDelete: null,
      serviceModal: null,
      deleteModal: null,
      toast: null,
      toastMessage: "",
      toastClass: "bg-success",
      formData: {
        name: "",
        description: "",
        price: "",
        duration: ""
      },
      errors: {
        name: "",
        description: "",
        price: "",
        duration: ""
      }
    };
  },
  
  watch: {
    searchQuery(newQuery) {
      this.filterServices();
    }
  },

  mounted() {
    this.fetchServices();
  },

  methods: {
    async fetchServices() {
      this.loading = true;
      try {
        const response = await axios.get("/api/services");
        this.services = response.data;
        this.filterServices();
      } catch (error) {
        this.showToast("Error fetching services. Please try again.", "bg-danger");
        console.error("Error fetching services:", error);
      } finally {
        this.loading = false;
      }
    },

    filterServices() {
      if (!this.searchQuery) {
        this.filteredServices = [...this.services];
        return;
      }
      
      const query = this.searchQuery.toLowerCase();
      this.filteredServices = this.services.filter(service => 
        service.name.toLowerCase().includes(query) ||
        service.description.toLowerCase().includes(query)
      );
    },

    truncateText(text, maxLength) {
      if (!text) return '';
      return text.length > maxLength 
        ? text.substring(0, maxLength) + '...' 
        : text;
    },

    openAddModal() {
      this.isEditing = false;
      this.resetForm();
      if (!this.serviceModal) {
        this.serviceModal = new Modal(this.$refs.serviceModal);
      }
      this.serviceModal.show();
    },

    openEditModal(service) {
      this.isEditing = true;
      this.formData = { ...service };
      if (!this.serviceModal) {
        this.serviceModal = new Modal(this.$refs.serviceModal);
      }
      this.serviceModal.show();
    },

    openDeleteModal(service) {
      this.serviceToDelete = service;
      if (!this.deleteModal) {
        this.deleteModal = new Modal(this.$refs.deleteModal);
      }
      this.deleteModal.show();
    },

    closeModal() {
      if (this.serviceModal) {
        this.serviceModal.hide();
      }
      this.resetForm();
    },

    closeDeleteConfirmModal() {
      if (this.deleteModal) {
        this.deleteModal.hide();
      }
      this.serviceToDelete = null;
    },

    resetForm() {
      this.formData = {
        name: "",
        description: "",
        price: "",
        duration: ""
      };
      this.errors = {
        name: "",
        description: "",
        price: "",
        duration: ""
      };
    },

    async submitForm() {
      // Reset errors
      this.errors = {
        name: "",
        description: "",
        price: "",
        duration: ""
      };

      // Basic validation
      if (!this.formData.name) {
        this.errors.name = "Service name is required";
      }
      if (!this.formData.description) {
        this.errors.description = "Description is required";
      }
      if (!this.formData.price || isNaN(this.formData.price) || this.formData.price < 0) {
        this.errors.price = "Valid price is required";
      }
      if (!this.formData.duration || isNaN(this.formData.duration) || this.formData.duration < 1) {
        this.errors.duration = "Valid duration is required";
      }

      // If there are errors, don't submit
      if (Object.values(this.errors).some(error => error !== "")) {
        return;
      }

      this.isSubmitting = true;

      try {
        if (this.isEditing) {
          // Update existing service
          await this.updateService();
        } else {
          // Create new service
          await this.createService();
        }
      } catch (error) {
        console.error("Form submission error:", error);
        
        // Handle validation errors from the server
        if (error.response && error.response.data && error.response.data.errors) {
          const serverErrors = error.response.data.errors;
          Object.keys(serverErrors).forEach(key => {
            this.errors[key] = serverErrors[key][0];
          });
        } else {
          this.showToast(
            `Error ${this.isEditing ? 'updating' : 'creating'} service. Please try again.`, 
            "bg-danger"
          );
        }
      } finally {
        this.isSubmitting = false;
      }
    },

    async createService() {
      const response = await axios.post("/api/services", this.formData);
      
      // Add the new service to the list
      this.services.push(response.data.service);
      this.filterServices();
      
      // Close modal and show success message
      this.closeModal();
      this.showToast("Service added successfully!", "bg-success");
    },

    async updateService() {
      const response = await axios.put(`/api/services/${this.formData.id}`, this.formData);
      
      // Update the service in the list
      const index = this.services.findIndex(service => service.id === this.formData.id);
      if (index !== -1) {
        this.services[index] = response.data.service;
        this.filterServices();
      }
      
      // Close modal and show success message
      this.closeModal();
      this.showToast("Service updated successfully!", "bg-success");
    },

    async deleteService() {
      if (!this.serviceToDelete) return;
      
      this.isDeleting = true;
      
      try {
        await axios.delete(`/api/services/${this.serviceToDelete.id}`);
        
        // Remove the service from the list
        this.services = this.services.filter(service => service.id !== this.serviceToDelete.id);
        this.filterServices();
        
        // Close modal and show success message
        this.closeDeleteConfirmModal();
        this.showToast("Service deleted successfully!", "bg-success");
      } catch (error) {
        console.error("Error deleting service:", error);
        this.showToast("Error deleting service. Please try again.", "bg-danger");
      } finally {
        this.isDeleting = false;
      }
    },

    showToast(message, colorClass = "bg-success") {
      this.toastMessage = message;
      this.toastClass = colorClass;
      
      if (!this.toast) {
        this.toast = new Toast(this.$refs.toast);
      }
      
      this.toast.show();
      
      // Auto-hide after 5 seconds
      setTimeout(() => {
        this.hideToast();
      }, 5000);
    },
    
    hideToast() {
      if (this.toast) {
        this.toast.hide();
      }
    }
  }
};
</script>

<style scoped>
.table th {
  font-weight: 600;
}

.toast {
  min-width: 250px;
}
</style>