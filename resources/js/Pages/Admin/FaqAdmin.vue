<!-- FaqAdmin.vue -->
<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import { useForm } from '@inertiajs/vue3'
import { toast } from 'vue3-toastify'
const props = defineProps({
  faqs: Array
})

const showModal = ref(false)
const editingFaq = ref(null)
const searchTerm = ref('')
const isLoading = ref(false)
const localFaqs = ref(props.faqs || [])

// Pagination
const currentPage = ref(1)
const perPage = 5
const statusFilter = ref('')

// Animation states
const tableLoaded = ref(false)

// Filtre de recherche et statut
const filteredFaqs = computed(() => {
  let filtered = localFaqs.value
  
  if (searchTerm.value) {
    const term = searchTerm.value.toLowerCase()
    filtered = filtered.filter(faq => 
      faq.question.toLowerCase().includes(term) || 
      faq.answer.toLowerCase().includes(term)
    )
  }
  
  if (statusFilter.value) {
    filtered = filtered.filter(faq => {
      if (statusFilter.value === 'active') return faq.is_active
      if (statusFilter.value === 'inactive') return !faq.is_active
      return true
    })
  }
  
  return filtered
})

// Pagination calculée
const totalPages = computed(() => Math.ceil(filteredFaqs.value.length / perPage))
const paginatedFaqs = computed(() => {
  const start = (currentPage.value - 1) * perPage
  return filteredFaqs.value.slice(start, start + perPage)
})

const goToPage = (page) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page
  }
}

const visiblePages = computed(() => {
  const total = totalPages.value
  const current = currentPage.value

  if (total <= 4) return Array.from({ length: total }, (_, i) => i + 1)

  let pages = []

  if (current <= 2) {
    pages = [1, 2, 3, 4]
  } else if (current >= total - 1) {
    pages = [total - 3, total - 2, total - 1, total]
  } else {
    pages = [current - 1, current, current + 1, current + 2]
  }

  pages = pages.filter(p => p >= 1 && p <= total)
  return pages
})

const form = useForm({
  question: '',
  answer: '',
  is_active: true
})

onMounted(() => {
  isLoading.value = true
  setTimeout(() => {
    isLoading.value = false
    tableLoaded.value = true
  }, 300)
})

const openModal = (faq = null) => {
  showModal.value = true
  if (faq) {
    editingFaq.value = faq
    form.question = faq.question
    form.answer = faq.answer
    form.is_active = faq.is_active
  } else {
    editingFaq.value = null
    form.reset()
    form.is_active = true
  }
}

const closeModal = () => {
  showModal.value = false
  form.reset()
  editingFaq.value = null
}

const submit = () => {
  if (editingFaq.value) {
    axios.put(`/api/admin/faqs/${editingFaq.value.id}`, {
      question: form.question,
      answer: form.answer,
      is_active: form.is_active
    })
    .then(response => {
      const index = localFaqs.value.findIndex(f => f.id === editingFaq.value.id)
      if (index !== -1) {
        localFaqs.value[index] = {
          ...localFaqs.value[index],
          question: form.question,
          answer: form.answer,
          is_active: form.is_active
        }
      }
      toast.success('FAQ mise à jour avec succès')

      closeModal()
    })
    .catch(error => {
      console.error('Erreur lors de la mise à jour', error)
      if (error.response && error.response.data && error.response.data.errors) {
        form.errors = error.response.data.errors
      }
    })
  } else {
    axios.post('/api/admin/faqs', {
      question: form.question,
      answer: form.answer,
      is_active: form.is_active
    })
    .then(response => {
      localFaqs.value.push(response.data.faq || {
        id: Date.now(),
        question: form.question,
        answer: form.answer,
        is_active: form.is_active,
        created_at: new Date().toISOString()
      })
      toast.success('FAQ ajoutée avec succès')
      closeModal()
    })
    .catch(error => {
      console.error('Erreur lors de la création', error)
      if (error.response && error.response.data && error.response.data.errors) {
        form.errors = error.response.data.errors
      }
    })
  }
}

const deleteFaq = (id) => {
  showConfirmDialog('Êtes-vous sûr de vouloir supprimer cette FAQ?', () => {
    axios.delete(`/api/admin/faqs/${id}`)
    .then(response => {
      localFaqs.value = localFaqs.value.filter(faq => faq.id !== id)
      toast.warning('FAQ supprimée avec succès')
    })
    .catch(error => {
      console.error('Erreur lors de la suppression', error)
    })
  })
}

// États pour les notifications
const notification = ref({
  show: false,
  message: '',
  type: 'success'
})



