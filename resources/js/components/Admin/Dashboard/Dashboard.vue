<template>
  <div class="container-fluid mt-4">
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
        <div class="card shadow-sm overflow-y-auto max-h-96">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
              <h5 class="card-title">Live Queue</h5>
              <div>
                <div class="btn btn-danger mr-5" @click="clearQueue">Start</div>
                <div class="btn btn-warning mr-5" @click="skipQueue">Skip</div>
                <div class="btn btn-success" @click="nextQueue">Next</div> 
              </div>            
            </div>
            <table class="table table-striped mt-5">
              <thead>
                <tr>
                  <th>Reference</th>
                  <th>Client</th>
                  <th>Status</th>
                  <th>Time Remaining</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="booking in queue" :key="booking.reference">
                  <td>{{ booking.reference }}</td>
                  <td>{{ booking.name }}</td>
                  <td>
                    <span class="badge" :class="getStatusClass(booking.status)">
                      {{ booking.status }}
                    </span>
                  </td>
                  <td>{{ formatTime(booking.time_remaining) }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="col-md-6">
        <div class="card shadow-sm">
          <div class="card-body">
            <h5 class="card-title">Walk-in Bookings</h5>
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
import { ref, onMounted } from "vue";

export default {
  components: { FontAwesomeIcon },
  data() {
    return {
      queueCount: 8, // Placeholder number of people in queue
      totalBookings: 120, // Total bookings in a month
      completedServices: 98, // Services completed
      revenue: 7500, // Revenue for the month in Rands
      icons: { faUsers, faCalendarCheck, faCheckCircle, faMoneyBillWave },
      queue: [],
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

    async skipQueue() {
      try {
        await axios.get("/admin/skip-booking/{bookingId}");
        this.fetchQueue();
      } catch (error) {
        console.error("Error skipping booking:", error);
      }
    },
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

    async fetchQueue() {
      try {
        const response = await axios.get("/api/queue");
        this.queue = response.data;
      } catch (error) {
        console.error("Error fetching queue:", error);
      }
    },

    formatTime(minutes) {
      if (minutes <= 0) return "Now";
      let hours = Math.floor(minutes / 60);
      let mins = minutes % 60;
      return `${hours}h ${mins}m`;
    },

    getStatusClass(status) {
      return {
        "badge-primary": status === "queued",
        "badge-success": status === "in-progress",
        "badge-danger": status === "skipped",
        "badge-secondary": status === "completed",
      };
    },

    updateTimeRemaining() {
      this.queue = this.queue.map((booking) => ({
        ...booking,
        time_remaining: Math.max(0, booking.time_remaining - 1),
      }));
    },

    async submitForm() {
      try {
        const response = await axios.post("/book-walkins", this.form);
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
    this.fetchQueue();
    setInterval(this.fetchQueue, 30000);
    setInterval(this.updateTimeRemaining, 60000);

  },
};
</script>
<style scoped>
.badge {
  padding: 5px 10px;
  border-radius: 5px;
  font-size: 14px;
}
.badge-primary {
  background-color: blue;
  color: white;
}
.badge-success {
  background-color: green;
  color: white;
}
.badge-danger {
  background-color: red;
  color: white;
}
.badge-secondary {
  background-color: gray;
  color: white;
}
</style>

