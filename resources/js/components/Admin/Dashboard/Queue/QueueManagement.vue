<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50 p-2 sm:p-4 lg:p-6">
    <div class="max-w-7xl mx-auto space-y-4 sm:space-y-6">
      <!-- Header -->
      <div class="text-center sm:text-left">
        <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900 mb-2">
          Queue Management
        </h1>
      </div>
      
      <!-- Control Panel -->
      <div class="bg-white rounded-xl shadow-sm border p-4 sm:p-6">
        <div class="flex flex-col sm:flex-row gap-3 sm:gap-4">
          <button 
            @click="startQueue" 
            class="flex-1 sm:flex-none px-4 py-3 bg-gradient-to-r from-green-500 to-green-600 text-white rounded font-medium shadow-md hover:shadow-lg hover:from-green-600 hover:to-green-700 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
            :disabled="queueActive"
          >
            <span class="flex items-center justify-center gap-2">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h1m4 0h1M9 16h1m4 0h1M12 3l7 4v10l-7 4-7-4V7l7-4z"/>
              </svg>
              Start Queue
            </span>
          </button>
          
          <button 
            @click="nextClient" 
            class="flex-1 sm:flex-none px-4 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded font-medium shadow-md hover:shadow-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
            :disabled="!queueActive"
          >
            <span class="flex items-center justify-center gap-2">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
              </svg>
              Next Client
            </span>
          </button>
          
          <button 
            @click="openAddClientModal" 
            class="flex-1 sm:flex-none px-4 py-3 bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded font-medium shadow-md hover:shadow-lg hover:from-purple-600 hover:to-purple-700 transition-all duration-200"
          >
            <span class="flex items-center justify-center gap-2">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
              </svg>
              Add Walk-in
            </span>
          </button>
        </div>
      </div>
      
      <!-- Current Client Card -->
      <CurrentlyServingCard 
        :currentClient="currentClient"
        :currentClientStatus="currentClientStatus"
        :showProgressBar="true"
      />
      
      <!-- Queue Statistics -->
      <QueueStats 
        :queueCount="waitingClients.length"
        :averageServiceTime="averageServiceTime"
        :totalQueueTime="totalQueueTime"
        :showAdditionalStats="true"
      />
      
      <!-- Waiting List - Mobile Optimized -->
       <WaitingList 
        :waitingClients="waitingClientsWithTimes"
        title="Current Queue"
        :showBarberColumn="true"
        :showPositionControls="true"
        :showEditButton="true"
        @removeClient="removeClient"
       
        
      />
      
    </div>
    
    <!-- Add Walk-in Client Modal -->
    <div v-if="showAddClientModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white p-6 mx-4 rounded w-full max-w-lg">
        <h2 class="text-xl font-bold mb-4">Add Walk-in Client</h2>
        
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700">Name</label>
          <input 
            v-model="newClient.name" 
            type="text" 
            class="mt-1 block w-full border rounded shadow-sm"
          >
        </div>
        
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700">Phone Number</label>
          <input 
            v-model="newClient.phoneNumber" 
            type="text" 
            class="mt-1 block w-full border rounded shadow-sm"
          >
        </div>
        
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700">Email</label>
          <input 
            v-model="newClient.email" 
            type="email" 
            class="mt-1 block w-full border rounded shadow-sm"
          >
        </div>
        
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700">Service</label>
          <select 
            v-model="newClient.service_id" 
            class="mt-1 block bg-white w-full border rounded shadow-sm"
          >
            <option v-for="service in services" :key="service.id" :value="service.id">
              {{ service.name }} ({{ service.duration }} min) - {{ formatPrice(service.price) }}
            </option>
          </select>
        </div>
        
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700">Barber</label>
          <select 
            v-model="newClient.barber_id" 
            class="mt-1 block w-full bg-white border rounded shadow-sm"
          >
            <option v-for="barber in barbers" :key="barber.id" :value="barber.id">
              {{ barber.name }}
            </option>
          </select>
        </div>
        
        <div class="flex justify-end gap-3 space-x-3">
          <button 
            @click="closeAddClientModal" 
            class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400"
          >
            Cancel
          </button>
          <button 
            @click="addWalkInClient" 
            class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
          >
            Add to Queue
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import axios from 'axios';
import CurrentlyServingCard from './CurrentlyServingCard.vue';
import QueueStats from './QueueStats.vue';
import WaitingList from './WaitingList.vue';


