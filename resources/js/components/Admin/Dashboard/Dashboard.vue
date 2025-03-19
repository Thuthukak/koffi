<template>
  <div class="container mt-4">
    <h1 class="mb-4 fw-bold">Welcome to the Dashboard</h1>
    <div class="row g-4">
      <!-- Queue Overview -->
      <div class="col-md-6 col-lg-3">
        <div class="card border-primary shadow-sm">
          <div class="card-body d-flex align-items-center justify-content-between">
            <div>
              <h5 class="card-title text-primary">Current Queue</h5>
              <h2 class="fw-bold">{{ queueCount }}</h2>
              <p class="text-muted mb-0">People waiting</p>
            </div>
            <font-awesome-icon :icon="icons.faUsers" class="text-primary display-4" />
          </div>
        </div>
      </div>

      <!-- Total Bookings -->
      <div class="col-md-6 col-lg-3">
        <div class="card border-success shadow-sm">
          <div class="card-body d-flex align-items-center justify-content-between">
            <div>
              <h5 class="card-title text-success">Total Bookings</h5>
              <h2 class="fw-bold">{{ totalBookings }}</h2>
              <p class="text-muted mb-0">This month</p>
            </div>
            <font-awesome-icon :icon="icons.faCalendarCheck" class="text-success display-4" />
          </div>
        </div>
      </div>

      <!-- Completed Services -->
      <div class="col-md-6 col-lg-3">
        <div class="card border-warning shadow-sm">
          <div class="card-body d-flex align-items-center justify-content-between">
            <div>
              <h5 class="card-title text-warning">Completed Services</h5>
              <h2 class="fw-bold">{{ completedServices }}</h2>
              <p class="text-muted mb-0">Satisfied customers</p>
            </div>
            <font-awesome-icon :icon="icons.faCheckCircle" class="text-warning display-4" />
          </div>
        </div>
      </div>

      <!-- Revenue -->
      <div class="col-md-6 col-lg-3">
        <div class="card border-info shadow-sm">
          <div class="card-body d-flex align-items-center justify-content-between">
            <div>
              <h5 class="card-title text-info">Revenue</h5>
              <h2 class="fw-bold">R {{ revenue }}</h2>
              <p class="text-muted mb-0">This month</p>
            </div>
            <font-awesome-icon :icon="icons.faMoneyBillWave" class="text-info display-4" />
          </div>
        </div>
      </div>
    </div>

    <div class="row mt-4">
      <div class="col-md-6">
        <div class="card shadow-sm overflow-y-auto max-h-96 ">
          <div class="card-body">
            <h5 class="card-title">Live Queue</h5>
            <ul>
              <li
                v-for="booking in bookings"
                :key="booking.id"
                class="p-2 border-b"
              >
                <strong>{{ booking.reference }} - {{ booking.client_name }}</strong>
                <br />
                Time Remaining: {{ formatTime(booking.timeRemaining) }}
              </li>
            </ul>
          </div>
        </div>
      </div>

      <div class="col-md-6">
        <div class="card shadow-sm">
          <div class="card-body">
            <h5 class="card-title">Quick Booking</h5>
            <div>
                <form @submit.prevent="submitForm">
                  <div class="row">
                        <div class="col-md-6 mt-3">
                          <label for="name" class="form-label">Name</label>
                          <input v-model="form.name" type="text" class="form-control" placeholder="Name">
                        </div>

                        <div class="col-md-6 mt-3">
                          <label for="phoneNumber" class="form-label">Phone Number (Optional)</label>
                          <input v-model="form.phoneNumber" type="text" class="form-control" placeholder="Phone Number">
                        </div>
          
                        <div class="col-md-6 mt-3">
                          <label for="email" class="form-label">Email (Optional)</label>
                          <input v-model="form.email" type="email" class="form-control" placeholder="Email">
                        </div>
                      
                        <div class="col-md-6 mt-3">
                          <label for="service_id" class="form-label">Select Service</label>
                          <select v-model="form.service_id" class="border rounded px-3 py-2 w-full">
                            <option value="" disabled>Select a service</option>
                            <option v-for="service in services" :key="service.id" :value="service.id">
                              {{ service.name }} - {{ service.duration }} mins 
                            </option>
                          </select>
                        </div>

                        <div class="col-md-6 mt-3">
                          <label for="barber_id" class="form-label">Preferred Barber</label>
                          <select v-model="form.barber_id" class="border rounded px-3 py-2 w-full">
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
    </div>
  </div>
</template>

<script>
import DashboardLayout from "../../../Layouts/DashboardLayout.vue";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { faUsers, faCalendarCheck, faCheckCircle, faMoneyBillWave } from "@fortawesome/free-solid-svg-icons";
import axios from "axios";

export default {
  components: { FontAwesomeIcon },
  data() {
    return {
      queueCount: 8, // Placeholder number of people in queue
      totalBookings: 120, // Total bookings in a month
      completedServices: 98, // Services completed
      revenue: 7500, // Revenue for the month in Rands
      icons: { faUsers, faCalendarCheck, faCheckCircle, faMoneyBillWave },
      bookings: [],
      services: [],
      barbers: [],
      form: {
        name: "",
        phoneNumber: "",
        email: "",
        service_id: "",
        barber_id: "",
      },
    };
  },
  methods: {
    async fetchBookings() {
      try {
        const response = await axios.get("/api/bookings");
        this.bookings = response.data;
      } catch (error) {
        console.error("Error fetching bookings:", error);
      }
    },

    async fetchServices() {
      try {
        const response = await axios.get("/api/services");
        this.services = response.data;
      } catch (error) {
        console.error("Error fetching services:", error);
      }
    },

    async fetchBarbers() {
      try {
        const response = await axios.get("/api/barbers");
        this.barbers = response.data;
      } catch (error) {
        console.error("Error fetching barbers:", error);
      }
    },

    formatTime(minutes) {
      const roundedMinutes = Math.max(0, Math.round(minutes));
      return `${Math.floor(roundedMinutes / 60)}h ${roundedMinutes % 60}m`;
    },

    async submitForm() {
      try {
        const response = await axios.post("/book", this.form);
        console.log("Booking successful:", response.data);

        // Clear form after successful submission
        this.form = {
          name: "",
          phoneNumber: "",
          email: "",
          service_id: "",
          barber_id: "",
        };

        // Refresh bookings list
        this.fetchBookings();
      } catch (error) {
        console.error("Error submitting booking:", error);
      }
    },
  },
  mounted() {
    this.fetchBookings();
    this.fetchServices();
    this.fetchBarbers();
    setInterval(this.fetchBookings, 30000);
  },
};
</script>

