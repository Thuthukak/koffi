<template>
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Bookings</h2>
  </div>
  <div>
    <!-- Desktop Table View -->
    <div class="d-none d-md-block">
      <table class="table table-bordered mt-4">
        <thead>
          <tr>
            <th>Reference</th>
            <th>Client</th>
            <th>Service</th>
            <th>Barber</th>
            <th>Duration (mins)</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(booking, index) in bookings" :key="booking.id">
            <td>{{ booking.reference }}</td>
            <td>{{ booking.client.name }}</td>
            <td>{{ booking.service.name }}</td>
            <td>{{ booking.barber.name }}</td>
            <td>{{ booking.service.duration }}</td>
            <td>
              <span :class="statusClass(booking.status)">{{ booking.status }}</span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Mobile Card View -->
    <div class="d-md-none">
      <div v-for="(booking, index) in bookings" :key="booking.id" class="booking-card mb-3">
        <div class="card">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-2">
              <h6 class="card-title mb-0">{{ booking.reference }}</h6>
              <span :class="statusClass(booking.status)">{{ booking.status }}</span>
            </div>
            
            <div class="booking-details">
              <div class="detail-row">
                <span class="detail-label">Client:</span>
                <span class="detail-value">{{ booking.client.name }}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Service:</span>
                <span class="detail-value">{{ booking.service.name }}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Barber:</span>
                <span class="detail-value">{{ booking.barber.name }}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Duration:</span>
                <span class="detail-value">{{ booking.service.duration }} mins</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import AdminBookingModal from "@/components/Admin/Dashboard/AdminBookingModal.vue";

export default {
  components: { AdminBookingModal },
  data() {
    return {
      bookings: [],
      showBookingModal: false
    };
  },
  mounted() {
    this.fetchBookings();
  },
  methods: {
    async fetchBookings() {
      try {
        const response = await axios.get("/api/bookings/data");
        this.bookings = response.data;
      } catch (error) {
        console.error("Error fetching bookings:", error);
      }
    },
    statusClass(status) {
      switch (status) {
        case "in-progress":
          return "badge bg-warning text-dark";
        case "queued":
          return "badge bg-primary";
        case "completed":
          return "badge bg-success";
        case "skipped":
          return "badge bg-danger";
        default:
          return "badge bg-secondary";
      }
    },
    openBookingModal() {
      this.showBookingModal = true;
    },
    closeBookingModal() {
      this.showBookingModal = false;
    },
  },
};
</script>

<style scoped>
/* Mobile card styling */
.booking-card {
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.booking-details {
  margin-top: 12px;
}

.detail-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 4px 0;
  border-bottom: 1px solid #f0f0f0;
}

.detail-row:last-child {
  border-bottom: none;
}

.detail-label {
  font-weight: 600;
  color: #666;
  font-size: 0.9rem;
}

.detail-value {
  font-weight: 500;
  text-align: right;
  flex: 1;
  margin-left: 12px;
}

/* Responsive table for tablets */
@media (max-width: 991.98px) and (min-width: 768px) {
  .table th,
  .table td {
    font-size: 0.875rem;
    padding: 0.5rem;
  }
}

/* Extra small screens optimization */
@media (max-width: 575.98px) {
  .card-body {
    padding: 1rem;
  }
  
  .detail-label,
  .detail-value {
    font-size: 0.85rem;
  }
  
  .card-title {
    font-size: 1rem;
  }
}

/* Ensure badges are properly sized on mobile */
@media (max-width: 767.98px) {
  .badge {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
  }
}
</style>