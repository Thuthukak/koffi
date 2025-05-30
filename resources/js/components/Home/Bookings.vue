<template>
  <Navbar />
  
  <!-- Main Container with proper spacing -->
  <div class="min-h-screen bg-gray-50 py-4 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
      
      <!-- Shop status banner - Full width on mobile -->
      <div :class="`w-full rounded-lg mb-6 p-4 text-white text-center shadow-md ${isQueueActive ? 'bg-green-600' : 'bg-red-600'}`">
        <h2 class="text-lg sm:text-xl font-bold">
          {{ isQueueActive ? 'We are open and serving clients!' : 'Queue is currently closed' }}
        </h2>
      </div>
      
      <!-- Main Grid Layout -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-8">
        
        <!-- Left Column - Queue Status -->
        <div class="space-y-6">
          <!-- Current Client -->
          <div v-if="currentClient" class="bg-white border rounded-lg p-4 sm:p-6 shadow-md">
            <h2 class="text-lg sm:text-xl font-bold mb-4 text-indigo-800">Currently Serving</h2>
            <div class="space-y-4">
              <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-3">
                <div class="flex-1">
                  <p class="text-lg font-semibold text-gray-900">{{ currentClient.client.name }}</p>
                  <p class="text-gray-700">{{ currentClient.service.name }} ({{ currentClient.service.duration }} mins)</p>
                  <p class="text-sm text-gray-600">Ref: {{ currentClient.reference }}</p>
                  <p class="text-sm text-gray-500">Started: {{ formatDateTime(currentClient.start_time) }}</p>
                </div>
                <div class="sm:text-right">
                  <p v-if="currentClientStatus.isOvertime" class="text-red-500 font-bold text-sm sm:text-base">
                    Overtime: {{ formatTime(currentClientStatus.timeDifference) }}
                  </p>
                  <p v-else class="text-green-600 font-bold text-sm sm:text-base">
                    Remaining: {{ formatTime(currentClientStatus.timeDifference) }}
                  </p>
                </div>
              </div>
              
              <!-- Progress Bar -->
              <div class="mt-4">
                <div class="w-full bg-gray-200 rounded-full h-3">
                  <div 
                    class="h-3 rounded-full transition-all duration-300" 
                    :class="progressBarColor" 
                    :style="{ width: progressPercentage + '%' }"
                  ></div>
                </div>
                <div class="flex justify-between text-xs text-gray-500 mt-1">
                  <span>0:00</span>
                  <span>{{ currentClient.service.duration }} min</span>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Waiting List -->
          <div class="bg-white border rounded-lg shadow-md overflow-hidden">
            <div class="p-4 sm:p-6 border-b bg-gray-50">
              <h2 class="text-lg sm:text-xl font-bold text-gray-900">Waiting List</h2>
            </div>
            
            <div class="p-4 sm:p-6">
              <div v-if="waitingClients.length === 0" class="text-gray-500 text-center py-8">
                <div class="text-4xl mb-2">🕐</div>
                <p>No clients in queue</p>
              </div>
              
              <!-- Mobile-optimized waiting list -->
              <div v-else class="space-y-3 sm:space-y-0">
                <!-- Mobile Cards (visible on small screens) -->
                <div class="block sm:hidden space-y-3">
                  <div 
                    v-for="(client, index) in waitingClientsWithTimes" 
                    :key="client.id"
                    class="bg-gray-50 rounded-lg p-4 border"
                  >
                    <div class="flex justify-between items-start mb-2">
                      <div class="font-semibold text-gray-900">{{ client.client.name }}</div>
                      <span class="bg-indigo-100 text-indigo-800 text-xs font-medium px-2 py-1 rounded-full">
                        #{{ index + 1 }}
                      </span>
                    </div>
                    <div class="text-sm text-gray-600 space-y-1">
                      <p>{{ client.service.name }}</p>
                      <p>Wait: {{ formatTime(client.waitTime) }}</p>
                      <p>Ref: {{ client.reference }}</p>
                    </div>
                  </div>
                </div>
                
                <!-- Desktop Table (hidden on small screens) -->
                <div class="hidden sm:block overflow-x-auto">
                  <table class="min-w-full">
                    <thead class="bg-gray-50">
                      <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Wait Time</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
                      </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                      <tr v-for="(client, index) in waitingClientsWithTimes" :key="client.id" class="hover:bg-gray-50">
                        <td class="px-4 py-4 whitespace-nowrap font-medium">{{ index + 1 }}</td>
                        <td class="px-4 py-4 whitespace-nowrap">{{ client.client.name }}</td>
                        <td class="px-4 py-4 whitespace-nowrap">{{ client.reference }}</td>
                        <td class="px-4 py-4 whitespace-nowrap">{{ formatTime(client.waitTime) }}</td>
                        <td class="px-4 py-4 whitespace-nowrap">{{ client.service.name }}</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Right Column - Actions and Forms -->
        <div class="space-y-6">
          <!-- Action Buttons -->
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-1 xl:grid-cols-2 gap-4">
            <button 
              @click="toggleJoinQueue"
              class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-4 px-6 rounded flex justify-between items-center transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5"
            >
              <span class="flex items-center">
                <font-awesome-icon :icon="['fas', 'user']" class="mr-2" />
                Join Queue
              </span>
              <font-awesome-icon 
                :icon="isJoinQueueOpen ? ['fas', 'chevron-up'] : ['fas', 'chevron-down']" 
                class="transition-transform duration-200"
              />
            </button>
            
            <button 
              @click="toggleCheckPosition"
              class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-4 px-6 rounded flex justify-between items-center transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5"
            >
              <span class="flex items-center">
                <font-awesome-icon :icon="['fas', 'search']" class="mr-2" />
                Check Position
              </span>
              <font-awesome-icon 
                :icon="isCheckPositionOpen ? ['fas', 'chevron-up'] : ['fas', 'chevron-down']" 
                class="transition-transform duration-200"
              />
            </button>
          </div>
          
          <!-- Join Queue Form -->
          <div 
            v-if="isJoinQueueOpen" 
            class="bg-white rounded border shadow-md overflow-hidden transition-all duration-300 ease-in-out"
          >
            <div class="p-4 sm:p-6 border-b bg-blue-50">
              <h2 class="font-bold text-lg text-blue-800">Join the Queue</h2>
              <p class="text-sm text-blue-700 mt-1">Fill out the form below to join</p>
            </div>
            
            <div v-if="joinSuccess" class="p-4 sm:p-6">
              <div v-if="typeof joinSuccess === 'string'" class="text-center py-4">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto mb-2"></div>
                <p>{{ joinSuccess }}</p>
              </div>
              <div v-else class="text-center bg-green-50 p-4 sm:p-6 rounded border border-green-200">
                <div class="text-4xl mb-3">✅</div>
                <h3 class="text-green-800 font-bold text-lg mb-3">{{ joinSuccess.message }}</h3>
                <div class="bg-white p-4 rounded-lg border-2 border-dashed border-green-300 mb-4">
                  <p class="font-bold text-sm text-gray-600">Your Reference Code</p>
                  <p class="text-2xl font-bold text-blue-600 font-mono">{{ joinSuccess.reference }}</p>
                  <p class="text-xs text-gray-500 mt-1">Save this code to check your position</p>
                </div>
                <div class="space-y-2 text-sm">
                  <p><span class="font-medium">Position:</span> {{ joinSuccess.position }}</p>
                  <p><span class="font-medium">Est. Wait:</span> {{ joinSuccess.waitTime }}</p>
                </div>
                <button 
                  @click="joinSuccess = null"
                  class="mt-4 bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition-colors"
                >
                  Done
                </button>
              </div>
            </div>
            
            <div v-else class="p-4 sm:p-6 space-y-4">
              <!-- Name Field -->
              <div>
                <label class="block text-gray-700 text-sm font-semibold mb-2" for="name">
                  Full Name*
                </label>
                <input
                  id="name"
                  type="text"
                  v-model="newClient.name"
                  :class="`w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors ${formErrors.name ? 'border-red-500 bg-red-50' : 'border-gray-300'}`"
                  placeholder="Enter your full name"
                />
                <p v-if="formErrors.name" class="text-red-500 text-xs mt-1 flex items-center">
                  <span class="mr-1">⚠️</span>{{ formErrors.name }}
                </p>
              </div>
              
              <!-- Phone Field -->
              <div>
                <label class="block text-gray-700 text-sm font-semibold mb-2" for="phoneNumber">
                  Phone Number*
                </label>
                <input
                  id="phoneNumber"
                  type="tel"
                  v-model="newClient.phoneNumber"
                  :class="`w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors ${formErrors.phoneNumber ? 'border-red-500 bg-red-50' : 'border-gray-300'}`"
                  placeholder="Your phone number"
                />
                <p v-if="formErrors.phoneNumber" class="text-red-500 text-xs mt-1 flex items-center">
                  <span class="mr-1">⚠️</span>{{ formErrors.phoneNumber }}
                </p>
              </div>
              
              <!-- Email Field -->
              <div>
                <label class="block text-gray-700 text-sm font-semibold mb-2" for="email">
                  Email <span class="text-gray-500 font-normal">(optional)</span>
                </label>
                <input
                  id="email"
                  type="email"
                  v-model="newClient.email"
                  :class="`w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors ${formErrors.email ? 'border-red-500 bg-red-50' : 'border-gray-300'}`"
                  placeholder="your.email@example.com"
                />
                <p v-if="formErrors.email" class="text-red-500 text-xs mt-1 flex items-center">
                  <span class="mr-1">⚠️</span>{{ formErrors.email }}
                </p>
              </div>
              
              <!-- Service Selection -->
              <div>
                <label class="block text-gray-700 text-sm font-semibold mb-2" for="service_id">
                  Service*
                </label>
                <select
                  id="service_id"
                  v-model="newClient.service_id"
                  :class="`w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors ${formErrors.service_id ? 'border-red-500 bg-red-50' : 'border-gray-300'}`"
                >
                  <option value="">Select a service</option>
                  <option v-for="service in services" :key="service.id" :value="service.id">
                    {{ service.name }} ({{ formatTime(service.duration) }}) - {{ formatPrice(service.price) }}
                  </option>
                </select>
                <p v-if="formErrors.service_id" class="text-red-500 text-xs mt-1 flex items-center">
                  <span class="mr-1">⚠️</span>{{ formErrors.service_id }}
                </p>
              </div>

              <!-- Barber Selection -->
              <div>
                <label class="block text-gray-700 text-sm font-semibold mb-2" for="barber_id">
                  Preferred Barber*
                </label>
                <select
                  id="barber_id"
                  v-model="newClient.barber_id"
                  :class="`w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors ${formErrors.barber_id ? 'border-red-500 bg-red-50' : 'border-gray-300'}`"
                >
                  <option value="">Select a barber</option>
                  <option v-for="barber in barbers" :key="barber.id" :value="barber.id">
                    {{ barber.user.name }} 
                  </option>
                </select>
                <p v-if="formErrors.barber_id" class="text-red-500 text-xs mt-1 flex items-center">
                  <span class="mr-1">⚠️</span>{{ formErrors.barber_id }}
                </p>
              </div>
              
              <!-- Wait Time Estimate -->
              <div v-if="newClient.service_id" class="bg-amber-50 border border-amber-200 p-4 rounded-lg">
                <div class="flex items-center">
                  <span class="text-xl mr-2">⏱️</span>
                  <div>
                    <p class="font-semibold text-amber-800">Estimated Wait Time</p>
                    <p class="text-amber-700">{{ formatTime(calculateTotalWaitTime()) }}</p>
                  </div>
                </div>
              </div>
              
              <!-- Submit Button -->
              <button
                @click="joinQueue"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5"
              >
                <span class="flex items-center justify-center">
                  <font-awesome-icon :icon="['fas', 'user']" class="mr-2" />
                  Join Queue
                </span>
              </button>
              
              <!-- Terms Notice -->
              <p class="text-xs text-gray-500 text-center px-2 mt-3">
                * Required fields. By joining, you agree to our terms and conditions.
              </p>
            </div>
          </div>
          
          <!-- Check Position Form -->
          <div 
            v-if="isCheckPositionOpen" 
            class="bg-white rounded-lg border shadow-md overflow-hidden transition-all duration-300 ease-in-out"
          >
            <div class="p-4 sm:p-6 border-b bg-indigo-50">
              <h2 class="font-bold text-lg text-indigo-800">Check Your Position</h2>
              <p class="text-sm text-indigo-700 mt-1">Enter your name or reference code</p>
            </div>
            
            <div class="p-4 sm:p-6 space-y-4">
              <div class="flex flex-col sm:flex-row gap-2">
                <input
                  type="text"
                  v-model="searchQuery"
                  class="flex-grow p-3 border border-gray-300 rounded-lg sm:rounded-r-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                  placeholder="Name or Reference Code"
                />
                <button
                  @click="searchQueue"
                  class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-6 py-3 rounded sm:rounded-l-none transition-all duration-200 shadow-md hover:shadow-lg"
                >
                  <font-awesome-icon :icon="['fas', 'search']" class="sm:mr-2" />
                  <span class="hidden sm:inline">Search</span>
                </button>
              </div>
              
              <!-- Search Results -->
              <div v-if="searchResult" :class="`p-4 rounded-lg border-2 ${searchResult.error ? 'bg-red-50 border-red-200' : 'bg-green-50 border-green-200'}`">
                <div v-if="searchResult.error" class="text-center">
                  <div class="text-2xl mb-2">❌</div>
                  <p class="text-red-700 font-medium">{{ searchResult.error }}</p>
                </div>
                <div v-else>
                  <div class="flex justify-between items-start mb-3">
                    <div class="flex-1">
                      <div class="text-xl mb-1">✅</div>
                      <h3 class="font-bold text-green-800">{{ searchResult.client }}</h3>
                      <p class="text-sm text-green-700">Ref: {{ searchResult.reference }}</p>
                    </div>
                    <button 
                      @click="searchResult = null"
                      class="text-gray-400 hover:text-gray-600 p-1"
                    >
                      <font-awesome-icon :icon="['fas', 'xmark']" />
                    </button>
                  </div>
                  <div class="border-t border-green-200 pt-3 space-y-2">
                    <div class="bg-white p-3 rounded border border-green-200">
                      <p class="font-bold text-green-800 text-lg">{{ searchResult.position }}</p>
                      <p class="text-green-700">Est. wait: {{ searchResult.waitTime }}</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <Footer />
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import Navbar from './Navbar.vue';
import Footer from './Footer.vue';
import axios from 'axios';
import { library } from '@fortawesome/fontawesome-svg-core';
import { 
  faSearch, 
  faChevronDown, 
  faChevronUp, 
  faUser,
  faXmark
} from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

