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
        <div class="modal-header d-flex justify-content-between align-items-center bg-primary text-white">
          <h5 class="modal-title">Book Appointment</h5>
          <button type="button" class="close ms-auto text-white" @click="closeModal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <!-- Body -->
        <div class="modal-body">
          <div v-if="errorMessages.length" class="alert alert-danger">
            <ul class="mb-0">
              <li v-for="(error, index) in errorMessages" :key="index">{{ error }}</li>
            </ul>
          </div>

          <form @submit.prevent="submitForm">
            <div class="row g-3">
              <!-- Personal Info -->
              <div class="col-md-6">
                <label for="name" class="form-label">Full Name</label>
                <input 
                  v-model="form.name" 
                  type="text" 
                  class="form-control"
                  placeholder="e.g. John Doe"
                  required
                >
              </div>

              <div class="col-md-6">
                <label for="phone_number" class="form-label">Phone Number</label>
                <input 
                  v-model="form.phone_number" 
                  type="tel" 
                  class="form-control"
                  placeholder="e.g. 071 234 5678"
                >
              </div>

              <div class="col-md-6">
                <label for="email" class="form-label">Email Address</label>
                <input 
                  v-model="form.email" 
                  type="email" 
                  class="form-control"
                  placeholder="e.g. john@example.com"
                >
              </div>

              <!-- Service Selection -->
              <div class="col-md-6">
                <label for="service" class="form-label">Select Service</label>
                <select 
                  v-model="form.service" 
                  class="form-select"
                  required
                >
                  <option value="" >Choose a service...</option>
                  <option 
                    v-for="service in services" 
                    :key="service.id" 
                    :value="service.id"
                  >
                    {{ service.name }} ({{ service.duration }} mins) - R{{ service.price }}
                  </option>
                  <option v-if="services.length === 0">
                    Brush - 30 mins - R60
                  </option>
                  <option v-if="services.length === 0">
                    Brush Fade - 40 mins - R100
                  </option>
                </select>
              </div>
              <!-- Barber Selection -->
                <div class="col-md-6">
                  <label for="barber" class="form-label">Preferred Barber</label>
                  <select 
                    v-model="form.barber" 
                    class="form-select"
                    required
                  >
                    <option value="" disabled>Choose your barber...</option>
                    <option 
                      v-for="barber in barbers" 
                      :key="barber.id" 
                      :value="barber.id"
                    >
                      {{ barber.name }} - {{ barber.specialty }} ({{ barber.experience }}+ years)
                    </option>
                    <option v-if="barbers.length === 0">
                    Koffi
                  </option>
                  <option v-if="barbers.length === 0">
                    New Guy
                  </option>
                  </select>
                </div>

              <!-- Date/Time Picker -->
              <div class="col-md-6">
                <label for="bookingSlot" class="form-label">Booking Time</label>
                <input 
                  v-model="form.bookingSlot" 
                  type="datetime-local" 
                  class="form-control"
                  :min="minDateTime"
                  :max="maxDateTime"
                  required
                >
                <small class="text-muted">
                  Open hours: 08:00 - 19:00
                </small>
              </div>
            </div>

            <!-- Submit Button -->
            <div class="mt-4 d-grid">
              <button 
                type="submit" 
                class="btn btn-primary btn-lg"
                :disabled="isSubmitting"
              >
                <span v-if="isSubmitting">
                  <span class="spinner-border spinner-border-sm" role="status"></span>
                  Booking...
                </span>
                <span v-else>
                  Confirm Booking
                </span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from "vue";
import axios from "axios";

export default {
  props: {
    isOpen: Boolean
  },

  emits: ["close", "booking-created"],

  setup(props, { emit }) {
    // Reactive state
    const form = ref({
      name: '',
      phone_number: '',
      email: '',
      service: '',
      barber: '',
      bookingSlot: ''
    });
    
    const errorMessages = ref([]);
    const isSubmitting = ref(false);
    const services = ref([]);
    const barbers = ref([]);

    // Computed
    const isModalOpen = computed(() => props.isOpen);
    
    // Methods
    const closeModal = () => {
      emit('close');
    };

    const minDateTime = ref('');
    const maxDateTime = ref('');

    const getMinDateTime = () => {
      const now = new Date();
      now.setHours(now.getHours() + 1);
      return now.toISOString().slice(0, 16);
    };

    const getMaxDateTime = () => {
      const futureDate = new Date();
      futureDate.setDate(futureDate.getDate() + 30);
      return futureDate.toISOString().slice(0, 16);
    };

    const fetchServices = async () => {
      try {
        const response = await axios.get("/services");
        services.value = response.data;
      } catch (error) {
        console.error("Error fetching services:", error);
        services.value = [];
      }
    };

    const fetchBarbers = async () => {
      try {
        const response = await axios.get("/barbers");
        barbers.value = response.data.map(barber => ({
          ...barber,
          specialty: barber.specialty || 'General Barber'
        }));
      } catch (error) {
        console.error("Error fetching barbers:", error);
        barbers.value = [];
      }
    };

    const resetForm = () => {
      form.value = {
        name: '',
        phone_number: '',
        email: '',
        service: '',
        barber: '',
        bookingSlot: ''
      };
    };

    const handleSubmissionError = (error) => {
      if (error.response?.data?.errors) {
        errorMessages.value = Object.values(error.response.data.errors).flat();
      } else {
        errorMessages.value = ['Failed to submit booking. Please try again.'];
      }
    };

    const submitForm = async () => {
      isSubmitting.value = true;
      errorMessages.value = [];

      try {
          const response = await axios.post('/bookings-create', {
            name: form.value.name,
            phone_number: form.value.phone_number,
            email: form.value.email,
            service: form.value.service,
            barber: form.value.barber,
            bookingSlot: form.value.bookingSlot
          });

          if (response.data.success) {
            emit('booking-created', {
            client: {
              name: form.value.name,
              phone: form.value.phone_number,
              email: form.value.email
            },
            service: services.value.find(s => s.id === form.value.service),
            barber: barbers.value.find(b => b.id === form.value.barber),
            bookingSlot: form.value.bookingSlot,
            reference: response.data.booking.reference
          });
          resetForm();
          closeModal();
        }
      } catch (error) {
        handleSubmissionError(error);
      } finally {
        isSubmitting.value = false;
      }
    };

    // Lifecycle hooks
    onMounted(() => {
      minDateTime.value = getMinDateTime();
      maxDateTime.value = getMaxDateTime();
      fetchServices();
      fetchBarbers();
    });

    return {
      form,
      errorMessages,
      isModalOpen,
      services,
      barbers,
      minDateTime,
      maxDateTime,
      isSubmitting,
      closeModal,
      submitForm
    };
  }
};
</script>

<style scoped>
.modal-header {
  border-bottom: 2px solid rgba(255,255,255,0.1);
}

.form-control:focus, .form-select:focus {
  border-color: #0d6efd;
  box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

.btn-primary {
  background-color: #0d6efd;
  border-color: #0d6efd;
  transition: all 0.3s ease;
}

.btn-primary:hover {
  background-color: #0b5ed7;
  border-color: #0a58ca;
}

.text-muted {
  font-size: 0.9em;
}

.modal {
  z-index: 1050 !important;
}
</style>