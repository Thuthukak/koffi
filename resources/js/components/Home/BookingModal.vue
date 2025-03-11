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
          <h5 class="modal-title">Book</h5>
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
                    <label for="phone_number" class="form-label">Phone Number (Optional)</label>
                    <input v-model="form.phone_number" type="text" class="form-control" placeholder="Phone Number">
                  </div>
     
                  <div class="col-md-6 mt-3">
                    <label for="email" class="form-label">Email (Optional)</label>
                    <input v-model="form.email" type="email" class="form-control" placeholder="Email">
                  </div>
                
                  <div class="col-md-6 mt-3">
                    <label for="service" class="form-label">Select Service</label>
                    <select v-model="form.service" class="border rounded px-3 py-2 w-full">
                      <option value="" disabled>Select a service</option>
                      <option v-for="service in services" :key="service.id" :value="service.id">
                        {{ service.name }} - {{ service.duration }} mins 
                      </option>
                    </select>
                  </div>

                  <div class="col-md-6 mt-3">
                    <label for="barber" class="form-label">Prefered barber</label>
                    <select v-model="form.barber" class="border rounded px-3 py-2 w-full">
                      <option value="" disabled>Select a Barber</option>
                      <option v-for="barber in barbers" :key="barber.id" :value="barber.id">
                        {{ barber.user.name }}
                      </option>
                    </select>
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

  mounted() {
    this.fetchServices();
    this.fetchBarbers();
  },

  methods: {
    async fetchServices() {
      try {
        const response = await axios.get("/services");
        this.services = response.data;
      } catch (error) {
        console.error("Error fetching services:", error);
      }
    },

    async fetchBarbers() {
      try {
        const response = await axios.get("/barbers");
        this.barbers = response.data;
      } catch (error) {
        console.error("Error fetching services:", error);
      }
    },
  },

  emits: ["close"],
  setup(props, { emit }) {
    const form = ref({
      name: '',
      phone_number: '',
      email: '',
      service: '',
      barber: '',
    });

    const errorMessages = ref([]);
    const isModalOpen = computed(() => props.isOpen);

    const closeModal = () => {
      emit('close');
    };

    const submitForm = () => {
      // submit to route /bookings-create
      axios.post('/bookings-create', form.value)
        .then(response => {
          // handle success
          console.log(response.data);
          // reset form
          form.value.name = '';
          form.value.phone_number = '';
          form.value.email = '';
          form.value.service = '';
          form.value.barber = '';
          // close modal
          closeModal();
        })
        .catch(error => {
          // handle error
          console.log(error.response.data.errors);
          errorMessages.value = error.response.data.errors;
        });
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
