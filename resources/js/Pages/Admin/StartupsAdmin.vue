<template>
  <div class="min-h-screen  p-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center">
          <span class="text-white text-lg">🚀</span>
        </div>
        <h1 class="text-2xl font-bold text-gray-800">Gestion des Startups</h1>
      </div>
      
      <!-- Search Bar -->
      <div class="flex items-center gap-4">
        <div class="relative">
          <input 
            type="text" 
            v-model="searchQuery"
            placeholder="Rechercher..." 
            class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-64"
          >
          <svg class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
          </svg>
        </div>
        
       
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
      <div class="bg-white p-4 rounded-lg shadow-sm border">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-gray-600">Total Startups</p>
            <p class="text-2xl font-bold text-gray-800">{{ filteredStartups.length }}</p>
          </div>
          <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
            <span class="text-blue-600 text-xl">🚀</span>
          </div>
        </div>
      </div>
      
      <div class="bg-white p-4 rounded-lg shadow-sm border">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-gray-600">Actives</p>
            <p class="text-2xl font-bold text-green-600">{{ activeStartups }}</p>
          </div>
          <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
            <span class="text-green-600 text-xl">✅</span>
          </div>
        </div>
      </div>
      
      <div class="bg-white p-4 rounded-lg shadow-sm border">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-gray-600">En attente</p>
            <p class="text-2xl font-bold text-yellow-600">{{ pendingStartups }}</p>
          </div>
          <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
            <span class="text-yellow-600 text-xl">⏳</span>
          </div>
        </div>
      </div>
      
    </div>

    <!-- DataTable -->
    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
      <!-- Table Header -->
      <div class="bg-gray-50 px-6 py-4 border-b">
        <div class="flex items-center justify-between">
          <h3 class="text-lg font-medium text-gray-800">Liste des Startups</h3>
          <span class="text-sm text-gray-600">{{ filteredStartups.length }} résultat(s)</span>
        </div>
      </div>

      <!-- Table -->
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50 border-b">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Startup</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Secteur</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date d'inscription</th>
              
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="(startup, index) in paginatedStartups" :key="startup.id" class="hover:bg-gray-50 transition-colors">
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ (currentPage - 1) * itemsPerPage + index + 1 }}
              </td>
              
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <div class="flex-shrink-0 h-10 w-10">
                    <img 
                      v-if="startup.startup?.logo_startup" 
                      :src="`/storage/${startup.startup.logo_startup}`" 
                       alt="Logo startup"
                        class="h-10 w-10 rounded-full object-cover"
                    >
       
                    <div v-else class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                      <span class="text-gray-600 text-sm font-medium">{{ getInitials(startup.name) }}</span>
                    </div>
                  </div>
                  <div class="ml-4">
                    <div class="text-sm font-medium text-gray-900">{{ startup.name }}</div>
                    <div v-if="startup.startup?.website_link" class="text-sm text-gray-500">
                      <a :href="startup.startup.website_link" target="_blank" class="hover:text-blue-600">
                        🌐 Site web
                      </a>
                    </div>
                  </div>
                </div>
              </td>
              
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">{{ startup.email }}</div>
                <div v-if="startup.startup?.phone_number" class="text-sm text-gray-500">
                  📱 {{ startup.startup.phone_number }}
                </div>
              </td>
              
              <td class="px-6 py-4 whitespace-nowrap">
                <span v-if="startup.startup?.sector?.name" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                  {{ startup.startup.sector.name }}
                </span>
                <span v-else class="text-sm text-gray-400">N/A</span>
              </td>
              
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="getStatusClass(startup.statut)" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                  {{ getStatusText(startup.statut) }}
                </span>
              </td>
              
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ formatDate(startup.created_at) }}
              </td>
              
             
            </tr>
            
            <!-- Empty State -->
            <tr v-if="filteredStartups.length === 0">
              <td colspan="7" class="px-6 py-12 text-center">
                <div class="text-gray-400">
                  <svg class="mx-auto h-12 w-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2 2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                  </svg>
                  <p class="text-sm">Aucune startup trouvée</p>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="filteredStartups.length > 0" class="bg-white px-6 py-3 border-t">
        <div class="flex items-center justify-between">
          <div class="text-sm text-gray-700">
            Affichage de {{ (currentPage - 1) * itemsPerPage + 1 }} à {{ Math.min(currentPage * itemsPerPage, filteredStartups.length) }} sur {{ filteredStartups.length }} résultats
          </div>
          
          <div class="flex items-center space-x-2">
            <select v-model="itemsPerPage" class="border border-gray-300 rounded text-sm px-2 py-1">
              <option :value="10">10 par page</option>
              <option :value="25">25 par page</option>
              <option :value="50">50 par page</option>
            </select>
            
            <button 
              @click="currentPage--" 
              :disabled="currentPage === 1"
              class="px-3 py-1 border rounded text-sm disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50"
            >
              Précédent
            </button>
            
            <span class="px-3 py-1 text-sm">{{ currentPage }} / {{ totalPages }}</span>
            
            <button 
              @click="currentPage++" 
              :disabled="currentPage === totalPages"
              class="px-3 py-1 border rounded text-sm disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50"
            >
              Suivant
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, defineProps , watch} from 'vue'

const props = defineProps({
  startups: {
    type: Array,
    default: () => []
  }
})

// Reactive data
const searchQuery = ref('')
const selectedStatus = ref('')
const selectedSector = ref('')
const currentPage = ref(1)
const itemsPerPage = ref(10)

// Computed properties
const filteredStartups = computed(() => {
  let filtered = props.startups

  // Search filter
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(startup => 
      startup.name.toLowerCase().includes(query) ||
      startup.email.toLowerCase().includes(query) ||
      (startup.startup?.sector?.name || '').toLowerCase().includes(query)
    )
  }

  // Status filter
  if (selectedStatus.value) {
    filtered = filtered.filter(startup => startup.statut === selectedStatus.value)
  }

  // Sector filter
  if (selectedSector.value) {
    filtered = filtered.filter(startup => 
      startup.startup?.sector?.name === selectedSector.value
    )
  }

  return filtered
})

const paginatedStartups = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value
  const end = start + itemsPerPage.value
  return filteredStartups.value.slice(start, end)
})

const totalPages = computed(() => {
  return Math.ceil(filteredStartups.value.length / itemsPerPage.value)
})

const uniqueSectors = computed(() => {
  const sectors = props.startups
    .map(startup => startup.startup?.sector?.name)
    .filter(Boolean)
  return [...new Set(sectors)]
})

const activeStartups = computed(() => {
  return props.startups.filter(startup => startup.statut === 'active').length
})

const pendingStartups = computed(() => {
  return props.startups.filter(startup => startup.statut === 'pending').length
})

const inactiveStartups = computed(() => {
  return props.startups.filter(startup => startup.statut === 'inactive').length
})

// Methods
const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  return new Date(dateString).toLocaleDateString('fr-FR', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const getInitials = (name) => {
  return name
    .split(' ')
    .map(word => word.charAt(0))
    .join('')
    .toUpperCase()
    .slice(0, 2)
}

const getStatusClass = (status) => {
  const classes = {
    'active': 'bg-green-100 text-green-800',
    'pending': 'bg-yellow-100 text-yellow-800',
    'inactive': 'bg-red-100 text-red-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const getStatusText = (status) => {
  const texts = {
    'active': 'Actif',
    'pending': 'En attente',
    'inactive': 'Inactif'
  }
  return texts[status] || 'Inconnu'
}




// Watch for itemsPerPage changes
watch(itemsPerPage, () => {
  currentPage.value = 1
})
</script>