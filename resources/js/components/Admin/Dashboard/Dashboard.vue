<template>
  <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 py-12 bg-blue-50">
    <!-- Header -->
    
      <div class="px-4 sm:px py-4">
        <h1 class="text-2xl sm:text-3xl font-bold bg-gradient-to-r from-blue-600 via-purple-600 to-indigo-600 bg-clip-text text-transparent">
          Queue Dashboard
        </h1>
        <p class="text-sm text-gray-600 mt-1">Real-time service monitoring</p>
      </div>
    

    <div class="px-4 sm:px-6 py-6 space-y-6">
      <!-- Summary Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <!-- Clients in Queue Card -->
        <div class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-blue-500 to-blue-600 p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
          <div class="absolute inset-0 bg-white/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
          <div class="relative z-10">
            <div class="flex items-center justify-between mb-2">
              <div class="p-2 bg-white/20 rounded-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
              </div>
              <span class="text-3xl sm:text-4xl font-bold">{{ waitingClients.length }}</span>
            </div>
            <h3 class="text-lg font-semibold mb-1">Clients in Queue</h3>
            <p class="text-blue-100 text-sm">waiting to be served</p>
          </div>
        </div>

        <!-- Average Service Time Card -->
        <div class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-500 to-emerald-600 p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
          <div class="absolute inset-0 bg-white/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
          <div class="relative z-10">
            <div class="flex items-center justify-between mb-2">
              <div class="p-2 bg-white/20 rounded-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <span class="text-3xl sm:text-4xl font-bold">{{ averageServiceTime }}</span>
            </div>
            <h3 class="text-lg font-semibold mb-1">Avg. Service Time</h3>
            <p class="text-emerald-100 text-sm">minutes per client</p>
          </div>
        </div>

        <!-- Total Queue Time Card -->
        <div class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-purple-500 to-purple-600 p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105 sm:col-span-2 lg:col-span-1">
          <div class="absolute inset-0 bg-white/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
          <div class="relative z-10">
            <div class="flex items-center justify-between mb-2">
              <div class="p-2 bg-white/20 rounded-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
              </div>
              <span class="text-2xl sm:text-3xl font-bold">{{ totalQueueTime }}</span>
            </div>
            <h3 class="text-lg font-semibold mb-1">Total Queue Time</h3>
            <p class="text-purple-100 text-sm">estimated wait</p>
          </div>
        </div>
      </div>

      <!-- Current Client Section -->
      <div v-if="currentClient" class="relative overflow-hidden rounded-2xl bg-white shadow-xl border border-gray-100">
        <div class="absolute inset-0 bg-gradient-to-r from-indigo-500/5 to-purple-500/5"></div>
        <div class="relative p-6">
          <div class="flex items-center gap-4 mb-6">
            <div class="p-3 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-full">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
            </div>
            <div>
              <h2 class="text-xl sm:text-2xl font-bold text-gray-900">Currently Serving</h2>
              <p class="text-gray-600">Active service session</p>
            </div>
          </div>

          <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Client Details -->
            <div class="space-y-4">
              <div class="p-4 bg-gray-50 rounded-xl">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ currentClient.client.name }}</h3>
                <div class="space-y-2 text-sm">
                  <div class="flex items-center gap-2">
                    <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                    <span class="text-gray-600">Service:</span>
                    <span class="font-medium">{{ currentClient.service.name }}</span>
                  </div>
                  <div class="flex items-center gap-2">
                    <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                    <span class="text-gray-600">Duration:</span>
                    <span class="font-medium">{{ currentClient.service.duration }} mins</span>
                  </div>
                  <div class="flex items-center gap-2">
                    <span class="w-2 h-2 bg-purple-500 rounded-full"></span>
                    <span class="text-gray-600">Reference:</span>
                    <span class="font-medium">{{ currentClient.reference }}</span>
                  </div>
                  <div class="flex items-center gap-2">
                    <span class="w-2 h-2 bg-orange-500 rounded-full"></span>
                    <span class="text-gray-600">Started:</span>
                    <span class="font-medium">{{ formatDateTime(currentClient.start_time) }}</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Timer and Progress -->
            <div class="space-y-4">
              <div class="text-center p-4 bg-gray-50 rounded-xl">
                <div class="text-3xl sm:text-4xl font-bold mb-2" :class="currentClientStatus.isOvertime ? 'text-red-500' : 'text-green-600'">
                  {{ formatTime(currentClientStatus.timeDifference) }}
                </div>
                <p class="text-sm font-medium" :class="currentClientStatus.isOvertime ? 'text-red-500' : 'text-green-600'">
                  {{ currentClientStatus.isOvertime ? 'Overtime' : 'Remaining' }}
                </p>
              </div>

              <!-- Progress Bar -->
              <div class="space-y-2">
                <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                  <div 
                    class="h-full rounded-full transition-all duration-1000 ease-out" 
                    :class="progressBarColor" 
                    :style="{ width: progressPercentage + '%' }"
                  ></div>
                </div>
                <div class="flex justify-between text-xs text-gray-600">
                  <span>0:00</span>
                  <span>{{ currentClient.service.duration }} min</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Waiting List -->
      <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
              <div class="p-2 bg-blue-100 rounded-lg">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
              </div>
              <h2 class="text-xl font-bold text-gray-900">Waiting List</h2>
            </div>
            <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
              {{ waitingClients.length }} waiting
            </span>
          </div>
        </div>

        <div class="p-6">
          <div v-if="waitingClients.length === 0" class="text-center py-12">
            <div class="p-4 bg-gray-100 rounded-full w-16 h-16 mx-auto mb-4 flex items-center justify-center">
              <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <p class="text-gray-500 text-lg">No clients in queue</p>
            <p class="text-gray-400 text-sm mt-1">All caught up!</p>
          </div>

          <div v-else class="space-y-3">
            <!-- Mobile Cards -->
            <div class="block sm:hidden space-y-3">
              <div v-for="(client, index) in waitingClientsWithTimes" :key="client.id" 
                   class="p-4 bg-gray-50 rounded-xl border-l-4 border-blue-500">
                <div class="flex items-center justify-between mb-2">
                  <span class="text-lg font-bold text-blue-600">#{{ index + 1 }}</span>
                  <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">
                    {{ formatTime(client.waitTime) }} wait
                  </span>
                </div>
                <h3 class="font-semibold text-gray-900 mb-1">{{ client.client.name }}</h3>
                <p class="text-sm text-gray-600 mb-1">{{ client.service.name }}</p>
                <p class="text-xs text-gray-500">Ref: {{ client.reference }}</p>
              </div>
            </div>

            <!-- Desktop Table -->
            <div class="hidden sm:block overflow-x-auto">
              <table class="min-w-full">
                <thead>
                  <tr class="border-b border-gray-200">
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Wait Time</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                  <tr v-for="(client, index) in waitingClientsWithTimes" :key="client.id" 
                      class="hover:bg-gray-50 transition-colors duration-200">
                    <td class="px-4 py-4">
                      <span class="inline-flex items-center justify-center w-8 h-8 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                        {{ index + 1 }}
                      </span>
                    </td>
                    <td class="px-4 py-4 font-medium text-gray-900">{{ client.client.name }}</td>
                    <td class="px-4 py-4 text-gray-600">{{ client.service.name }}</td>
                    <td class="px-4 py-4">
                      <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                        {{ formatTime(client.waitTime) }}
                      </span>
                    </td>
                    <td class="px-4 py-4 text-gray-500 text-sm">{{ client.reference }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- Completed Services -->
      <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
              <div class="p-2 bg-green-100 rounded-lg">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <h2 class="text-xl font-bold text-gray-900">Completed Today</h2>
            </div>
            <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">
              {{ completedBookings.length }} completed
            </span>
          </div>
        </div>

        <div class="p-6">
          <div v-if="completedBookings.length === 0" class="text-center py-12">
            <div class="p-4 bg-gray-100 rounded-full w-16 h-16 mx-auto mb-4 flex items-center justify-center">
              <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
              </svg>
            </div>
            <p class="text-gray-500 text-lg">No services completed today</p>
            <p class="text-gray-400 text-sm mt-1">Services will appear here once completed</p>
          </div>

          <div v-else class="space-y-3">
            <!-- Mobile Cards -->
            <div class="block lg:hidden space-y-3">
              <div v-for="booking in completedBookings" :key="booking.id" 
                   class="p-4 bg-gray-50 rounded-xl border-l-4 border-green-500">
                <div class="flex items-center justify-between mb-2">
                  <h3 class="font-semibold text-gray-900">{{ booking.client.name }}</h3>
                  <button @click="reopenBooking(booking.id)" 
                          class="px-3 py-1 bg-blue-100 text-blue-700 rounded-lg text-sm font-medium hover:bg-blue-200 transition-colors duration-200">
                    Reopen
                  </button>
                </div>
                <p class="text-sm text-gray-600 mb-2">{{ booking.service.name }}</p>
                <div class="grid grid-cols-2 gap-2 text-xs text-gray-500">
                  <div>Start: {{ formatDateTime(booking.start_time) }}</div>
                  <div>End: {{ formatDateTime(booking.end_time) }}</div>
                  <div>Duration: {{ booking.actual_duration }}m</div>
                </div>
              </div>
            </div>

            <!-- Desktop Table -->
            <div class="hidden lg:block overflow-x-auto">
              <table class="min-w-full">
                <thead>
                  <tr class="border-b border-gray-200">
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Start</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">End</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                  <tr v-for="booking in completedBookings" :key="booking.id" 
                      class="hover:bg-gray-50 transition-colors duration-200">
                    <td class="px-4 py-4 font-medium text-gray-900">{{ booking.client.name }}</td>
                    <td class="px-4 py-4 text-gray-600">{{ booking.service.name }}</td>
                    <td class="px-4 py-4 text-gray-500 text-sm">{{ formatDateTime(booking.start_time) }}</td>
                    <td class="px-4 py-4 text-gray-500 text-sm">{{ formatDateTime(booking.end_time) }}</td>
                    <td class="px-4 py-4">
                      <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        {{ booking.actual_duration }}m
                      </span>
                    </td>
                    <td class="px-4 py-4">
                      <button @click="reopenBooking(booking.id)" 
                              class="text-blue-600 hover:text-blue-900 font-medium text-sm hover:underline transition-colors duration-200">
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

    const totalRevenue = async () => {
      try {
        const response = await axios.get("/api/total-revenue");
        return response.data.total_revenue;
      } catch (error) {
        console.error("Error fetching total revenue:", error);
      }
    }
    
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
      totalRevenue,
      progressPercentage,
      progressBarColor,
      formatTime,
      formatDateTime,
      reopenBooking
    };
  }
};
</script>