// État pour la boîte de dialogue de confirmation
const confirmDialog = ref({
  show: false,
  message: '',
  onConfirm: null
})

const showConfirmDialog = (message, onConfirm) => {
  confirmDialog.value = {
    show: true,
    message,
    onConfirm
  }
}

const cancelConfirmDialog = () => {
  confirmDialog.value.show = false
}

const confirmAction = () => {
  if (confirmDialog.value.onConfirm) {
    confirmDialog.value.onConfirm()
  }
  confirmDialog.value.show = false
}
</script>

<template>
  <div class="p-6 min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50">
    <!-- Notification toast -->
    <div
      v-if="notification.show"
      :class="[
        'fixed top-4 right-4 z-50 px-6 py-4 rounded-2xl shadow-2xl text-white transition-all duration-500 transform backdrop-blur-md',
        notification.type === 'success' ? 'bg-gradient-to-r from-emerald-500 to-green-600' : 'bg-gradient-to-r from-amber-500 to-orange-600'
      ]"
      style="min-width: 350px"
    >
      <div class="flex items-center">
        <div v-if="notification.type === 'success'" class="mr-3 p-1 bg-white/20 rounded-full">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
          </svg>
        </div>
        <div v-else class="mr-3 p-1 bg-white/20 rounded-full">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
          </svg>
        </div>
        <div>
          <span class="font-semibold text-lg block">{{ notification.message }}</span>
          <span class="text-sm opacity-90">{{ notification.type === 'success' ? 'Opération réussie' : 'Attention requise' }}</span>
        </div>
      </div>
    </div>

    <!-- Confirm Dialog -->
    <div
      v-if="confirmDialog.show"
      class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 backdrop-blur-sm"
    >
      <div class="bg-white rounded-3xl shadow-2xl p-8 max-w-md w-full animate-bounce-in border border-gray-100">
        <div class="text-center">
          <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-gradient-to-br from-amber-100 to-orange-100 mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
          </div>
          <h3 class="text-xl font-bold text-gray-900 mb-3">Confirmation</h3>
          <p class="text-gray-600 leading-relaxed">{{ confirmDialog.message }}</p>
        </div>
        <div class="flex justify-center space-x-4 mt-8">
          <button
            @click="cancelConfirmDialog"
            class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-all duration-200 font-medium"
          >
            Annuler
          </button>
          <button
            @click="confirmAction"
            class="px-6 py-3 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-xl hover:from-red-600 hover:to-red-700 transition-all duration-200 font-medium shadow-lg hover:shadow-xl"
          >
            Confirmer
          </button>
        </div>
      </div>
    </div>

    <!-- Header with modern design -->
    <div class="mb-8">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-4xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
            FAQ Management
          </h1>
          <p class="text-gray-600 mt-2">Gérez vos questions fréquemment posées</p>
        </div>
        <div class="flex items-center space-x-4">
          <div class="bg-white/70 backdrop-blur-sm rounded-2xl px-4 py-2 border border-white/20">
            <span class="text-sm text-gray-600">Total: </span>
            <span class="font-bold text-blue-600">{{ filteredFaqs.length }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Enhanced Filters and Search -->
    <div class="bg-white/70 backdrop-blur-sm rounded-3xl p-6 shadow-xl border border-white/20 mb-8">
      <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center space-x-4">
          <!-- Search Input -->
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </div>
            <input 
              v-model="searchTerm" 
              type="text" 
              class="w-80 pl-12 pr-4 py-3 bg-white/80 border border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 placeholder-gray-400"
              placeholder="Rechercher dans les FAQs..."
            />
          </div>

          <!-- Status Filter -->
          <div class="relative">
            <select 
              v-model="statusFilter" 
              class="appearance-none bg-white/80 border border-gray-200 rounded-2xl px-6 py-3 pr-10 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 cursor-pointer"
            >
              <option value="">📊 Tous les statuts</option>
              <option value="active">✅ Actives</option>
              <option value="inactive">❌ Inactives</option>
            </select>
            <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </div>
          </div>
        </div>

        <!-- Add Button -->
        <button 
          @click="openModal()" 
          class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-2xl hover:from-blue-600 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
          </svg>
          Ajouter une FAQ
        </button>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="isLoading" class="flex justify-center items-center py-20">
      <div class="relative">
        <div class="animate-spin rounded-full h-16 w-16 border-4 border-blue-200"></div>
        <div class="animate-spin rounded-full h-16 w-16 border-4 border-blue-600 border-t-transparent absolute top-0 left-0"></div>
      </div>
    </div>

    <!-- Main Content -->
    <div v-else class="space-y-6">
      <!-- Table Header -->
      <div class="bg-white/70 backdrop-blur-sm rounded-2xl p-6 shadow-lg border border-white/20">
        <div class="flex items-center text-sm font-bold text-gray-700 uppercase tracking-wider">
          <div class="w-1/4 text-center">
            <span class="bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">Question</span>
          </div>
          <div class="w-1/4 text-center">
            <span class="bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">Réponse</span>
          </div>
          <div class="w-1/4 text-center">
            <span class="bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">Statut</span>
          </div>
          <div class="w-1/4 text-center">
            <span class="bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">Actions</span>
          </div>
        </div>
      </div>

      <!-- Table Rows (keeping original card design) -->
      <div class="space-y-4">
        <div v-for="(faq, index) in paginatedFaqs" :key="faq.id"
          class="flex items-center justify-between bg-white rounded-xl px-6 py-6 hover:shadow-md transition"
          style="
            height: 70px;
            background: var(--Color, #FFF);
            box-shadow: 10px 8px 20px 0px rgba(0, 81, 131, 0.25);
            border-radius: 15px;
          "
          :class="tableLoaded ? 'animate-fade-in-up' : ''"
          :style="{ animationDelay: `${index * 50}ms` }"
        >
          <!-- Question -->
          <div class="w-1/4 text-center text-sm px-2" style="color: #0093EE;">
            <div class="truncate">{{ faq.question }}</div>
          </div>

          <!-- Réponse -->
          <div class="w-1/4 text-center text-sm px-2" style="color: #0093EE;">
            <div class="truncate">{{ faq.answer }}</div>
          </div>

          <!-- Statut -->
          <div class="w-1/4 text-center">
            <span class="px-4 py-1 rounded-full text-sm font-semibold" :class="{
              'bg-green-100 text-green-800': faq.is_active,
              'bg-red-100 text-red-800': !faq.is_active
            }">
              {{ faq.is_active ? 'Active' : 'Inactive' }}
            </span>
          </div>

          <!-- Actions -->
          <div class="w-1/4 text-center">
            <div class="flex justify-center space-x-2">
              <button 
                @click="openModal(faq)" 
                class="text-blue-600 hover:text-blue-900 transition focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-md p-2"
                :aria-label="`Modifier la FAQ: ${faq.question}`"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                  <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                </svg>
              </button>
              <button 
                @click="deleteFaq(faq.id)" 
                class="text-red-600 hover:text-red-900 transition focus:outline-none focus:ring-2 focus:ring-red-500 rounded-md p-2"
                :aria-label="`Supprimer la FAQ: ${faq.question}`"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="paginatedFaqs.length === 0" class="bg-white/70 backdrop-blur-sm rounded-3xl p-12 text-center shadow-xl border border-white/20">
        <div class="flex flex-col items-center justify-center">
          <div class="bg-gradient-to-br from-gray-100 to-gray-200 rounded-full p-6 mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <h3 class="text-2xl font-bold text-gray-800 mb-2">Aucune FAQ trouvée</h3>
          <p class="text-gray-600 mb-6">{{ searchTerm ? 'Essayez avec un autre terme de recherche' : 'Commencez par ajouter votre première FAQ' }}</p>
          <button 
            v-if="!searchTerm"
            @click="openModal()" 
            class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-2xl hover:from-blue-600 hover:to-indigo-700 transition-all duration-200 shadow-lg hover:shadow-xl"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Créer ma première FAQ
          </button>
        </div>
      </div>

      <!-- Enhanced Pagination -->
      <div v-if="totalPages > 1" class="flex justify-center items-center gap-6 mt-12">
        <button
          @click="goToPage(currentPage - 1)"
          :disabled="currentPage === 1"
          class="flex items-center px-4 py-2 bg-white/70 backdrop-blur-sm border border-gray-200 rounded-xl text-gray-700 hover:bg-white transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed shadow-md"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
          </svg>
          Précédent
        </button>

        <div class="flex items-center gap-2">
          <button
            v-for="page in visiblePages"
            :key="page"
            @click="goToPage(page)"
            :class="[
              'w-12 h-12 rounded-xl transition-all duration-200 font-medium',
              page === currentPage 
                ? 'bg-gradient-to-r from-blue-500 to-indigo-600 text-white shadow-lg transform scale-110' 
                : 'bg-white/70 backdrop-blur-sm text-gray-700 hover:bg-white border border-gray-200'
            ]"
          >
            {{ page }}
          </button>
        </div>

        <button
          @click="goToPage(currentPage + 1)"
          :disabled="currentPage === totalPages"
          class="flex items-center px-4 py-2 bg-white/70 backdrop-blur-sm border border-gray-200 rounded-xl text-gray-700 hover:bg-white transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed shadow-md"
        >
          Suivant
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
          </svg>
        </button>
      </div>
    </div>

    <!-- Enhanced Modal -->
    <div
      v-if="showModal"
      class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 backdrop-blur-sm"
      @click.self="closeModal"
    >
      <div class="bg-white rounded-xl shadow-xl w-full max-w-3xl p-0 relative animate-zoom-in border border-gray-100">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-8 py-6 rounded-t-3xl border-b border-gray-100">
          <h2 class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
            {{ editingFaq ? 'Modifier une FAQ' : 'Créer une nouvelle FAQ' }}
          </h2>
          <p class="text-gray-600 mt-1">{{ editingFaq ? 'Mettez à jour les informations de la FAQ' : 'Ajoutez une nouvelle question fréquemment posée' }}</p>
        </div>
        
        <!-- Body -->
        <div class="px-8 py-6">
          <div class="space-y-6">
            <div>
              <label for="question-input" class="block text-sm font-semibold text-gray-700 mb-2">Question</label>
              <input
                id="question-input"
                type="text"
                v-model="form.question"
                placeholder="Entrez la question"
                class="w-full border border-gray-300 rounded-xl px-3 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none shadow-sm transition-all duration-200"
              />
              <div v-if="form.errors.question" class="text-sm text-red-500 mt-2 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ form.errors.question }}
              </div>
            </div>

            <div>
              <label for="answer-textarea" class="block text-sm font-semibold text-gray-700 mb-2">Réponse</label>
              <textarea
                id="answer-textarea"
                v-model="form.answer"
                placeholder="Entrez la réponse"
                rows="6"
                class="w-full border border-gray-300 rounded-2xl px-4 py-4 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none shadow-sm transition-all duration-200 resize-none"
              ></textarea>
              <div v-if="form.errors.answer" class="text-sm text-red-500 mt-2 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ form.errors.answer }}
              </div>
            </div>

            <div class="flex items-center p-4 bg-gray-50 rounded-2xl">
              <div class="relative inline-block w-12 mr-4 align-middle select-none">
                <input 
                  type="checkbox" 
                  id="is_active" 
                  v-model="form.is_active" 
                  class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer transition-all duration-300 ease-in-out"
                />
                <label 
                  for="is_active" 
                  class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-300 cursor-pointer transition-all duration-300"
                ></label>
              </div>
              <div>
                <label for="is_active" class="text-sm font-semibold text-gray-700 block">Activer cette FAQ</label>
                <p class="text-xs text-gray-500 mt-1">Cette FAQ sera visible aux utilisateurs</p>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Footer -->
        <div class="bg-gray-50 px-8 py-4 rounded-b-xl border-t flex justify-end space-x-3">
          <button
            @click="closeModal"
            class="px-4 py-3 border border-gray-300 rounded-2xl text-gray-700 hover:bg-gray-100 transition-all duration-200 font-medium"
          >
            Annuler
          </button>
          <button
            @click="submit"
            :disabled="form.processing"
            :class="[
              'px-8 py-3 text-white rounded-2xl transition-all duration-200 font-medium shadow-lg hover:shadow-xl',
              form.processing ? 'bg-blue-400 cursor-not-allowed' : 'bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700'
            ]"
          >
            <div class="flex items-center">
              <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              {{ editingFaq ? 'Mettre à jour' : 'Ajouter' }}
            </div>
          </button>
        </div>
        
        <!-- Close button -->
        <button 
          @click="closeModal" 
          class="absolute top-6 right-6 text-gray-400 hover:text-gray-600 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 rounded-full p-2 hover:bg-gray-100"
          aria-label="Fermer la modale"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
    </div>
  </div>
