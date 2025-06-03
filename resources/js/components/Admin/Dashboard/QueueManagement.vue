<template>
  <div class="p-4">
    <h1 class="text-2xl font-bold mb-6">Queue Management</h1>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <!-- Control Panel -->
      <div class="p-4 border rounded-md bg-gray-50 col-span-full">
        <div class="flex space-x-4">
          <button 
            @click="startQueue" 
            class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600"
            :disabled="queueActive"
          >
            Start Queue
          </button>
          <button 
            @click="nextClient" 
            class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600"
            :disabled="!queueActive"
          >
            Next Client
          </button>
          <button 
            @click="openAddClientModal" 
            class="px-4 py-2 bg-purple-500 text-white rounded-md hover:bg-purple-600"
          >
            Add Walk-in Client
          </button>
        </div>
      </div>
      
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
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="(client, index) in waitingClientsWithTimes" :key="client.id">
                <td class="px-6 py-4 whitespace-nowrap">{{ index + 1 }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ client.client.name }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ client.service.name }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ formatTime(client.waitTime) }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ client.reference }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <button 
                    @click="skipClient(client.id)" 
                    class="text-red-600 hover:text-red-900"
                  >
                    Skip
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      
      <!-- Queue Statistics -->
      <div class="border rounded-md p-4 col-span-full">
        <h2 class="text-xl font-bold mb-4">Queue Statistics</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div class="p-4 bg-blue-50 rounded-md">
            <h3 class="font-semibold text-blue-800">Clients in Queue</h3>
            <p class="text-2xl font-bold">{{ waitingClients.length }}</p>
          </div>
          <div class="p-4 bg-green-50 rounded-md">
            <h3 class="font-semibold text-green-800">Avg. Service Time</h3>
            <p class="text-2xl font-bold">{{ averageServiceTime }} min</p>
          </div>
          <div class="p-4 bg-indigo-50 rounded-md">
            <h3 class="font-semibold text-indigo-800">Total Queue Time</h3>
            <p class="text-2xl font-bold">{{ totalQueueTime }}</p>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Add Walk-in Client Modal -->
    <div v-if="showAddClientModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white p-6 rounded-lg w-full max-w-lg">
        <h2 class="text-xl font-bold mb-4">Add Walk-in Client</h2>
        
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700">Name</label>
          <input 
            v-model="newClient.name" 
            type="text" 
            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
          >
        </div>
        
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700">Phone Number</label>
          <input 
            v-model="newClient.phoneNumber" 
            type="text" 
            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
          >
        </div>
        
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700">Email</label>
          <input 
            v-model="newClient.email" 
            type="email" 
            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
          >
        </div>
        
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700">Service</label>
          <select 
            v-model="newClient.service_id" 
            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
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
            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
          >
            <option v-for="barber in barbers" :key="barber.id" :value="barber.id">
              {{ barber.name }}
            </option>
          </select>
        </div>
        
        <div class="flex justify-end space-x-3">
          <button 
            @click="closeAddClientModal" 
            class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400"
          >
            Cancel
          </button>
          <button 
            @click="addWalkInClient" 
            class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600"
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

export default {
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

    const skipClient = async (id) => {
      try {
        await axios.post(`/api/queue/skip/${id}`);
        await fetchQueue();
      } catch (error) {
        console.error("Error skipping client:", error);
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
      skipClient,
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