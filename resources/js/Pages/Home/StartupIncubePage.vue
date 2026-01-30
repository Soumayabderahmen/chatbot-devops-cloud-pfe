<template>
  <section class="py-12 bg-gradient-to-r from-gray-50 to-gray-100">
    <div class="container mx-auto px-4">
      <!-- Header avec design moderne -->
      <div class="text-center mb-12">
        <h2 class="font-bold text-3xl mb-3 text-gray-800">Startups <span class="text-blue-600">diplômées</span></h2>
        <div class="w-20 h-1 bg-blue-600 mx-auto mb-6"></div>
        <p class="text-gray-600 max-w-2xl mx-auto">
          Ces startups ont été incubées dans notre programme et ont traversé un parcours de transformation pour devenir des entreprises innovantes à fort potentiel.
        </p>
      </div>

      <!-- Filtres avec design amélioré -->
      <div class="bg-white rounded-lg shadow-md p-6 mb-10 max-w-3xl mx-auto">
        <div class="flex flex-col md:flex-row gap-4">
          <div class="flex-1">
            <div class="relative">
              <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
              </span>
              <input 
                type="text" 
                v-model="search" 
                class="form-input w-full py-3 pl-10 pr-4 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent" 
                placeholder="Rechercher une startup"
              >
            </div>
          </div>
          <div class="md:w-1/3">
            <div class="relative">
              <select 
                v-model="category" 
                class="form-select w-full py-3 pl-4 pr-10 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent appearance-none"
              >
                <option value="">Toutes les catégories</option>
                <option value="Agritech">Agritech</option>
                <option value="Santé">Santé</option>
                <option value="Éducation">Éducation</option>
                <option value="Mobilité">Mobilité</option>
                <option value="Environnement">Environnement</option>
                <option value="Écologie">Écologie</option>
                <option value="Domotique">Domotique</option>
              </select>
              <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Message quand aucun résultat -->
      <div v-if="filteredStartups.length === 0" class="text-center py-10">
        <p class="text-gray-600">Aucune startup ne correspond à votre recherche.</p>
      </div>

      <!-- Cartes avec design professionnel -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <div
  v-for="(startup, index) in filteredStartups"
  :key="index"
  class="bg-white rounded-2xl overflow-hidden shadow-md hover:shadow-xl transition duration-300 flex flex-col"
>
  <div class="relative h-48 w-full">
    <img
      :src="startup.image"
      :alt="startup.name"
      class="object-cover w-full h-full"
    />
    <span class="absolute top-3 right-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white text-xs font-semibold px-3 py-1 rounded-full shadow-lg">
      {{ startup.category }}
    </span>
  </div>

  <div class="p-5 flex flex-col flex-1">
    <h3 class="text-lg font-bold text-gray-800 mb-1">{{ startup.name }}</h3>
    <p class="text-sm text-gray-600 flex-1">
      {{ startup.description }}
    </p>

    <div class="mt-4 space-y-2 text-sm text-gray-600 border-t pt-3">
      <div class="flex items-center gap-2">
        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.5 4.5a1 1 0 01-.502 1.21L8 11a11 11 0 005.5 5.5l1.1-2.2a1 1 0 011.2-.5l4.5 1.5a1 1 0 01.7.95V19a2 2 0 01-2 2h-1c-7.3 0-14-6.716-14-15V5z"/>
        </svg>
        <span class="truncate">{{ startup.phone }}</span>
      </div>
      <div class="flex items-center gap-2">
        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3c4.97 0 9 4.03 9 9 0 1.71-.49 3.29-1.34 4.63L12 21l-7.66-4.37A8.963 8.963 0 013 12c0-4.97 4.03-9 9-9z"/>
        </svg>
        <a :href="startup.website" class="text-blue-600 hover:underline truncate" target="_blank">
          {{ startup.website }}
        </a>
      </div>
    </div>

    <a
      :href="startup.link"
      target="_blank"
      class="mt-4 inline-block text-center bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-xl transition duration-200"
    >
      En savoir plus
    </a>
  </div>
</div>

      </div>

      <!-- Pagination améliorée -->
      <div class="mt-12 flex justify-center">
        <nav class="inline-flex rounded-md shadow-sm" aria-label="Pagination">
          <a href="#" class="relative inline-flex items-center rounded-l-md px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0">
            <span class="sr-only">Précédent</span>
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
          </a>
          <a href="#" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-blue-600 hover:bg-blue-700 focus:z-20 focus:outline-offset-0">
            1
          </a>
          <a href="#" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0">
            2
          </a>
          <a href="#" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0">
            3
          </a>
          <a href="#" class="relative inline-flex items-center rounded-r-md px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0">
            <span class="sr-only">Suivant</span>
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
          </a>
        </nav>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

const search = ref('')
const category = ref('')
const startups = ref([])

const filteredStartups = computed(() => {
  return startups.value.filter(s =>
    (!search.value || s.name.toLowerCase().includes(search.value.toLowerCase()) || 
     s.description.toLowerCase().includes(search.value.toLowerCase())) &&
    (!category.value || s.category === category.value)
  )
})

// Chargement des données dynamiques
onMounted(() => {
  axios.get('/api/startups/graduated')
    .then(res => {
      startups.value = res.data
    })
    .catch(err => {
      console.error('Erreur lors du chargement des startups diplômées :', err)
    })
})
</script>


<style scoped>
/* Styles complémentaires pour améliorer le design */
.form-input:focus, .form-select:focus {
  outline: none;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25);
}

/* Animation pour les cartes */
.transition-transform {
  transition-property: transform;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 300ms;
}

.hover\:-translate-y-2:hover {
  --tw-translate-y: -0.5rem;
  transform: translate(var(--tw-translate-x), var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y));
}
</style>