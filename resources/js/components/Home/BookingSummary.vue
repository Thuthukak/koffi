<template>
    <div class="summary-container">
      <div class="card shadow-lg">
        <div class="card-header bg-primary text-white">
          <h3 class="mb-0">Booking Confirmed! 🎉</h3>
        </div>
        <div class="card-body">
          <h4 class="mb-4">Booking Details</h4>
          
          <div class="row g-3 mb-4">
            <div class="col-md-6">
              <label class="text-muted">Client Name:</label>
              <p class="h5">{{ bookingDetails.client.name }}</p>
            </div>
            <div class="col-md-6">
              <label class="text-muted">Contact Phone:</label>
              <p class="h5">{{ bookingDetails.client.phone }}</p>
            </div>
            <div class="col-md-6">
              <label class="text-muted">Email:</label>
              <p class="h5">{{ bookingDetails.client.email }}</p>
            </div>
          </div>
  
          <div class="row g-3 mb-4">
            <div class="col-md-6">
              <label class="text-muted">Service:</label>
              <p class="h5">
                {{ bookingDetails.service.name }} 
                <small class="text-muted">({{ bookingDetails.service.duration }} mins)</small>
              </p>
            </div>
            <div class="col-md-6">
              <label class="text-muted">Barber:</label>
              <p class="h5">
                {{ bookingDetails.barber.name }}
                <small class="text-muted">- {{ bookingDetails.barber.specialty }}</small>
              </p>
            </div>
          </div>
  
          <div class="row mb-4">
            <div class="col-12">
              <label class="text-muted">Appointment Time:</label>
              <p class="h5">{{ formattedDateTime }}</p>
            </div>
          </div>
  
          <div class="d-grid">
            <button 
              @click="returnToHome"
              class="btn btn-primary btn-lg"
            >
              Return to Home Page
            </button>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import { computed } from 'vue';
  
  export default {
    props: {
      bookingDetails: {
        type: Object,
        required: true
      }
    },
    
    setup(props) {
      const formattedDateTime = computed(() => {
        const date = new Date(props.bookingDetails.bookingSlot);
        return date.toLocaleString('en-US', {
          weekday: 'long',
          year: 'numeric',
          month: 'long',
          day: 'numeric',
          hour: '2-digit',
          minute: '2-digit'
        });
      });
  
      const returnToHome = () => {
        window.location.reload(); // Or use your router
      };
  
      return {
        formattedDateTime,
        returnToHome
      };
    }
  };
  </script>
  
  <style scoped>
  .summary-container {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #f8f9fa;
    padding: 2rem;
  }
  
  .card {
    max-width: 800px;
    width: 100%;
    border: none;
    border-radius: 1rem;
  }
  
  .card-header {
    border-radius: 1rem 1rem 0 0 !important;
    padding: 2rem;
  }
  
  .card-body {
    padding: 2rem;
  }
  
  .h5 {
    margin-bottom: 0.5rem;
  }
  </style>