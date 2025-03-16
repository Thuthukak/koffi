<template>
  <Layout>
    <!-- Hero Section -->
    <section class="hero position-relative text-white text-center">
      <div class="banner-image"></div>
      <div class="overlay-content position-absolute top-50 start-50 translate-middle text-center">
        <h1 class="display-4 fw-bold mb-4">Strengthening Advancements in Potential and Excellence</h1>
        <p class="lead mb-5">Promoting growth and development through education, enhanced opportunities, and the connection of talent to the global market</p>
        <button 
          @click="openBookingModal" 
          class="btn btn-light btn-lg px-5 py-3 rounded-pill shadow">
          Book Now
        </button>
      </div>
    </section>

    <!-- Booking Modal -->
    <BookingModal 
      :isOpen="showBookingModal" 
      @close="closeBookingModal" 
      @booking-created="handleBookingSuccess"
    />

    <BookingSummary
      v-if="showSummary"
      :booking-details="bookingData"
    />
  </Layout>
</template>

<script>
import Layout from "../../Layouts/HomeLayout.vue";
import BookingModal from "@/components/Home/BookingModal.vue";
import BookingSummary from '@/components/Home/BookingSummary.vue';
import { ref } from "vue"; 

export default {
  components: {
    Layout,
    BookingModal,
    BookingSummary
  },

  setup() {
    const showBookingModal = ref(false);
    const showSummary = ref(false);
    const bookingData = ref(null);

    const openBookingModal = () => {
      showBookingModal.value = true;
    };

    const handleBookingSuccess = (bookingDetails) => {
      console.log('Booking created:', bookingData);
      bookingData.value = bookingDetails;
      showSummary.value = true;
    }

    const closeBookingModal = () => {
      showBookingModal.value = false;
    };

    return {
      showBookingModal,
      openBookingModal,
      closeBookingModal,
      showSummary,
      bookingData,
      handleBookingSuccess
    };
  }
};
</script>

<style scoped>
.hero {
  height: 90vh;
  overflow: hidden;
  position: relative;
}

.banner-image {
  background: linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.3)),
              url("/public/assets/images/hero.jpg") center/cover no-repeat;
  height: 100%;
  width: 100%;
  position: absolute;
  top: 0;
  left: 0;
  animation: zoomEffect 20s infinite alternate;
}

.overlay-content {
  width: 90%;
  max-width: 800px;
  position: relative;
  z-index: 2;
}

.btn-light {
  background-color: rgba(255, 255, 255, 0.9);
  border: none;
  transition: all 0.3s ease;
  font-weight: 600;
  color: #333;
}

.btn-light:hover {
  background-color: white;
  transform: translateY(-2px);
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
}

@keyframes zoomEffect {
  0% { transform: scale(1); }
  100% { transform: scale(1.05); }
}

@media (max-width: 768px) {
  .hero {
    height: 70vh;
  }
  
  .display-4 {
    font-size: 2.5rem;
  }
  
  .lead {
    font-size: 1.1rem;
  }
}
</style>