</template>

<style scoped>
@keyframes fade-in-up {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes zoom-in {
  from {
    opacity: 0;
    transform: scale(0.95);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}

@keyframes bounce-in {
  0% {
    opacity: 0;
    transform: scale(0.8);
  }
  70% {
    transform: scale(1.05);
  }
  100% {
    opacity: 1;
    transform: scale(1);
  }
}

.animate-fade-in-up {
  animation: fade-in-up 0.4s ease-out forwards;
}

.animate-zoom-in {
  animation: zoom-in 0.4s ease-out;
}

.animate-bounce-in {
  animation: bounce-in 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

/* Enhanced Toggle Switch */
.toggle-checkbox:checked {
  transform: translateX(100%);
  border-color: #3b82f6;
  background-color: #3b82f6;
}
.toggle-checkbox:checked + .toggle-label {
  background: linear-gradient(90deg, #3b82f6, #1d4ed8);
}

.toggle-label {
  background: linear-gradient(90deg, #d1d5db, #9ca3af);
  transition: all 0.3s ease;
}

/* Custom scrollbar */
::-webkit-scrollbar {
  width: 8px;
}

::-webkit-scrollbar-track {
  background: #f1f5f9;
  border-radius: 4px;
}

::-webkit-scrollbar-thumb {
  background: linear-gradient(to bottom, #3b82f6, #1d4ed8);
  border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
  background: linear-gradient(to bottom, #1d4ed8, #1e40af);
}

/* Glassmorphism effect */
.glass-effect {
  background: rgba(255, 255, 255, 0.25);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.18);
}

/* Smooth transitions for all interactive elements */
button, input, select, textarea {
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Hover effects */
.hover-lift:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

/* Focus states */
input:focus, textarea:focus, select:focus {
  transform: scale(1.02);
  box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
}

/* Loading animation enhancement */
@keyframes pulse-ring {
  0% {
    transform: scale(0.33);
  }
  80%, 100% {
    opacity: 0;
  }
}

.pulse-ring {
  animation: pulse-ring 1.25s cubic-bezier(0.215, 0.61, 0.355, 1) infinite;
}

/* Card hover effect */
.card-hover:hover {
  transform: translateY(-4px);
  box-shadow: 0 20px 40px rgba(0, 81, 131, 0.15);
}

/* Notification slide-in effect */
@keyframes slide-in-right {
  from {
    transform: translateX(100%);
    opacity: 0;
  }
  to {
    transform: translateX(0);
    opacity: 1;
  }
}

.notification-enter {
  animation: slide-in-right 0.4s ease-out;
}

/* Button press effect */
.btn-press:active {
  transform: scale(0.98);
}

/* Gradient text animation */
@keyframes gradient-shift {
  0% {
    background-position: 0% 50%;
  }
  50% {
    background-position: 100% 50%;
  }
  100% {
    background-position: 0% 50%;
  }
}

.gradient-text {
  background: linear-gradient(-45deg, #3b82f6, #8b5cf6, #06b6d4, #10b981);
  background-size: 400% 400%;
  animation: gradient-shift 3s ease infinite;
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

/* Modal backdrop blur animation */
@keyframes backdrop-blur-in {
  from {
    backdrop-filter: blur(0px);
    background-color: rgba(0, 0, 0, 0);
  }
  to {
    backdrop-filter: blur(8px);
    background-color: rgba(0, 0, 0, 0.6);
  }
}

.modal-backdrop {
  animation: backdrop-blur-in 0.3s ease-out;
}

/* Status badge animations */
.status-badge {
  position: relative;
  overflow: hidden;
}

.status-badge::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
  transition: left 0.5s;
}

.status-badge:hover::before {
  left: 100%;
}

/* Enhanced pagination */
.pagination-btn {
  position: relative;
  overflow: hidden;
}

.pagination-btn::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 0;
  height: 100%;
  background: linear-gradient(90deg, #3b82f6, #1d4ed8);
  transition: width 0.3s ease;
  z-index: -1;
}

.pagination-btn:hover::before {
  width: 100%;
}

/* Form validation styles */
.input-error {
  border-color: #ef4444;
  box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
}

.input-success {
  border-color: #10b981;
  box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}

/* Mobile responsive improvements */
@media (max-width: 768px) {
  .mobile-stack {
    flex-direction: column;
    gap: 1rem;
  }
  
  .mobile-full {
    width: 100%;
  }
  
  .mobile-text-center {
    text-align: center;
  }
}

/* Dark mode support (if needed) */
@media (prefers-color-scheme: dark) {
  .dark-mode {
    background: linear-gradient(135deg, #1e293b, #334155);
    color: white;
  }
  
  .dark-mode .glass-effect {
    background: rgba(30, 41, 59, 0.8);
    border: 1px solid rgba(255, 255, 255, 0.1);
  }
}

/* Performance optimizations */
.will-change-transform {
  will-change: transform;
}

.will-change-opacity {
  will-change: opacity;
}

/* Accessibility improvements */
.focus-visible:focus-visible {
  outline: 2px solid #3b82f6;
  outline-offset: 2px;
}

/* Print styles */
@media print {
  .no-print {
    display: none !important;
  }
  
  .print-friendly {
    background: white !important;
    color: black !important;
    box-shadow: none !important;
  }
}
</style>