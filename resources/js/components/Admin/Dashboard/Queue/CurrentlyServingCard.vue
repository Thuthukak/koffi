<template>
  <div v-if="currentClient" class="bg-white rounded-xl shadow-sm border overflow-hidden">
    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-4 sm:px-6 py-4">
      <h2 class="text-lg sm:text-xl font-bold text-white flex items-center gap-2">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
        </svg>
        Currently Serving
      </h2>
    </div>
    
    <div class="p-4 sm:p-6">
      <div class="flex flex-col lg:flex-row lg:justify-between lg:items-start gap-4">
        <div class="space-y-2 flex-1">
          <h3 class="text-xl sm:text-2xl font-bold text-gray-900">{{ currentClient.client.name }}</h3>
          <div class="space-y-1">
            <p class="text-sm sm:text-base text-gray-700 flex items-center gap-2">
              <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v4a2 2 0 002 2h2m0 0h10a2 2 0 002-2V7a2 2 0 00-2-2H9m0 0V3"/>
              </svg>
              {{ currentClient.service.name }} ({{ currentClient.service.duration }} mins)
            </p>
            <p class="text-xs sm:text-sm text-gray-500 flex items-center gap-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
              </svg>
              Ref: {{ currentClient.reference }}
            </p>
            <p class="text-xs sm:text-sm text-gray-500 flex items-center gap-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              Started: {{ formatDateTime(currentClient.start_time) }}
            </p>
          </div>
        </div>
        
        <div class="lg:text-right">
          <div v-if="currentClientStatus.isOvertime" 
               class="inline-flex items-center gap-2 px-3 py-2 bg-red-100 text-red-800 rounded border border-red-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
              <p class="text-xs font-medium">OVERTIME</p>
              <p class="text-lg font-bold">{{ formatTime(currentClientStatus.timeDifference) }}</p>
            </div>
          </div>
          <div v-else 
               class="inline-flex items-center gap-2 px-3 py-2 bg-green-100 text-green-800 rounded border border-green-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
              <p class="text-xs font-medium">REMAINING</p>
              <p class="text-lg font-bold">{{ formatTime(currentClientStatus.timeDifference) }}</p>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Optional: Progress bar -->
      <div v-if="showProgressBar" class="mt-4">
        <div class="flex justify-between text-sm text-gray-600 mb-1">
          <span>Progress</span>
          <span>{{ progressPercentage }}%</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2">
          <div 
            class="h-2 rounded-full transition-all duration-300"
            :class="progressBarColor"
            :style="{ width: progressPercentage + '%' }"
          ></div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { computed, watch } from 'vue';

export default {
  name: "CurrentlyServingCard",
  props: {
    currentClient: {
      type: Object,
      default: null
    },
    currentClientStatus: {
      type: Object,
      default: () => ({ isOvertime: false, timeDifference: 0 })
    },
    showProgressBar: {
      type: Boolean,
      default: false
    }
  },
  setup(props) {
    // Helper function to format time
    const formatTime = (minutes) => {
      const roundedMinutes = Math.max(0, Math.round(minutes || 0));
      return `${Math.floor(roundedMinutes / 60)}h ${roundedMinutes % 60}m`;
    };

    // Helper function to format date/time
    const formatDateTime = (datetime) => {
      if (!datetime) return '';
      const date = new Date(datetime);
      return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    };

    // Calculate progress percentage
    const progressPercentage = computed(() => {
      if (!props.currentClient || !props.currentClientStatus) return 0;
      
      const serviceDuration = parseInt(props.currentClient.service?.duration || 0);
      if (!serviceDuration) return 0;
      
      if (props.currentClientStatus.isOvertime) {
        // If overtime, show 100%
        return 100;
      } else {
        // Calculate percentage of time elapsed
        const remainingTime = props.currentClientStatus.timeDifference;
        const elapsedTime = serviceDuration - remainingTime;
        return Math.min(100, Math.max(0, (elapsedTime / serviceDuration) * 100));
      }
    });

    // Dynamic progress bar color based on progress
    const progressBarColor = computed(() => {
      if (props.currentClientStatus.isOvertime) {
        return 'bg-red-500';
      } else if (progressPercentage.value >= 80) {
        return 'bg-yellow-500';
      } else {
        return 'bg-green-500';
      }
    });

    return {
      formatTime,
      formatDateTime,
      progressPercentage,
      progressBarColor
    };
  }
};
</script>