// Add icons to library
library.add(faSearch, faChevronDown, faChevronUp, faUser, faXmark);

// Core state management
const isQueueActive = ref(true);
const currentClient = ref(null);
const waitingClients = ref([]);
const services = ref([]);
const barbers = ref([]);

// Timer management
const timerInterval = ref(null);
const currentTime = ref(new Date());
const processedStartTime = ref(null);

// UI state
const isJoinQueueOpen = ref(true);
const isCheckPositionOpen = ref(false);
const searchQuery = ref('');
const searchResult = ref(null);

// Form state
const newClient = ref({
  name: '',
  phoneNumber: '',
  email: '',
  service_id: '',
  barber_id: '',
});
const formErrors = ref({});
const joinSuccess = ref(null);

// Toggle functions
const toggleJoinQueue = () => {
  isJoinQueueOpen.value = !isJoinQueueOpen.value;
  isCheckPositionOpen.value = false;
  joinSuccess.value = null;
};

const toggleCheckPosition = () => {
  isCheckPositionOpen.value = !isCheckPositionOpen.value;
  isJoinQueueOpen.value = false;
  searchResult.value = null;
  searchQuery.value = '';
};

// Time calculation functions
const parseStartTime = (rawStartTime) => {
  if (!rawStartTime) return null;
  
  try {
    const parsedTime = new Date(rawStartTime);
    if (!isNaN(parsedTime.getTime())) {
      return parsedTime;
    }
  } catch (e) {
    console.warn("Failed to parse start time:", e);
  }
  
  return new Date(); // Fallback to current time
};

