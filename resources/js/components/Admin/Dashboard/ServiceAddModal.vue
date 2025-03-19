<template>
    <div 
      v-if="isModalOpen" 
      class="modal fade show d-block" 
      tabindex="-1" 
      role="dialog"
      @click.self="closeModal"
    >
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <!-- Header -->
          <div class="modal-header d-flex justify-content-between align-items-center">
            <h5 class="modal-title">Add</h5>
            <button type="button" class="close ms-auto" @click="closeModal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
  
          <!-- Body -->
          <div class="modal-body">
            <div v-if="errorMessages.length" class="alert alert-danger">
              <ul>
                <li v-for="(error, index) in errorMessages" :key="index">{{ error }}</li>
              </ul>
            </div>
  
            <form @submit.prevent="submitForm">
              <div class="row">
                    <div class="col-md-6 mt-3">
                      <label for="name" class="form-label">Name</label>
                      <input v-model="form.name" type="text" class="form-control" placeholder="Name">
                    </div>
  
                    <div class="col-md-6 mt-3">
                      <label for="description" class="form-label">Description</label>
                      <input v-model="form.description" type="text" class="form-control" placeholder="description">
                    </div>
       
                    <div class="col-md-6 mt-3">
                      <label for="price" class="form-label">Price</label>
                      <input v-model="form.price" type="number" class="form-control" placeholder="Price">
                    </div>

                    <div class="col-md-6 mt-3">
                      <label for="duration" class="form-label">Duration (Mins)</label>
                      <input v-model="form.duration" type="number" class="form-control" placeholder="Duration">
                    </div>
                  </div>
                  
                  <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Book</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import { ref, computed } from "vue";
  import axios from "axios";
  
  export default {
    props: {
      isOpen: Boolean
    },
  
    emits: ["close"],
    
    setup(props, { emit }) {
      const form = ref({
        name: '',
        description: '',
        price: '',
        duration: '',
      });
  
      const errorMessages = ref([]);
      const isModalOpen = computed(() => props.isOpen);
  
      const closeModal = () => {
        emit('close');
      };
  
      const submitForm = async () => {
        try {
          const response = await axios.post('/admin/add-service', form.value);
          
          console.log(response.data);
  
          // Reset form
          form.value = {
            name: '',
            description: '',
            price: '',
            duration: '',
          };
  
          // Close modal
          closeModal();

          window.location.href = "/admin/services"; 

        } catch (error) {
          if (error.response && error.response.data.errors) {
            errorMessages.value = Object.values(error.response.data.errors).flat();
                      
          } else {
            console.error("Error submitting form:", error);
          }
        }
      };
  
      return {
        form,
        errorMessages,
        isModalOpen,
        closeModal,
        submitForm
      };
    }
  };
  </script>
  
  <style scoped>
  .modal {
    background: rgba(0, 0, 0, 0.5);
  }
  .modal-header {
    display: flex;
    justify-content: space-between;
    width: 100%;
  }
  .close {
    font-size: 1.5rem;
    cursor: pointer;
  }
  </style>
  