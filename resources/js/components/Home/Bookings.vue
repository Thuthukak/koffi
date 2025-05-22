<template>
  <Navbar />
  <div class="row">
    <div class="col-md-6">
  <div class="w-full max-w-4xl mx-auto px-4 py-8">
    <!-- Shop status banner -->
    <div :class="`w-full rounded-lg mb-6 p-4 text-white text-center ${isQueueActive ? 'bg-green-600' : 'bg-red-600'}`">
      <h2 class="text-xl font-bold">
        {{ isQueueActive ? 'We are open and serving clients!' : 'Queue is currently closed' }}
      </h2>
    </div>
    
    <!-- Current client display -->
    <div v-if="currentClient" class="bg-indigo-50 border-l-4 border-indigo-500 p-4 rounded-lg mb-6">
      <div class="flex justify-between items-center">
        <div>
          <h2 class="font-bold text-lg">Currently Serving</h2>
          <p class="text-indigo-800">Service: {{ currentClient.service.name }}</p>
        </div>
        <div :class="currentClient.overtime > 0 ? 'text-red-600' : 'text-green-600'">
          <font-awesome-icon :icon="['fas', 'clock']" class="inline mr-1" />
          <span class="font-bold">
            {{ currentClient.overtime > 0 
              ? `Overtime: ${formatTime(currentClient.overtime)}` 
              : `Remaining: ${formatTime(currentClient.remainingTime)}` }}
          </span>
        </div>
      </div>
    </div>
    
    <!-- Queue status -->
    <div class="bg-white rounded-lg border shadow-sm mb-6">
      <div class="p-4 border-b bg-gray-50">
        <div class="flex justify-between items-center">
          <h2 class="font-bold text-lg">Queue Status</h2>
          <button 
            @click="fetchQueue" 
            class="p-2 text-blue-600 hover:text-blue-900 rounded-full transition-colors"
            :disabled="refreshing"
          >
            <font-awesome-icon :icon="['fas', 'arrows-rotate']" :class="`h-5 w-5 ${refreshing ? 'animate-spin' : ''}`" />
          </button>
        </div>
        <p class="text-gray-600">
          {{ waitingClients.length }} {{ waitingClients.length === 1 ? 'person' : 'people' }} waiting
        </p>
      </div>
      
      <div class="divide-y">
        <div v-if="waitingClients.length === 0" class="p-6 text-center text-gray-500">
          No clients currently waiting
        </div>
        <div 
          v-else
          v-for="(client, index) in waitingClients.slice(0, 3)" 
          :key="client.id" 
          class="p-4 flex justify-between items-center"
        >
          <div>
            <p class="font-bold">Position {{ index + 1 }}</p>
            <p class="text-gray-600">{{ client.service.name }}</p>
          </div>
          <div class="text-right">
            <p class="font-medium">Est. wait: {{ formatTime(client.waitTime) }}</p>
          </div>
        </div>
        
        <div v-if="waitingClients.length > 3" class="p-4 text-center text-gray-600">
          +{{ waitingClients.length - 3 }} more clients in queue
        </div>
      </div>
    </div>
    </div>
    </div>

    <div class="col-md-6">
    
    
    <!-- Action buttons -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
      <button 
        @click="toggleJoinQueue"
        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 px-6 rounded-lg flex justify-between items-center transition-colors"
      >
        <span class="flex items-center">
          <font-awesome-icon :icon="['fas', 'user']" class="mr-2" />
          Join Queue
        </span>
        <font-awesome-icon v-if="isJoinQueueOpen" :icon="['fas', 'chevron-up']" />
        <font-awesome-icon v-else :icon="['fas', 'chevron-down']" />
      </button>
      
      <button 
        @click="toggleCheckPosition"
        class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 px-6 rounded-lg flex justify-between items-center transition-colors"
      >
        <span class="flex items-center">
          <font-awesome-icon :icon="['fas', 'search']" class="mr-2" />
          Check Position
        </span>
        <font-awesome-icon v-if="isCheckPositionOpen" :icon="['fas', 'chevron-up']" />
        <font-awesome-icon v-else :icon="['fas', 'chevron-down']" />
      </button>
    </div>
    
    <!-- Join Queue Form -->
    <div v-if="isJoinQueueOpen" class="bg-white rounded-lg border shadow-sm mb-6 overflow-hidden transition-all">
      <div class="p-4 border-b bg-blue-50">
        <h2 class="font-bold text-lg text-blue-800">Join the Queue</h2>
        <p class="text-sm text-blue-800">Fill out the form below to join the queue</p>
      </div>
      
      <div v-if="joinSuccess" class="p-6">
        <div v-if="typeof joinSuccess === 'string'" class="text-center">
          <p>{{ joinSuccess }}</p>
        </div>
        <div v-else class="text-center bg-green-50 p-4 rounded-lg">
          <h3 class="text-green-800 font-bold text-lg mb-2">{{ joinSuccess.message }}</h3>
          <div class="bg-white p-4 rounded border mb-4">
            <p class="font-bold text-lg">Your Reference</p>
            <p class="text-2xl font-bold text-blue-600">{{ joinSuccess.reference }}</p>
            <p class="text-sm text-gray-600 mt-1">Keep this code to check your position</p>
          </div>
          <p class="font-medium">Your Position: {{ joinSuccess.position }}</p>
          <p class="font-medium">Estimated Wait Time: {{ joinSuccess.waitTime }}</p>
          <button 
            @click="joinSuccess = null"
            class="mt-4 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
          >
            Done
          </button>
        </div>
      </div>
      <div v-else class="p-6">
        <div class="grid gap-4">
          <div>
            <label class="block text-gray-700 text-sm font-bold mb-2" for="name">
              Name*
            </label>
            <input
              id="name"
              type="text"
              v-model="newClient.name"
              :class="`w-full p-3 border rounded-md ${formErrors.name ? 'border-red-500' : 'border-gray-300'}`"
              placeholder="Your full name"
            />
            <p v-if="formErrors.name" class="text-red-500 text-xs mt-1">{{ formErrors.name }}</p>
          </div>
          
          <div>
            <label class="block text-gray-700 text-sm font-bold mb-2" for="phoneNumber">
              Phone Number*
            </label>
            <input
              id="phoneNumber"
              type="tel"
              v-model="newClient.phoneNumber"
              :class="`w-full p-3 border rounded-md ${formErrors.phoneNumber ? 'border-red-500' : 'border-gray-300'}`"
              placeholder="Your phone number"
            />
            <p v-if="formErrors.phoneNumber" class="text-red-500 text-xs mt-1">{{ formErrors.phoneNumber }}</p>
          </div>
          
          <div>
            <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
              Email (optional)
            </label>
            <input
              id="email"
              type="email"
              v-model="newClient.email"
              :class="`w-full p-3 border rounded-md ${formErrors.email ? 'border-red-500' : 'border-gray-300'}`"
              placeholder="Your email address"
            />
            <p v-if="formErrors.email" class="text-red-500 text-xs mt-1">{{ formErrors.email }}</p>
          </div>
          
          <div>
            <label class="block text-gray-700 text-sm font-bold mb-2" for="service_id">
              Service*
            </label>
            <select
              id="service_id"
              v-model="newClient.service_id"
              :class="`w-full p-3 border rounded-md ${formErrors.service_id ? 'border-red-500' : 'border-gray-300'}`"
            >
              <option value="">Select a service</option>
              <option v-for="service in services" :key="service.id" :value="service.id">
                {{ service.name }} ({{ formatTime(service.duration) }}) - {{ formatPrice(service.price) }}
              </option>
            </select>
            <p v-if="formErrors.service_id" class="text-red-500 text-xs mt-1">{{ formErrors.service_id }}</p>
          </div>
          
          <div v-if="newClient.service_id" class="bg-yellow-50 p-3 rounded-md border border-yellow-200 mt-2">
            <p class="font-medium text-yellow-800">
              Estimated waiting time: {{ formatTime(calculateTotalWaitTime()) }}
            </p>
          </div>
          
          <button
            @click="joinQueue"
            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-md mt-2 transition-colors"
          >
            Join Queue
          </button>
          
          <p class="text-xs text-gray-500 mt-2">
            * Required fields. By joining the queue, you agree to our terms and conditions.
          </p>
        </div>
      </div>
    </div>
    
    <!-- Check Position Form -->
    <div v-if="isCheckPositionOpen" class="bg-white rounded-lg border shadow-sm mb-6 overflow-hidden">
      <div class="p-4 border-b bg-indigo-50">
        <h2 class="font-bold text-lg text-indigo-800">Check Your Position</h2>
        <p class="text-sm text-indigo-800">Enter your name or reference number</p>
      </div>
      
      <div class="p-6">
        <div class="flex">
          <input
            type="text"
            v-model="searchQuery"
            class="flex-grow p-3 border border-gray-300 rounded-l-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
            placeholder="Name or Reference #"
          />
          <button
            @click="searchQueue"
            class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-4 py-3 rounded-r-md transition-colors"
          >
            <font-awesome-icon :icon="['fas', 'search']" />
          </button>
        </div>
        
        <div v-if="searchResult" :class="`mt-4 p-4 rounded-md ${searchResult.error ? 'bg-red-50' : 'bg-green-50'}`">
          <p v-if="searchResult.error" class="text-red-700">{{ searchResult.error }}</p>
          <div v-else>
            <div class="flex justify-between items-start">
              <div>
                <h3 class="font-bold">{{ searchResult.client }}</h3>
                <p class="font-medium">Reference: {{ searchResult.reference }}</p>
              </div>
              <button 
                @click="searchResult = null"
                class="text-gray-400 hover:text-gray-600"
              >
                <font-awesome-icon :icon="['fas', 'xmark']" />
              </button>
            </div>
            <div class="mt-2 pt-2 border-t">
              <p class="font-bold text-indigo-700">{{ searchResult.position }}</p>
              <p>Estimated wait time: {{ searchResult.waitTime }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
    </div>
    </div>
    
    <!-- Information section -->
    <div class="bg-white rounded-lg border shadow-sm p-6">
      <h2 class="font-bold text-lg mb-4">Queue Information</h2>
      <div class="space-y-4 text-gray-700">
        <p>
          <span class="font-bold">Store Hours:</span> Monday to Friday: 9am - 7pm, Saturday: 9am - 5pm
        </p>
        <p>
          <span class="font-bold">Address:</span> 123 Main Street, Anytown
        </p>
        <p>
          <span class="font-bold">Contact:</span> (123) 456-7890
        </p>
        <p class="text-sm">
          For any questions or concerns about your queue position, please contact us directly.
        </p>
      </div>
    </div>
  
  <Footer />
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import Navbar from './Navbar.vue';
import Footer from './Footer.vue';
// Import Font Awesome library - you'll need to make sure these libraries are installed
// npm install @fortawesome/fontawesome-svg-core @fortawesome/free-solid-svg-icons @fortawesome/vue-fontawesome
import { library } from '@fortawesome/fontawesome-svg-core';
import { 
  faClock, 
  faSearch, 
  faChevronDown, 
  faChevronUp, 
  faArrowsRotate,
  faUser,
  faXmark
} from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

// Add icons to library
library.add(
  faClock,
  faSearch,
  faChevronDown,
  faChevronUp,
  faArrowsRotate,
  faUser,
  faXmark
);

// State management
const currentClient = ref(null);
const waitingClients = ref([]);
const searchQuery = ref('');
const searchResult = ref(null);
const isQueueActive = ref(false);
const isJoinQueueOpen = ref(true);
const isCheckPositionOpen = ref(false);
const services = ref([]);
const refreshing = ref(false);

// Form state
const newClient = ref({
  name: '',
  phoneNumber: '',
  email: '',
  service_id: '',
});

// Form validation state
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

// Simulate fetching queue data
const fetchQueue = () => {
  // This would be replaced with your actual API call
  refreshing.value = true;
  
  // Simulate API delay
  setTimeout(() => {
    // Sample data - replace with your API call
    const sampleCurrent = {
      id: 1,
      client: { name: "John Doe" },
      service: { name: "Haircut", duration: 30 },
      reference: "JD1234",
      remainingTime: 15,
      overtime: 0
    };
    
    const sampleWaiting = [
      {
        id: 2,
        client: { name: "Jane Smith" },
        service: { name: "Styling", duration: 45 },
        reference: "JS5678",
        waitTime: 15
      },
      {
        id: 3,
        client: { name: "Alex Johnson" },
        service: { name: "Beard Trim", duration: 20 },
        reference: "AJ9012",
        waitTime: 60
      },
      {
        id: 4,
        client: { name: "Sam Brown" },
        service: { name: "Full Service", duration: 60 },
        reference: "SB3456",
        waitTime: 80
      }
    ];
    
    currentClient.value = sampleCurrent;
    waitingClients.value = sampleWaiting;
    isQueueActive.value = true;
    refreshing.value = false;
  }, 800);
};

// Simulate fetching services
const fetchServices = () => {
  // This would be replaced with your actual API call
  setTimeout(() => {
    const sampleServices = [
      { id: 1, name: "Haircut", duration: 30, price: 150 },
      { id: 2, name: "Styling", duration: 45, price: 200 },
      { id: 3, name: "Beard Trim", duration: 20, price: 100 },
      { id: 4, name: "Full Service", duration: 60, price: 300 }
    ];
    
    services.value = sampleServices;
  }, 500);
};

// Search functionality
const searchQueue = () => {
  if (!searchQuery.value.trim()) {
    searchResult.value = null;
    return;
  }
  
  const query = searchQuery.value.toLowerCase();
  
  // Include current client in search
  const allClients = [currentClient.value, ...waitingClients.value].filter(Boolean);
  
  const found = allClients.find(booking => 
    booking.client?.name?.toLowerCase().includes(query) || 
    booking.reference?.toLowerCase().includes(query)
  );
  
  if (found) {
    const position = allClients.indexOf(found);
    const estimatedTime = position === 0 ? found.remainingTime : found.waitTime;
    
    searchResult.value = {
      client: found.client.name,
      position: position === 0 ? 'Currently Being Served' : `Position ${position} in queue`,
      waitTime: formatTime(estimatedTime || 0),
      reference: found.reference
    };
  } else {
    searchResult.value = { error: 'No match found. Please check your name or reference number.' };
  }
};

// Form validation
const validateForm = () => {
  const errors = {};
  
  if (!newClient.value.name.trim()) errors.name = 'Name is required';
  if (!newClient.value.phoneNumber.trim()) errors.phoneNumber = 'Phone number is required';
  if (!newClient.value.service_id) errors.service_id = 'Please select a service';
  
  // Basic email validation
  if (newClient.value.email && !/^\S+@\S+\.\S+$/.test(newClient.value.email)) {
    errors.email = 'Please enter a valid email';
  }
  
  formErrors.value = errors;
  return Object.keys(errors).length === 0;
};

// Join queue function
const joinQueue = () => {
  if (!validateForm()) return;
  
  // This would be replaced with your actual API call
  joinSuccess.value = 'Processing...';
  
  // Simulate API delay
  setTimeout(() => {
    // Generate a random reference number
    const reference = `${newClient.value.name.substring(0, 2).toUpperCase()}${Math.floor(1000 + Math.random() * 9000)}`;
    
    joinSuccess.value = {
      message: 'Successfully added to queue!',
      reference: reference,
      position: waitingClients.value.length + 1,
      waitTime: formatTime(calculateTotalWaitTime())
    };
    
    // Reset form
    newClient.value = {
      name: '',
      phoneNumber: '',
      email: '',
      service_id: '',
    };
    
    // Refresh queue data
    fetchQueue();
  }, 1000);
};

// Calculate total wait time for a new client
const calculateTotalWaitTime = () => {
  let totalWait = 0;
  
  // Add current client's remaining time
  if (currentClient.value && currentClient.value.remainingTime) {
    totalWait += currentClient.value.remainingTime;
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

// Format time from minutes to hours and minutes
const formatTime = (minutes) => {
  if (!minutes && minutes !== 0) return 'Unknown';
  
  const roundedMinutes = Math.max(0, Math.round(minutes));
  const hours = Math.floor(roundedMinutes / 60);
  const mins = roundedMinutes % 60;
  
  if (hours === 0) {
    return `${mins} min`;
  }
  return `${hours} hr ${mins} min`;
};

// Format price 
const formatPrice = (price) => {
  return `R${parseFloat(price).toFixed(2)}`;
};

// Lifecycles hooks
let interval;
onMounted(() => {
  fetchQueue();
  fetchServices();
  
  // Set up interval to refresh queue data every minute
  interval = setInterval(() => {
    fetchQueue();
  }, 60000);
});

// Clean up interval on unmount
onUnmounted(() => {
  if (interval) clearInterval(interval);
});
</script>
```