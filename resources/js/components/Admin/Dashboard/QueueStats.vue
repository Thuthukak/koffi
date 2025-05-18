<template>
  <div class="p-4">
    <h1 class="text-2xl font-bold mb-6">Queue Dashboard</h1>
    
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
      <div class="p-4 bg-blue-50 rounded-md shadow">
        <h2 class="text-lg font-semibold text-blue-800">Clients in Queue</h2>
        <p class="text-3xl font-bold">{{ waitingClients.length }}</p>
        <p class="text-sm text-gray-600">waiting to be served</p>
      </div>
      
      <div class="p-4 bg-green-50 rounded-md shadow">
        <h2 class="text-lg font-semibold text-green-800">Avg. Service Time</h2>
        <p class="text-3xl font-bold">{{ averageServiceTime }} min</p>
        <p class="text-sm text-gray-600">per client</p>
      </div>
      
      <div class="p-4 bg-indigo-50 rounded-md shadow">
        <h2 class="text-lg font-semibold text-indigo-800">Total Queue Time</h2>
        <p class="text-3xl font-bold">{{ totalQueueTime }}</p>
        <p class="text-sm text-gray-600">estimated wait</p>
      </div>
    </div>
    
    <!-- Queue Status -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <!-- Current Client -->
      <div v-if="currentClient" class="border rounded-md p-4 bg-indigo-50 col-span-full">
        <h2 class="text-xl font-bold mb-4">Currently Serving</h2>
        <div class="flex justify-between items-center">
          <div>
            <p class="text-lg font-semibold">{{ currentClient.client.name }}</p>
            <p>{{ currentClient.service.name }} ({{ currentClient.service.duration }} mins)</p>
            <p class="text-sm">Ref: {{ currentClient.reference }}</p>
            <p class="text-sm text-gray-600">Started at: {{ formatDateTime(currentClient.start_time) }}</p>
          </div>
          <div>
            <p v-if="currentClientStatus.isOvertime" class="text-red-500 font-bold">
              Overtime: {{ formatTime(currentClientStatus.timeDifference) }}
            </p>
            <p v-else class="text-green-600 font-bold">
              Remaining: {{ formatTime(currentClientStatus.timeDifference) }}
            </p>
          </div>
        </div>
        
        <div class="mt-4">
          <div class="w-full bg-gray-200 rounded-full h-2.5">
            <div 
              class="h-2.5 rounded-full" 
              :class="progressBarColor" 
              :style="{ width: progressPercentage + '%' }"
            ></div>
          </div>
          <div class="flex justify-between text-xs mt-1">
            <span>0:00</span>
            <span>{{ currentClient.service.duration }} min</span>
          </div>
        </div>
      </div>
      
      <!-- Waiting List -->
      <div class="border rounded-md p-4 overflow-y-auto max-h-96 col-span-full">
        <h2 class="text-xl font-bold mb-4">Waiting List</h2>
        <div v-if="waitingClients.length === 0" class="text-gray-500 text-center py-4">
          No clients in queue
        </div>
        <div v-else class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Wait Time</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="(client, index) in waitingClientsWithTimes" :key="client.id">
                <td class="px-6 py-4 whitespace-nowrap">{{ index + 1 }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ client.client.name }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ client.service.name }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ formatTime(client.waitTime) }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ client.reference }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      
      <!-- Today's Completed Services -->
      <div class="border rounded-md p-4 col-span-full">
        <h2 class="text-xl font-bold mb-4">Completed Services Today</h2>
        <div v-if="completedBookings.length === 0" class="text-gray-500 text-center py-4">
          No services completed today
        </div>
        <div v-else class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Start Time</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">End Time</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="booking in completedBookings" :key="booking.id">
                <td class="px-6 py-4 whitespace-nowrap">{{ booking.client.name }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ booking.service.name }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ formatDateTime(booking.start_time) }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ formatDateTime(booking.end_time) }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ booking.actual_duration }} min</td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <button 
                    @click="reopenBooking(booking.id)" 
                    class="text-indigo-600 hover:text-indigo-900"
                  >
                    Reopen
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import axios from 'axios';