const calculateElapsedMinutes = () => {
  if (!processedStartTime.value) return 0;
  const elapsedMs = currentTime.value.getTime() - processedStartTime.value.getTime();
  return Math.max(0, Math.floor(elapsedMs / 60000));
};

// Computed properties for current client status
const currentClientStatus = computed(() => {
  if (!currentClient.value || !processedStartTime.value) {
    return { isOvertime: false, timeDifference: 0 };
  }
  
  const serviceDuration = parseInt(currentClient.value.service.duration || 0);
  const elapsedMinutes = calculateElapsedMinutes();
  
  if (elapsedMinutes > serviceDuration) {
    return {
      isOvertime: true,
      timeDifference: elapsedMinutes - serviceDuration
    };
  } else {
    return {
      isOvertime: false,
      timeDifference: serviceDuration - elapsedMinutes
    };
  }
});

const progressPercentage = computed(() => {
  if (!currentClient.value || !processedStartTime.value) return 0;
  
  const serviceDuration = parseInt(currentClient.value.service.duration || 0);
  if (!serviceDuration) return 0;
  
  const elapsedMinutes = calculateElapsedMinutes();
  const percentage = (elapsedMinutes / serviceDuration) * 100;
  
  return Math.min(100, Math.max(0, percentage));
});

const progressBarColor = computed(() => {
  if (!currentClient.value) return 'bg-gray-300';
  
  if (currentClientStatus.value.isOvertime) {
    return 'bg-red-500';
  }
  
  const percentage = progressPercentage.value;
  if (percentage < 50) return 'bg-green-500';
  else if (percentage < 80) return 'bg-yellow-500';
  else return 'bg-orange-500';
});

