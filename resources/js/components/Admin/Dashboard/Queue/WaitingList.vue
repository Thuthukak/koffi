<template>
  <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
    <!-- Header -->
    <div class="px-4 sm:px-6 py-4 border-b border-gray-200 bg-gray-50">
      <h2 class="text-lg sm:text-xl font-bold text-gray-900 flex items-center gap-2">
        
        {{ title }}
        <span v-if="waitingClients.length > 0" class="ml-auto bg-blue-100 text-blue-800 text-sm font-medium px-2.5 py-0.5 rounded-full">
          {{ waitingClients.length }}
        </span>
      </h2>
    </div>
    
    <!-- Empty State -->
    <div v-if="waitingClients.length === 0" class="p-8 sm:p-12 text-center">
      <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
      </svg>
      <h3 class="text-lg font-medium text-gray-900 mb-2">{{ emptyStateTitle }}</h3>
      <p class="text-gray-500">{{ emptyStateMessage }}</p>
    </div>
    
    <!-- Mobile Cards View (shown on mobile) -->
    <div v-else class="block lg:hidden">
      <div v-for="(client, index) in waitingClients" :key="client.id" 
           class="border-b border-gray-100 last:border-b-0">
        <div class="p-4 space-y-3">
          <div class="flex items-start justify-between">
            <div class="flex items-center gap-3">
              <div class="flex-shrink-0 w-8 h-8 bg-blue-100 text-blue-800 rounded-full flex items-center justify-center text-sm font-bold">
                {{ index + 1 }}
              </div>
              <div>
                <h3 class="font-semibold text-gray-900 text-base">{{ client.client.name }}</h3>
                <p class="text-sm text-gray-600">{{ client.service.name }}</p>
                <p v-if="client.barber" class="text-xs text-gray-500">{{ client.barber.name }}</p>
              </div>
            </div>
            <div class="flex items-center gap-2">
              <!-- Optional: Move Up/Down buttons for mobile -->
              <div v-if="showPositionControls" class="flex flex-col gap-1">
                <button 
                  v-if="index > 0"
                  @click="$emit('moveClient', client.id, 'up')" 
                  class="p-1 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded transition-colors"
                  title="Move up"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                  </svg>
                </button>
                <button 
                  v-if="index < waitingClients.length - 1"
                  @click="$emit('moveClient', client.id, 'down')" 
                  class="p-1 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded transition-colors"
                  title="Move down"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                  </svg>
                </button>
              </div>
              <button 
                @click="handleRemoveClient(client)" 
                class="p-2 text-red-500 hover:text-red-700 hover:bg-red-50 rounded transition-colors"
                title="Remove client"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
              </button>
            </div>
          </div>
          
          <div class="flex items-center justify-between text-sm">
            <div class="flex items-center gap-4">
              <span class="inline-flex items-center gap-1 text-gray-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ formatTime(client.waitTime) }}
              </span>
              <span class="text-gray-400">•</span>
              <span class="text-gray-600">{{ client.service.duration }}min</span>
              <span v-if="client.service.price" class="text-gray-400">•</span>
              <span v-if="client.service.price" class="text-gray-600">{{ formatPrice(client.service.price) }}</span>
            </div>
            <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">
              {{ client.reference }}
            </span>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Desktop Table View (hidden on mobile) -->
    <div v-else class="hidden lg:block overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
            <th v-if="showBarberColumn" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Barber</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Wait Time</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="(client, index) in waitingClients" :key="client.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-blue-100 text-blue-800 rounded-full flex items-center justify-center text-sm font-bold">
                  {{ index + 1 }}
                </div>
                <!-- Position controls for desktop -->
                <div v-if="showPositionControls" class="flex flex-col">
                  <button 
                    v-if="index > 0"
                    @click="$emit('moveClient', client.id, 'up')" 
                    class="p-1 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded transition-colors"
                    title="Move up"
                  >
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                    </svg>
                  </button>
                  <button 
                    v-if="index < waitingClients.length - 1"
                    @click="$emit('moveClient', client.id, 'down')" 
                    class="p-1 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded transition-colors"
                    title="Move down"
                  >
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                  </button>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="font-medium text-gray-900">{{ client.client.name }}</div>
              <div v-if="client.client.phoneNumber" class="text-sm text-gray-500">{{ client.client.phoneNumber }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-gray-900">{{ client.service.name }}</div>
              <div class="text-sm text-gray-500 flex items-center gap-2">
                <span>{{ client.service.duration }} minutes</span>
                <span v-if="client.service.price">• {{ formatPrice(client.service.price) }}</span>
              </div>
            </td>
            <td v-if="showBarberColumn" class="px-6 py-4 whitespace-nowrap">
              <div v-if="client.barber" class="text-sm text-gray-900">{{ client.barber.name }}</div>
              <div v-else class="text-sm text-gray-400 italic">Not assigned</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                    :class="getWaitTimeColor(client.waitTime)">
                {{ formatTime(client.waitTime) }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ client.reference }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center gap-2">
                <button 
                  @click="$emit('jumpClient', client.id)"
                  class="px-3 py-1 bg-yellow-500 text-white text-sm rounded hover:bg-yellow-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                  :disabled="client.skipCount >= 3"
                  :title="client.skipCount >= 3 ? 'Client has reached maximum jump limit' : `Jump client (${3 - client.skipCount} jumps remaining)`"
                >
                  <span class="flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                    </svg>
                    Jump
                    <span v-if="client.skipCount > 0" class="text-xs">
                      ({{ 3 - client.skipCount }})
                    </span>
                  </span>
                </button>
                <div v-if="client.skipCount > 0" class="mt-1">
                  <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-yellow-100 text-yellow-800">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.728-.833-2.498 0L3.316 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                    Jumped {{ client.skipCount }} time{{ client.skipCount > 1 ? 's' : '' }}
                  </span>
                </div>
                <button 
                  v-if="showEditButton"
                  @click="$emit('editClient', client)" 
                  class="inline-flex items-center px-3 py-1 border border-gray-300 text-sm leading-4 font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
                >
                  <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                  </svg>
                  Edit
                </button>
                <button 
                  @click="handleRemoveClient(client)" 
                  class="inline-flex items-center px-3 py-1 border border-red-300 text-sm leading-4 font-medium rounded text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors"
                >
                  <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                  </svg>
                  Remove
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
export default {
  name: "WaitingList",
  props: {
    waitingClients: {
      type: Array,
      default: () => []
    },
    title: {
      type: String,
      default: 'Waiting List'
    },
    emptyStateTitle: {
      type: String,
      default: 'No clients in queue'
    },
    emptyStateMessage: {
      type: String,
      default: 'Add walk-in clients or wait for bookings to appear here.'
    },
    showBarberColumn: {
      type: Boolean,
      default: false
    },
    showPositionControls: {
      type: Boolean,
      default: false
    },
    showEditButton: {
      type: Boolean,
      default: false
    },
    confirmRemove: {
      type: Boolean,
      default: true
    }
  },
  emits: ['removeClient', 'editClient', 'moveClient','jumpClient'],
  setup(props, { emit }) {
    // Helper function to format time
    const formatTime = (minutes) => {
      const roundedMinutes = Math.max(0, Math.round(minutes || 0));
      return `${Math.floor(roundedMinutes / 60)}h ${roundedMinutes % 60}m`;
    };

    // Helper function to format price
    const formatPrice = (price) => {
      return `R${parseFloat(price).toFixed(2)}`;
    };

    // Get color class based on wait time
    const getWaitTimeColor = (waitTime) => {
      if (waitTime <= 15) {
        return 'bg-green-100 text-green-800';
      } else if (waitTime <= 30) {
        return 'bg-yellow-100 text-yellow-800';
      } else {
        return 'bg-red-100 text-red-800';
      }
    };

    // Handle remove client with optional confirmation
    const handleRemoveClient = (client) => {
      if (props.confirmRemove) {
        if (confirm(`Are you sure you want to remove ${client.client.name} from the queue?`)) {
          emit('removeClient', client.id);
        }
      } else {
        emit('removeClient', client.id);
      }
    };

    return {
      formatTime,
      formatPrice,
      getWaitTimeColor,
      handleRemoveClient
    };
  }
};
</script>