export default {
  components: {
    CurrentlyServingCard,
    QueueStats,
    WaitingList
  },
  setup() {
    // State variables 
    const currentClient = ref(null);
    const waitingClients = ref([]);
    const queueActive = ref(false);
    const timerInterval = ref(null);
    const showAddClientModal = ref(false);
    const services = ref([]);
    const barbers = ref([]);
    const completedBookings = ref([]);
    const currentTime = ref(new Date());
    
    // Store the processed start time to ensure consistent calculations
    const processedStartTime = ref(null);
    const serverTimeOffset = ref(0); // Track server-client time difference
    
    const newClient = ref({
      name: '',
      phoneNumber: '',
      email: '',
      service_id: '',
      barber_id: ''
    });
    
    // Helper function to safely parse a start time with timezone consideration
    const parseStartTime = (rawStartTime) => {
      // Debug the raw input
      console.log("Parsing start time:", rawStartTime, typeof rawStartTime);
      
      if (!rawStartTime) {
        console.warn("No start time provided");
        return null;
      }
      
      let parsedTime;
      
      // First try parsing directly as it is - assumes server time strings include timezone
      try {
        parsedTime = new Date(rawStartTime);
        if (!isNaN(parsedTime.getTime())) {
          console.log("Successfully parsed as date string:", parsedTime);
          return parsedTime;
        }
      } catch (e) {
        console.warn("Failed to parse as date string:", e);
      }
      
      // If the server time string doesn't include timezone (like "2025-05-17 19:28:19"),
      // we need to handle it as a server-local time
      try {
        // Try to parse with timezone adjustment if needed
        // This is for formats like "2025-05-17 19:28:19" which don't include timezone
        if (typeof rawStartTime === 'string' && rawStartTime.match(/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/)) {
          // Replace space with 'T' for ISO format and add timezone identifier
          // Use the server's timezone which is assumed to be the same as specified in PHP config
          const isoString = rawStartTime.replace(' ', 'T');
          parsedTime = new Date(isoString);
          
          if (!isNaN(parsedTime.getTime())) {
            console.log("Successfully parsed as local ISO format:", parsedTime);
            return parsedTime;
          }
        }
      } catch (e) {
        console.warn("Failed to parse as local ISO format:", e);
      }
      
      // Try parsing as timestamp (seconds or milliseconds)
      try {
        const timestamp = parseInt(rawStartTime);
        if (!isNaN(timestamp)) {
          // Determine if seconds (10 digits) or milliseconds (13 digits)
          if (timestamp.toString().length <= 10) {
            parsedTime = new Date(timestamp * 1000);
          } else {
            parsedTime = new Date(timestamp);
          }
          
          if (!isNaN(parsedTime.getTime())) {
            console.log("Successfully parsed as timestamp:", parsedTime);
            return parsedTime;
          }
        }
      } catch (e) {
        console.warn("Failed to parse as timestamp:", e);
      }
      
      // Last resort - use current time to prevent immediate overtime
      console.warn("Could not parse start time, using current time as fallback");
      const fallbackTime = new Date();
      fallbackTime.setSeconds(fallbackTime.getSeconds() - 5); // 5 seconds ago to prevent immediate overtime
      return fallbackTime;
    };
    
    // Calculate elapsed minutes since service start with timezone consideration
    const calculateElapsedMinutes = () => {
      if (!processedStartTime.value) {
        return 0;
      }
      
      // Calculate elapsed milliseconds
      const elapsedMs = currentTime.value.getTime() - processedStartTime.value.getTime();
      console.log("Start time:", processedStartTime.value.toISOString());
      console.log("Current time:", currentTime.value.toISOString());
      console.log("Time difference (ms):", elapsedMs);
      
      // Convert to minutes and ensure it's not negative
      return Math.max(0, Math.floor(elapsedMs / 60000));
    };
    
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
    
    // Fetch queue data
    const fetchQueue = async () => {
      try {
        const response = await axios.get("/api/queue/current");
        console.log("Raw queue data from backend:", response.data);
        
        // Update the data references
        currentClient.value = response.data.current;
        waitingClients.value = response.data.waiting;
        queueActive.value = response.data.queueActive;
        
        // Process start time right after receiving data
        if (currentClient.value && currentClient.value.start_time) {
          console.log("Start time from API:", currentClient.value.start_time);
          processedStartTime.value = parseStartTime(currentClient.value.start_time);
          console.log("Processed start time:", processedStartTime.value);
        } else {
          processedStartTime.value = null;
        }
        
        // Update current time when data is fetched
        currentTime.value = new Date();
        console.log("Current time updated:", currentTime.value);
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
      console.log("Elapsed minutes:", elapsedMinutes);
      
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
      
      console.log("Initial wait time for queue:", cumulativeWaitTime);
      
      // Clone the waiting clients array to avoid mutating the original
      return waitingClients.value.map((client, index) => {
        const clientCopy = { ...client };
        
        // Add current wait time before adding this client's duration
        clientCopy.waitTime = cumulativeWaitTime;
        
        // Add this client's service duration to the cumulative wait time
        // Parse service duration as an integer
        const serviceDuration = parseInt(client.service.duration || 0);
        cumulativeWaitTime += serviceDuration;
        
        return clientCopy;
      });
    });
    
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
    
    // Fetch services and barbers for the form
    const fetchFormData = async () => {
      try {
        const [servicesResponse, barbersResponse] = await Promise.all([
          axios.get('/api/services'),
          axios.get('/api/barbers')
        ]);
        
        services.value = servicesResponse.data;
        barbers.value = barbersResponse.data;
      } catch (error) {
        console.error("Error fetching form data:", error);
      }
    };
    
    // Queue management functions
    const startQueue = async () => {
      try {
        console.log("Starting queue...");
        const response = await axios.post("/api/queue/start");
        console.log("Queue start response:", response.data);
        
        // Immediately process the current client's start time if it's returned directly
        if (response.data && response.data.current && response.data.current.start_time) {
          console.log("Start time from queue start response:", response.data.current.start_time);
          processedStartTime.value = parseStartTime(response.data.current.start_time);
        }
        
        await fetchQueue();
      } catch (error) {
        console.error("Error starting queue:", error);
      }
    };

    const nextClient = async () => {
      try {
        console.log("Moving to next client...");
        const response = await axios.post("/api/queue/next");
        console.log("Next client response:", response.data);
        
        // Immediately process the current client's start time if it's returned directly
        if (response.data && response.data.current && response.data.current.start_time) {
          console.log("Start time from next client response:", response.data.current.start_time);
          processedStartTime.value = parseStartTime(response.data.current.start_time);
        }
        
        await fetchQueue();
      } catch (error) {
        console.error("Error moving to next client:", error);
      }
    };

    // Updated remove client function (previously skipClient)
    const removeClient = async (id) => {
      try {
        console.log("Removing client with ID:", id);
        
        // Show confirmation dialog
        if (!confirm('Are you sure you want to remove this client from the queue?')) {
          return;
        }
        
        const response = await axios.delete(`/api/queue/remove/${id}`);
        console.log("Remove client response:", response.data);
        
        // Show success message (optional)
        if (response.data && response.data.message) {
          // You could add a toast notification here if you have one
          console.log(response.data.message);
        }
        
        // Refresh the queue
        await fetchQueue();
      } catch (error) {
        console.error("Error removing client:", error);
        
        // Show error message to user
        let errorMessage = "Failed to remove client from queue.";
        if (error.response && error.response.data && error.response.data.message) {
          errorMessage = error.response.data.message;
        }
        
        alert(errorMessage);
      }
    };

    // Modal controls
    const openAddClientModal = () => {
      showAddClientModal.value = true;
      fetchFormData();
    };

    const closeAddClientModal = () => {
      showAddClientModal.value = false;
      newClient.value = {
        name: '',
        phoneNumber: '',
        email: '',
        service_id: '',
        barber_id: '',
      };
    };

    // Add walk-in client to queue
    const addWalkInClient = async () => {
      try {
        await axios.post('/api/queue/add-walkin', newClient.value);
        closeAddClientModal();
        await fetchQueue();
      } catch (error) {
        console.error("Error adding walk-in client:", error);
        
        // Show error message to user
        let errorMessage = "Failed to add walk-in client.";
        if (error.response && error.response.data && error.response.data.message) {
          errorMessage = error.response.data.message;
        }
        
        alert(errorMessage);
      }
    };

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

    const formatPrice = (price) => {
      return `R${parseFloat(price).toFixed(2)}`;
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

    // Fetch completed bookings for statistics
    const fetchCompletedBookings = async () => {
      try {
        const response = await axios.get('/api/bookings/completed-today');
        completedBookings.value = response.data;
      } catch (error) {
        console.error("Error fetching completed bookings:", error);
      }
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
      console.log("Component mounted");
      // First get server time to calibrate our clock
      await fetchServerTime();
      // Then fetch queue data
      await fetchQueue();
      fetchCompletedBookings();
      setupWebsocket();
      startTimeUpdates();
      
      // Log timezone information to help diagnose date issues
      console.log("Browser timezone:", Intl.DateTimeFormat().resolvedOptions().timeZone);
      console.log("Local timezone offset (minutes):", new Date().getTimezoneOffset());
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
      queueActive,
      services,
      barbers,
      newClient,
      showAddClientModal,
      averageServiceTime,
      totalQueueTime,
      currentClientStatus,
      calculateElapsedMinutes,
      startQueue,
      nextClient,
      removeClient, // Updated function name
      openAddClientModal,
      closeAddClientModal,
      addWalkInClient,
      formatTime,
      formatDateTime,
      formatPrice
    };
  }
};
</script>