const waitingClientsWithTimes = computed(() => {
  if (waitingClients.value.length === 0) return [];
  
  let cumulativeWaitTime = 0;
  
  // Start with current client's remaining time if applicable
  if (currentClient.value && currentClientStatus.value && !currentClientStatus.value.isOvertime) {
    cumulativeWaitTime = currentClientStatus.value.timeDifference;
  }
  
  return waitingClients.value.map((client) => {
    const clientCopy = { ...client };
    clientCopy.waitTime = cumulativeWaitTime;
    
    // Add this client's service duration to the cumulative wait time
    const serviceDuration = parseInt(client.service.duration || 0);
    cumulativeWaitTime += serviceDuration;
    
    return clientCopy;
  });
});

// API functions
const fetchQueue = async () => {
  try {
    const response = await axios.get("/api/queue/current");
    
    currentClient.value = response.data.current;
    waitingClients.value = response.data.waiting;
    
    // Process start time right after receiving data
    if (currentClient.value && currentClient.value.start_time) {
      processedStartTime.value = parseStartTime(currentClient.value.start_time);
    } else {
      processedStartTime.value = null;
    }
    
    currentTime.value = new Date();
  } catch (error) {
    console.error("Error fetching queue:", error);
  }
};

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

// Form validation and submission
const validateForm = () => {
  const errors = {};
  
  if (!newClient.value.name.trim()) errors.name = 'Name is required';
  if (!newClient.value.phoneNumber.trim()) errors.phoneNumber = 'Phone number is required';
  if (!newClient.value.service_id) errors.service_id = 'Please select a service';
  if (!newClient.value.barber_id) errors.barber_id = 'Please select a barber';
  
  if (newClient.value.email && !/^\S+@\S+\.\S+$/.test(newClient.value.email)) {
    errors.email = 'Please enter a valid email';
  }
  
  formErrors.value = errors;
  return Object.keys(errors).length === 0;
};