export default {
  setup() {
    // State variables
    const currentClient = ref(null);
    const waitingClients = ref([]);
    const completedBookings = ref([]);
    const timerInterval = ref(null);
    const currentTime = ref(new Date());
    const processedStartTime = ref(null);
    const serverTimeOffset = ref(0);
    
    // Fetch server time for synchronization
    const fetchServerTime = async () => {
      try {
        const response = await axios.get("/api/server-time");
        if (response.data && response.data.time) {
          // Calculate offset between server and client time
          const serverTime = new Date(response.data.time);
          const clientTime = new Date();
          serverTimeOffset.value = serverTime.getTime() - clientTime.getTime();
          console.log("Server time offset (ms):", serverTimeOffset.value);
        }
      } catch (error) {
        console.error("Error fetching server time:", error);
      }
    };
    
    // Helper function to safely parse a start time with timezone consideration
    const parseStartTime = (rawStartTime) => {
      if (!rawStartTime) {
        console.warn("No start time provided");
        return null;
      }
      
      let parsedTime;
      
      // First try parsing directly as it is
      try {
        parsedTime = new Date(rawStartTime);
        if (!isNaN(parsedTime.getTime())) {
          return parsedTime;
        }
      } catch (e) {
        console.warn("Failed to parse as date string:", e);
      }
      
      // Try parsing with timezone adjustment if needed
      try {
        if (typeof rawStartTime === 'string' && rawStartTime.match(/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/)) {
          const isoString = rawStartTime.replace(' ', 'T');
          parsedTime = new Date(isoString);
          
          if (!isNaN(parsedTime.getTime())) {
            return parsedTime;
          }
        }
      } catch (e) {
        console.warn("Failed to parse as local ISO format:", e);
      }
      
      // Try parsing as timestamp
      try {
        const timestamp = parseInt(rawStartTime);
        if (!isNaN(timestamp)) {
          if (timestamp.toString().length <= 10) {
            parsedTime = new Date(timestamp * 1000);
          } else {
            parsedTime = new Date(timestamp);
          }
          
          if (!isNaN(parsedTime.getTime())) {
            return parsedTime;
          }
        }
      } catch (e) {
        console.warn("Failed to parse as timestamp:", e);
      }
      
      // Last resort - use current time
      const fallbackTime = new Date();
      fallbackTime.setSeconds(fallbackTime.getSeconds() - 5);
      return fallbackTime;
    };
    
    // Calculate elapsed minutes since service start
    const calculateElapsedMinutes = () => {
      if (!processedStartTime.value) {
        return 0;
      }
      
      // Calculate elapsed milliseconds
      const elapsedMs = currentTime.value.getTime() - processedStartTime.value.getTime();
      
      // Convert to minutes and ensure it's not negative
      return Math.max(0, Math.floor(elapsedMs / 60000));
    };
    
    // Fetch queue data
    const fetchQueue = async () => {
      try {
        const response = await axios.get("/api/queue/current");
        
        // Update the data references
        currentClient.value = response.data.current;
        waitingClients.value = response.data.waiting;
        
        // Process start time right after receiving data
        if (currentClient.value && currentClient.value.start_time) {
          processedStartTime.value = parseStartTime(currentClient.value.start_time);
        } else {
          processedStartTime.value = null;
        }
        
        // Update current time when data is fetched
        currentTime.value = new Date();
      } catch (error) {
        console.error("Error fetching queue:", error);
      }
    };
    
    // Current client status computation
    const currentClientStatus = computed(() => {
      const result = {
        isOvertime: false,
        timeDifference: 0
      };
      
      if (!currentClient.value || !processedStartTime.value) {
        return result;
      }
      
      // Parse service duration as an integer
      const serviceDuration = parseInt(currentClient.value.service.duration || 0);
      if (!serviceDuration) {
        return result;
      }
      
      // Calculate elapsed time in minutes
      const elapsedMinutes = calculateElapsedMinutes();
      
      // Check if we're in overtime
      if (elapsedMinutes >= serviceDuration) {
        // We're in overtime
        return {
          isOvertime: true,
          timeDifference: elapsedMinutes - serviceDuration
        };
      } else {
        // Still have time left
        return {
          isOvertime: false,
          timeDifference: serviceDuration - elapsedMinutes
        };
      }
    });
    
    // Computed waiting clients with calculated wait times
    const waitingClientsWithTimes = computed(() => {
      if (waitingClients.value.length === 0) {
        return [];
      }
      
      let cumulativeWaitTime = 0;
      
      // Start with current client's remaining time if applicable
      if (currentClient.value && currentClientStatus.value && !currentClientStatus.value.isOvertime) {
        cumulativeWaitTime = currentClientStatus.value.timeDifference;
      }
      
      // Clone the waiting clients array to avoid mutating the original
      return waitingClients.value.map((client, index) => {
        const clientCopy = { ...client };
        
        // Add current wait time before adding this client's duration
        clientCopy.waitTime = cumulativeWaitTime;
        
        // Add this client's service duration to the cumulative wait time
        const serviceDuration = parseInt(client.service.duration || 0);
        cumulativeWaitTime += serviceDuration;
        
        return clientCopy;
      });
    });
    
    // Fetch completed bookings for statistics
    const fetchCompletedBookings = async () => {
      try {
        const response = await axios.get('/api/bookings/completed-today');
        completedBookings.value = response.data;
      } catch (error) {
        console.error("Error fetching completed bookings:", error);
      }
    };
    
    // Reopen a completed booking
    const reopenBooking = async (id) => {
      try {
        await axios.post(`/api/queue/reopen/${id}`);
        await Promise.all([
          fetchQueue(),
          fetchCompletedBookings()
        ]);
      } catch (error) {
        console.error("Error reopening booking:", error);
      }
    };
    
    // Update current time every second with server offset consideration
    const startTimeUpdates = () => {
      timerInterval.value = setInterval(() => {
        const now = new Date();
        
        // Apply server time offset if available
        if (serverTimeOffset.value !== 0) {
          now.setTime(now.getTime() + serverTimeOffset.value);
        }
        
        currentTime.value = now;
      }, 1000); // Update every second for more responsive UI
    };
    
    // Computed properties for statistics
    const averageServiceTime = computed(() => {
      if (!completedBookings.value || completedBookings.value.length === 0) return 0;
      
      const totalDuration = completedBookings.value.reduce((sum, booking) => {
        return sum + (booking.actual_duration || 0);
      }, 0);
      
      return Math.round(totalDuration / completedBookings.value.length);
    });
    
    const totalQueueTime = computed(() => {
      if (!waitingClientsWithTimes.value || waitingClientsWithTimes.value.length === 0) {
        return '0h 0m';
      }
      
      // Get the last client's wait time and add their service duration
      const lastClient = waitingClientsWithTimes.value[waitingClientsWithTimes.value.length - 1];
      const lastClientServiceDuration = parseInt(lastClient.service.duration || 0);
      const totalWaitMinutes = lastClient.waitTime + lastClientServiceDuration;
      
      return formatTime(totalWaitMinutes);
    });
    
    // Computed property for progress bar
    const progressPercentage = computed(() => {
      if (!currentClient.value || !processedStartTime.value) {
        return 0;
      }
      
      const serviceDuration = parseInt(currentClient.value.service.duration || 0);
      if (!serviceDuration) {
        return 0;
      }
      
      const elapsedMinutes = calculateElapsedMinutes();
      const percentage = (elapsedMinutes / serviceDuration) * 100;
      
      return Math.min(100, Math.max(0, percentage));
    });
    
    // Computed property for progress bar color
    const progressBarColor = computed(() => {
      if (!currentClient.value) return 'bg-gray-300';
      
      if (currentClientStatus.value.isOvertime) {
        return 'bg-red-500';
      }
      
      const percentage = progressPercentage.value;
      if (percentage < 50) {
        return 'bg-green-500';
      } else if (percentage < 80) {
        return 'bg-yellow-500';
      } else {
        return 'bg-orange-500';
      }
    });
    
    // Helper functions
    const formatTime = (minutes) => {
      // Ensure minutes is a number and not negative
      const roundedMinutes = Math.max(0, Math.round(minutes || 0));
      return `${Math.floor(roundedMinutes / 60)}h ${roundedMinutes % 60}m`;
    };
    
    const formatDateTime = (datetime) => {
      if (!datetime) return '';
      // Parse the date string
      const date = new Date(datetime);
      // Format it for display - hours and minutes only
      return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    };
    
    // Setup websocket connection for real-time updates
    const setupWebsocket = () => {
      if (window.Echo) {
        window.Echo.channel('queue')
          .listen('QueueUpdated', () => {
            fetchQueue();
            fetchCompletedBookings();
          });
      }
    };
    
    // Lifecycle hooks
    onMounted(async () => {
      // First get server time to calibrate our clock
      await fetchServerTime();
      // Then fetch queue data
      await fetchQueue();
      fetchCompletedBookings();
      setupWebsocket();
      startTimeUpdates();
      
      // Log timezone information to help diagnose date issues
      console.log("Browser timezone:", Intl.DateTimeFormat().resolvedOptions().timeZone);
    });
    
    onUnmounted(() => {
      if (timerInterval.value) {
        clearInterval(timerInterval.value);
      }
      
      // Cleanup Echo listener if needed
      if (window.Echo) {
        window.Echo.leave('queue');
      }
    });
    
    return {
      currentClient,
      waitingClients,
      waitingClientsWithTimes,
      completedBookings,
      currentClientStatus,
      averageServiceTime,
      totalQueueTime,
      progressPercentage,
      progressBarColor,
      formatTime,
      formatDateTime,
      reopenBooking
    };
  }
};
</script>