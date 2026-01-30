<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50 py-8 px-4">
    <div class="max-w-4xl mx-auto">
      <!-- Header avec gradient -->
      <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-blue-600 to-purple-600 rounded-full mb-4">
          <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
          </svg>
        </div>
        <h1 class="text-4xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent mb-2">
          Mon Profil Startup
        </h1>
        <p class="text-gray-600 text-lg">Gérez les informations de votre entreprise</p>
      </div>

      <!-- Formulaire principal -->
      <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
        <!-- En-tête du formulaire -->
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-8 py-6">
          <h2 class="text-2xl font-semibold text-white">Informations de l'entreprise</h2>
          <p class="text-blue-100 mt-1">Complétez votre profil pour une meilleure visibilité</p>
        </div>

        <form @submit="submitForm" enctype="multipart/form-data" class="p-8">
  <!-- Section Logo -->
  <div class="mb-8">
    <div class="flex items-center gap-6">
      <div class="flex-shrink-0">
        <div v-if="startup.logo_startup" class="relative group">
          <img
            :src="'/storage/' + startup.logo_startup"
            :alt="startup.name ? `Logo de ${startup.name}` : 'Logo de l\'entreprise'"
          />
          <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 rounded-2xl transition-all duration-300 flex items-center justify-center">
            <svg class="w-6 h-6 text-white opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
          </div>
        </div>
        <div v-else class="w-24 h-24 rounded-2xl bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center border-2 border-dashed border-gray-300">
          <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
          </svg>
        </div>
      </div>
      <div class="flex-1">
        <label for="logo_startup" class="block text-sm font-semibold text-gray-700 mb-2">Logo de l'entreprise</label>
        <div class="relative">
          <input
            id="logo_startup"
            type="file"
            name="logo_startup"
            @change="handleFileUpload"
            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
            accept="image/*"
          />
          <div class="flex items-center gap-3 px-4 py-3 border-2 border-dashed border-gray-300 rounded-xl hover:border-blue-400 hover:bg-blue-50 transition-colors cursor-pointer">
            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
            </svg>
            <span class="text-sm text-gray-600">Cliquez pour télécharger ou glissez votre logo ici</span>
          </div>
        </div>
        <p class="text-xs text-gray-500 mt-1">PNG, JPG jusqu'à 2MB. Format carré recommandé.</p>
      </div>
    </div>
  </div>

  <!-- Informations principales -->
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Nom -->
    <div class="form-group">
      <label for="name" class="form-label flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
        </svg>
        Nom de l'entreprise
      </label>
      <input
        id="name"
        name="name"
        v-model="form.name"
        class="form-input"
        type="text"
        required
        placeholder="Ex: InnovTech Solutions"
      />
    </div>

    <!-- Email -->
    <div class="form-group">
      <label for="email" class="form-label flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
        </svg>
        Email professionnel
      </label>
      <input
        id="email"
        name="email"
        v-model="form.email"
        class="form-input"
        type="email"
        required
        placeholder="contact@entreprise.com"
      />
    </div>

    <!-- Téléphone -->
    <div class="form-group">
      <label for="phone_number" class="form-label flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
        </svg>
        Téléphone
      </label>
      <input
        id="phone_number"
        name="phone_number"
        v-model="form.phone_number"
        class="form-input"
        type="tel"
        required
        placeholder="+216 XX XXX XXX"
      />
    </div>

    <!-- Site Web -->
    <div class="form-group">
      <label for="website_link" class="form-label flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9v-9m0-9v9m0 9c-5 0-9-4-9-9s4-9 9-9"/>
        </svg>
        Site Web
      </label>
      <input
        id="website_link"
        name="website_link"
        v-model="form.website_link"
        class="form-input"
        type="url"
        placeholder="https://www.votre-site.com"
      />
    </div>
  </div>

  <!-- Spécialité -->
  <div class="form-group mb-6">
    <label for="sector_id" class="form-label flex items-center gap-2">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
      </svg>
      Domaine d'activité
    </label>
    <div class="relative">
      <select
        id="sector_id"
        name="sector_id"
        v-model="form.sector_id"
        class="form-input appearance-none pr-10"
        required
      >
        <option value="">-- Sélectionner un domaine --</option>
        <option v-for="sector in sectors" :key="sector.id" :value="sector.id">
          {{ sector.name }}
        </option>
      </select>
      <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
      </div>
    </div>
  </div>

  <!-- Description -->
  <div class="form-group mb-8">
    <label for="description" class="form-label flex items-center gap-2">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
      </svg>
      Description de l'entreprise
    </label>
    <textarea
      id="description"
      name="description"
      v-model="form.description"
      rows="5"
      class="form-input resize-none"
      placeholder="Décrivez votre startup, votre mission, vos produits/services, et ce qui vous rend unique..."
    ></textarea>
    <div class="flex justify-between items-center mt-2">
      <p class="text-xs text-gray-500">Décrivez votre startup en quelques phrases percutantes</p>
      <span class="text-xs text-gray-400">{{ form.description?.length || 0 }}/500</span>
    </div>
  </div>

  <!-- Boutons d'action -->
  <div class="flex items-center justify-between pt-6 border-t border-gray-200">
    <button
      type="button"
      class="px-6 py-3 text-gray-600 border border-gray-300 rounded-xl hover:bg-gray-50 hover:border-gray-400 transition-colors font-medium"
    >
      Annuler
    </button>
    <button
      type="submit"
      class="px-8 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl hover:from-blue-700 hover:to-purple-700 transform hover:scale-105 transition-all duration-200 font-semibold shadow-lg hover:shadow-xl flex items-center gap-2"
      :disabled="isLoading"
    >
      <span v-if="isLoading">Enregistrement...</span>
      <span v-else class="flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        Enregistrer le profil
      </span>
    </button>
  </div>