const joinQueue = async () => {
  if (!validateForm()) return;
  
  joinSuccess.value = 'Processing...';
  
  try {
    const response = await axios.post('/api/queue/add-walkin', newClient.value);
    
    joinSuccess.value = {
      message: 'Successfully added to queue!',
      reference: response.data.reference,
      position: response.data.position,
      waitTime: formatTime(response.data.waitTime)
    };
    
    // Reset form
    newClient.value = {
      name: '',
      phoneNumber: '',
      email: '',
      service_id: '',
      barber_id: '',
    };
    
    // Refresh queue data
    fetchQueue();
  } catch (error) {
    console.error("Error joining queue:", error);
    joinSuccess.value = 'Error joining queue. Please try again.';
  }
};

// Search functionality
const searchQueue = () => {
  if (!searchQuery.value.trim()) {
    searchResult.value = null;
    return;
  }
  
  const query = searchQuery.value.toLowerCase();
  const allClients = [currentClient.value, ...waitingClients.value].filter(Boolean);
  
  const found = allClients.find(booking => 
    booking.client?.name?.toLowerCase().includes(query) || 
    booking.reference?.toLowerCase().includes(query)
  );
  
  if (found) {
    const position = allClients.indexOf(found);
    const estimatedTime = position === 0 ? 
      currentClientStatus.value.timeDifference : 
      waitingClientsWithTimes.value[position - 1]?.waitTime || 0;
    
    searchResult.value = {
      client: found.client.name,
      position: position === 0 ? 'Currently Being Served' : `Position ${position} in queue`,
      waitTime: formatTime(estimatedTime),
      reference: found.reference
    };
  } else {
    searchResult.value = { 
      error: 'No match found. Please check your name or reference number.' 
    };
  }
};

