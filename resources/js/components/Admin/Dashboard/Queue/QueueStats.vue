<template>
  <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
    <!-- In Queue Statistic -->
    <div class="bg-white rounded-xl shadow-sm border p-4 sm:p-6">
      <div class="flex items-center gap-3">
        <div class="p-3 bg-blue-100 rounded">
          <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
          </svg>
        </div>
        <div>
          <p class="text-2xl sm:text-3xl font-bold text-gray-900">{{ queueCount }}</p>
          <p class="text-xs sm:text-sm text-gray-600 font-medium">In Queue</p>
        </div>
      </div>
    </div>
    
    <!-- Average Service Time Statistic -->
    <div class="bg-white rounded-xl shadow-sm border p-4 sm:p-6">
      <div class="flex items-center gap-3">
        <div class="p-3 bg-green-100 rounded">
          <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </div>
        <div>
          <p class="text-2xl sm:text-3xl font-bold text-gray-900">{{ averageServiceTime }}</p>
          <p class="text-xs sm:text-sm text-gray-600 font-medium">Avg Time (min)</p>
        </div>
      </div>
    </div>
    
    <!-- Total Queue Time Statistic -->
    <div class="bg-white rounded-xl shadow-sm border p-4 sm:p-6">
      <div class="flex items-center gap-3">
        <div class="p-3 bg-indigo-100 rounded">
          <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
          </svg>
        </div>
        <div>
          <p class="text-lg sm:text-xl font-bold text-gray-900">{{ totalQueueTime }}</p>
          <p class="text-xs sm:text-sm text-gray-600 font-medium">Total Queue Time</p>
        </div>
      </div>
    </div>

    <!-- Optional: Additional Statistics -->
    <!-- <div v-if="showAdditionalStats" class="bg-white rounded-xl shadow-sm border p-4 sm:p-6">
      <div class="flex items-center gap-3">
        <div class="p-3 bg-purple-100 rounded">
          <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
          </svg>
        </div>
        <div>
          <p class="text-2xl sm:text-3xl font-bold text-gray-900">{{ completedToday }}</p>
          <p class="text-xs sm:text-sm text-gray-600 font-medium">Completed Today</p>
        </div>
      </div>
    </div> -->

    <!-- <div v-if="showAdditionalStats" class="bg-white rounded-xl shadow-sm border p-4 sm:p-6">
      <div class="flex items-center gap-3">
        <div class="p-3 bg-yellow-100 rounded">
          <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
          </svg>
        </div>
        <div>
          <p class="text-2xl sm:text-3xl font-bold text-gray-900">{{ formatCurrency(todayRevenue) }}</p>
          <p class="text-xs sm:text-sm text-gray-600 font-medium">Today's Revenue</p>
        </div>
      </div>
    </div> -->
  </div>
</template>

<script>
import { computed } from 'vue';

export default {
  name: "QueueStatistics",
  props: {
    queueCount: {
      type: Number,
      default: 0
    },
    averageServiceTime: {
      type: [Number, String],
      default: 0
    },
    totalQueueTime: {
      type: String,
      default: '0h 0m'
    },
    // completedBookings: {
    //   type: Array,
    //   default: () => []
    // },
    showAdditionalStats: {
      type: Boolean,
      default: false
    }
  },
  setup(props) {
    // Calculate completed bookings today
    const completedToday = computed(() => {
      return props.completedBookings.length;
    });

    // Calculate today's revenue
    const todayRevenue = computed(() => {
      return props.completedBookings.reduce((total, booking) => {
        return total + (parseFloat(booking.service?.price || 0));
      }, 0);
    });

    // Format currency
    const formatCurrency = (amount) => {
      return `R${parseFloat(amount).toFixed(2)}`;
    };

    return {
      completedToday,
      todayRevenue,
      formatCurrency
    };
  }
};
</script>