</form>

      </div>

      <!-- Conseils -->
      <div class="mt-8 bg-blue-50 border border-blue-200 rounded-2xl p-6">
        <div class="flex items-start gap-4">
          <div class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
          <div>
            <h3 class="font-semibold text-blue-900 mb-2">Conseils pour optimiser votre profil</h3>
            <ul class="text-sm text-blue-800 space-y-1">
              <li>• Utilisez un logo haute qualité au format carré</li>
              <li>• Rédigez une description claire et engageante</li>
              <li>• Assurez-vous que vos coordonnées sont à jour</li>
              <li>• Mentionnez vos réalisations et votre valeur ajoutée</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { toast } from 'vue3-toastify'

const props = defineProps({
  user: Object,
  startup: Object,
  sectors: Array, 
  flashMessage: String,
})

const isLoading = ref(false);
const logoFile = ref(null);

// Formulaire local (mais envoyé en POST natif)
const form = ref({
  name: props.user?.name ?? '',
  email: props.user?.email ?? '',
  specialty: props.user?.specialty ?? '',
  phone_number: props.startup?.phone_number ?? '',
  website_link: props.startup?.website_link ?? '',
  description: props.startup?.description ?? '',
  sector_id: props.startup?.sector_id ?? '', 
})

// Récupère le token CSRF pour l'envoyer
const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? ''

function submitForm(event) {
  event.preventDefault();

  const formData = new FormData();
  formData.append('name', form.value.name);
  formData.append('email', form.value.email);
  formData.append('phone_number', form.value.phone_number);
  formData.append('website_link', form.value.website_link);
  formData.append('description', form.value.description);
  formData.append('sector_id', form.value.sector_id);

  if (logoFile.value) {
    formData.append('logo_startup', logoFile.value);
  }

  isLoading.value = true;
  
  fetch('/startup/profile', {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': csrf,
      'Accept': 'application/json',
    },
    body: formData,
  })
  .then(response => {
    if (!response.ok) throw new Error('Échec lors de l\'enregistrement.');
    return response.json();
  })
  .then(data => {
    toast.success('Profil mis à jour avec succès');
  })
  .catch(error => {
    toast.error('Une erreur est survenue : ' + error.message);
  })
  .finally(() => {
    isLoading.value = false;
  });
}

function handleFileUpload(e) {
  logoFile.value = e.target.files[0];
}

// Afficher un toast si flashMessage existe
onMounted(() => {
  if (props.flashMessage) {
    toast.success(props.flashMessage)
  }
})
</script>

<style scoped>
.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.form-label {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.875rem;
  font-weight: 600;
  color: #374151;
}

.form-input {
  width: 100%;
  padding: 0.75rem 1rem;
  border: 1px solid #d1d5db;
  border-radius: 0.75rem;
  background-color: white;
  transition: all 0.2s ease-in-out;
  color: #111827;
}

.form-input::placeholder {
  color: #9ca3af;
}

.form-input:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1), 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
  transform: scale(1.02);
}

.form-input:hover {
  border-color: #9ca3af;
}

/* Animation pour les champs requis */
.form-input:invalid:not(:placeholder-shown) {
  border-color: #fca5a5;
  background-color: #fef2f2;
}

.form-input:valid:not(:placeholder-shown) {
  border-color: #86efac;
  background-color: #f0fdf4;
}

/* Smooth scrolling pour les mobiles */
@media (max-width: 768px) {
  .form-input:focus {
    transform: none;
    scale: 1;
  }
}
</style>