// Utility functions
const calculateTotalWaitTime = () => {
  let totalWait = 0;
  
  // Add current client's remaining time
  if (currentClient.value && !currentClientStatus.value.isOvertime) {
    totalWait += currentClientStatus.value.timeDifference;
  }
  
  // Add all waiting clients' service times
  waitingClients.value.forEach(client => {
    if (client.service && client.service.duration) {
      totalWait += client.service.duration;
    }
  });
  
  // Add selected service duration
  if (newClient.value.service_id) {
    const selectedService = services.value.find(s => s.id === parseInt(newClient.value.service_id));
    if (selectedService) {
      totalWait += selectedService.duration;
    }
  }
  
  return totalWait;
};

const formatTime = (minutes) => {
  const roundedMinutes = Math.max(0, Math.round(minutes || 0));
  return `${Math.floor(roundedMinutes / 60)}h ${roundedMinutes % 60}m`;
};

const formatDateTime = (datetime) => {
  if (!datetime) return '';
  const date = new Date(datetime);
  return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
};

const formatPrice = (price) => {
  return `R${parseFloat(price).toFixed(2)}`;
};

// Timer management
const startTimeUpdates = () => {
  timerInterval.value = setInterval(() => {
    currentTime.value = new Date();
  }, 1000);
};

// WebSocket setup
const setupWebsocket = () => {
  if (window.Echo) {
    window.Echo.channel('queue')
      .listen('QueueUpdated', () => {
        fetchQueue();
      });
  }
};

// Lifecycle hooks
onMounted(async () => {
  await fetchFormData();
  await fetchQueue();
  setupWebsocket();
  startTimeUpdates();
});

onUnmounted(() => {
  if (timerInterval.value) {
    clearInterval(timerInterval.value);
  }
  
  if (window.Echo) {
    window.Echo.leave('queue');
  }
});

</script>