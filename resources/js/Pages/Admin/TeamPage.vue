<template>
  <div class="team-admin">
    <!-- Header avec gradient -->
    <div class="admin-header">
      <div class="container-fluid">
        <div class="row align-items-center">
          <div class="col">
     
          </div>
          <div class="col-auto">
            <button class="btn btn-primary btn-lg" @click="addNewMember">
              <i class="fas fa-plus me-2"></i>
              Ajouter un membre
            </button>
          </div>
        </div>
      </div>
    </div>


    <!-- Modal Formulaire -->
<div v-if="showModal" class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form @submit.prevent="submitMember">
        <div class="modal-header">
          <h5 class="modal-title">{{ isEditMode ? 'Modifier' : 'Ajouter' }} un membre</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body row g-3">
          <div class="col-md-6">
            <label for="name">Nom</label>
            <input id="name" v-model="form.name" class="form-control" required />
          </div>
          <div class="col-md-6">
            <label for="role">Rôle</label>
            <input id="role" v-model="form.role" class="form-control" required />
          </div>
          <div class="col-md-6">
            <label for="type">Type</label>
            <select id="type" v-model="form.type" class="form-select">
              <option value="founder">Fondateur</option>
              <option value="chef">Chef</option>
              <option value="member">Membre</option>
            </select>
          </div>
          <div class="col-md-6">
            <label for="email">Email</label>
            <input id="email" v-model="form.email" class="form-control" />
          </div>
          <div class="col-md-6">
            <label for="linkedin">LinkedIn</label>
            <input id="linkedin" v-model="form.linkedin" class="form-control" />
          </div>
          <div class="col-md-6">
            <label for="twitter">Twitter</label>
            <input id="twitter" v-model="form.twitter" class="form-control" />
          </div>
          <div class="col-md-12">
            <label for="photo">Photo</label>
            <input id="photo" type="file" @change="handleImageUpload" class="form-control" />
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" @click="showModal = false">Annuler</button>
          <button class="btn btn-primary" type="submit">
            {{ isEditMode ? 'Mettre à jour' : 'Ajouter' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

  </div>
  <div class="container-fluid mt-4">
      <!-- Statistiques -->
      <div class="row mb-4">
        <div class="col-md-3">
          <div class="stat-card founder">
            <div class="stat-icon">
              <i class="fas fa-crown"></i>
            </div>
            <div class="stat-content">
              <h3>{{ getCount('founder') }}</h3>
              <p>Fondateurs</p>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="stat-card chef">
            <div class="stat-icon">
              <i class="fas fa-user-tie"></i>
            </div>
            <div class="stat-content">
              <h3>{{ getCount('chef') }}</h3>
              <p>Chefs d'équipe</p>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="stat-card member">
            <div class="stat-icon">
              <i class="fas fa-users"></i>
            </div>
            <div class="stat-content">
              <h3>{{ getCount('member') }}</h3>
              <p>Membres</p>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="stat-card total">
            <div class="stat-icon">
              <i class="fas fa-chart-bar"></i>
            </div>
            <div class="stat-content">
              <h3>{{ getTotalCount() }}</h3>
              <p>Total</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Onglets pour la vue -->
      <div class="view-toggle mb-4">
         <div class="btn-group">
          <button 
            :class="['btn', viewMode === 'table' ? 'btn-primary' : 'btn-outline-primary']"
            @click="viewMode = 'table'"
          >
            <i class="fas fa-table me-2"></i>Vue Tableau
          </button>
          <button 
            :class="['btn', viewMode === 'cards' ? 'btn-primary' : 'btn-outline-primary']"
            @click="viewMode = 'cards'"
          >
            <i class="fas fa-th-large me-2"></i>Vue Cartes
          </button>
        </div>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="text-center py-5">
        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;">
          <span class="visually-hidden">Chargement...</span>
        </div>
      </div>

      <!-- Vue Tableau -->
      <div v-else-if="viewMode === 'table'" class="table-container">
        <div class="card shadow-lg border-0">
          <div class="card-header bg-white">
            <div class="row align-items-center">
              <div class="col">
                <h5 class="mb-0">
                  <i class="fas fa-table me-2 text-primary"></i>
                  Liste des membres
                </h5>
              </div>
              <div class="col-auto">
                <div class="input-group">
                  <span class="input-group-text">
                    <i class="fas fa-search"></i>
                  </span>
                  <input 
                    type="text" 
                    class="form-control" 
                    placeholder="Rechercher un membre..."
                    v-model="searchTerm"
                  >
                </div>
              </div>
            </div>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-hover mb-0">
                <thead class="table-dark">
                  <tr>
                    <th scope="col" @click="sortBy('name')" class="sortable">
                      <i class="fas fa-user me-2"></i>Nom
                      <i :class="getSortIcon('name')"></i>
                    </th>
                    <th scope="col" @click="sortBy('role')" class="sortable">
                      <i class="fas fa-briefcase me-2"></i>Rôle
                      <i :class="getSortIcon('role')"></i>
                    </th>
                    <th scope="col" @click="sortBy('type')" class="sortable">
                      <i class="fas fa-tag me-2"></i>Type
                      <i :class="getSortIcon('type')"></i>
                    </th>
                    <th scope="col">
                      <i class="fas fa-image me-2"></i>Photo
                    </th>
                    <th scope="col" class="text-center">
                      <i class="fas fa-cog me-2"></i>Actions
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="member in filteredAndSortedMembers" :key="member.id" class="member-row">
                    <td>
                      <div class="d-flex align-items-center">
                        <img 
                          :src="getImageUrl(member.image)" 
                          :alt="member.name"
                          class="member-avatar me-3"
                        >
                        <div>
                          <strong>{{ member.name }}</strong>
                        </div>
                      </div>
                    </td>
                    <td>
                      <span class="role-badge">{{ member.role }}</span>
                    </td>
                    <td>
                      <span :class="getTypeBadgeClass(member.type)">
                        {{ labels[member.type] || member.type }}
                      </span>
                    </td>
                    <td>
                      <button 
                        class="btn btn-sm btn-outline-info"
                        @click="viewImage(member)"
                      >
                        <i class="fas fa-eye"></i>
                      </button>
                    </td>
                    <td class="text-center">
                      <div class="btn-group">
                        <button 
                          @click="editMember(member)" 
                          class="btn btn-sm btn-outline-primary"
                          title="Modifier"
                        >
                          <i class="fas fa-edit"></i>
                        </button>
                        <button 
                          @click="deleteMember(member.id)" 
                          class="btn btn-sm btn-outline-danger"
                          title="Supprimer"
                        >
                          <i class="fas fa-trash"></i>
                        </button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- Vue Cartes (améliorée) -->
      <div v-else class="cards-container">
        <section v-for="(list, type) in members" :key="type" class="mb-5">
          <div class="section-header">
            <h4 class="section-title">
              <i :class="getTypeIcon(type)" class="me-2"></i>
              {{ labels[type] || type }}
              <span class="badge bg-secondary ms-2">{{ list.length }}</span>
            </h4>
          </div>
          <div class="row g-4">
            <div v-for="member in list" :key="member.id" class="col-lg-3 col-md-4 col-sm-6">
              <div class="member-card">
                <div class="card-image-container">
                  <img 
                    :src="getImageUrl(member.image)" 
                    class="card-img-top" 
                    :alt="member.name"
                  >
                  <div class="card-overlay">
                    <button 
                      @click="viewImage(member)"
                      class="btn btn-light btn-sm rounded-circle"
                    >
                      <i class="fas fa-expand"></i>
                    </button>
                  </div>
                </div>
                <div class="card-body">
                  <h5 class="card-title">{{ member.name }}</h5>
                  <p class="card-text">{{ member.role }}</p>
                  <span :class="getTypeBadgeClass(type)" class="type-badge">
                    {{ labels[type] || type }}
                  </span>
                </div>
                <div class="card-actions">
                  <button @click="editMember(member)" class="btn btn-outline-primary">
                    <i class="fas fa-edit me-1"></i>Modifier
                  </button>
                  <button @click="deleteMember(member.id)" class="btn btn-outline-danger">
                    <i class="fas fa-trash me-1"></i>Supprimer
                  </button>
                </div>
              </div>
            </div>
          </div>
        </section>
      </div>
    </div>

</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import { toast } from 'vue3-toastify'
const members = ref({})
const loading = ref(false)
const viewMode = ref('table')
const searchTerm = ref('')
const sortKey = ref('name')
const sortOrder = ref('asc')

const form = ref({
  id: null,
  name: '',
  role: '',
  type: 'member',
  email: '',
  linkedin: '',
  twitter: '',
  image: null
})

const isEditMode = ref(false)
const showModal = ref(false)

const labels = {
  founder: 'Fondateurs',
  chef: "Chefs d'équipe",
  member: 'Membres'
}




const getTypeIcon = (type) => {
  const icons = {
    founder: 'fas fa-crown text-warning',
    chef: 'fas fa-user-tie text-info',
    member: 'fas fa-user text-secondary'
  }
  return icons[type] || 'fas fa-user'
}
const getTypeBadgeClass = (type) => {
  const classes = {
    founder: 'badge bg-warning text-dark',
    chef: 'badge bg-info text-white',
    member: 'badge bg-secondary text-white'
  }
  return classes[type] || 'badge bg-secondary'
}
const sortBy = (key) => {
  if (sortKey.value === key) {
    sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc'
  } else {
    sortKey.value = key
    sortOrder.value = 'asc'
  }
}
const getSortIcon = (key) => {
  if (sortKey.value !== key) return 'fas fa-sort text-muted'
  return sortOrder.value === 'asc' ? 'fas fa-sort-up text-primary' : 'fas fa-sort-down text-primary'
}
const fetchMembers = async () => {
  loading.value = true
  try {
    const res = await axios.get('/api/admin/team')
    members.value = res.data
  } catch (e) {
    toast.error("Erreur lors du chargement de l'équipe")
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchMembers()
})

const addNewMember = () => {
  resetForm()
  isEditMode.value = false
  showModal.value = true
}

const editMember = (member) => {
  form.value = { ...member }
  isEditMode.value = true
  showModal.value = true
}

const handleImageUpload = (e) => {
  const file = e.target.files[0]
  if (file && file.type.startsWith('image/')) {
    form.value.image = file
  } else {
    form.value.image = null
  }
}

const submitMember = async () => {
  try {
    loading.value = true
    const formData = new FormData()

    // Ajout des champs uniquement valides
    for (const [key, val] of Object.entries(form.value)) {
      if (key === 'image') {
        if (val instanceof File) {
          formData.append('image', val)
        }
      } else if (val !== null && val !== '') {
        formData.append(key, val)
      }
    }

    if (isEditMode.value && form.value.id) {
      await axios.post(`/api/admin/team/${form.value.id}?_method=PUT`, formData)
    } else {
      await axios.post('/api/admin/team', formData)
    }

    showModal.value = false
    await fetchMembers()
    resetForm()
  } catch (e) {
    if (e.response?.status === 422) {
      const errors = e.response.data.errors
      toast.error(Object.values(errors).flat().join('\n'))
    } else {
      toast.error("Erreur lors de l'enregistrement")
    }
  } finally {
    loading.value = false
  }
}

const deleteMember = async (id) => {
  if (!confirm('Confirmer la suppression de ce membre ?')) return
  try {
    loading.value = true
    await axios.delete(`/api/admin/team/${id}`)
    await fetchMembers()
    toast.success("Membre supprimé avec succès")

  } catch (e) {
    toast.error("Erreur lors de la suppression")
  } finally {
    loading.value = false
  }
}
const viewImage = (member) => {
  // Ouvrir modal ou nouvelle fenêtre pour voir l'image
  window.open(getImageUrl(member.image), '_blank')
}
const resetForm = () => {
  form.value = {
    id: null,
    name: '',
    role: '',
    type: 'member',
    email: '',
    linkedin: '',
    twitter: '',
    image: null
  }
}

const flatMembers = computed(() => {
  const flat = []
  Object.entries(members.value || {}).forEach(([type, list]) => {
    list.forEach(member => flat.push({ ...member, type }))
  })
  return flat
})

const filteredAndSortedMembers = computed(() => {
  let filtered = flatMembers.value
  if (searchTerm.value) {
    const term = searchTerm.value.toLowerCase()
    filtered = filtered.filter(m =>
      m.name.toLowerCase().includes(term) ||
      m.role.toLowerCase().includes(term) ||
      labels[m.type]?.toLowerCase().includes(term)
    )
  }
  filtered.sort((a, b) => {
    let aVal = a[sortKey.value], bVal = b[sortKey.value]
    if (sortKey.value === 'type') {
      aVal = labels[a.type] || a.type
      bVal = labels[b.type] || b.type
    }
    if (typeof aVal === 'string') {
       aVal.toLowerCase()
       bVal.toLowerCase()
    }
   filtered.sort((a, b) => {
  let aVal = a[sortKey.value], bVal = b[sortKey.value]
  
  if (sortKey.value === 'type') {
    aVal = labels[a.type] || a.type
    bVal = labels[b.type] || b.type
  }

  if (typeof aVal === 'string') {
    aVal = aVal.toLowerCase()
    bVal = bVal.toLowerCase()
  }

  let result = 0
  if (aVal < bVal) result = -1
  else if (aVal > bVal) result = 1

  return sortOrder.value === 'asc' ? result : -result
})


  })
  return filtered
})

const getCount = (type) => members.value?.[type]?.length || 0
const getTotalCount = () => Object.values(members.value || {}).reduce((acc, l) => acc + l.length, 0)
const getImageUrl = (path) => path ? `/storage/${path}` : '/default-user.png'
</script>


<style scoped>
/* Header avec gradient */


.page-title {
  font-size: 2rem;
  font-weight: 700;
  margin: 0;
}

.page-subtitle {
  font-size: 1.1rem;
  opacity: 0.9;
  margin: 0;
   font-weight: 700;
}

/* Cartes de statistiques */
.stat-card {
  background: white;
  border-radius: 15px;
  padding: 1.5rem;
  box-shadow: 0 4px 20px rgba(0,0,0,0.1);
  display: flex;
  align-items: center;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.stat-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 6px 30px rgba(0,0,0,0.15);
}

.stat-icon {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
  margin-right: 1rem;
}

.stat-card.founder .stat-icon {
  background: linear-gradient(45deg, #ffd700, #ffed4e);
  color: #8b7355;
}

.stat-card.chef .stat-icon {
  background: linear-gradient(45deg, #17a2b8, #20c997);
  color: white;
}

.stat-card.member .stat-icon {
  background: linear-gradient(45deg, #6c757d, #495057);
  color: white;
}

.stat-card.total .stat-icon {
  background: linear-gradient(45deg, #667eea, #764ba2);
  color: white;
}

.stat-content h3 {
  font-size: 2rem;
  font-weight: 700;
  margin: 0;
  color: #2c3e50;
}

.stat-content p {
  margin: 0;
  color: #6c757d;
  font-weight: 500;
}

/* Toggle des vues */
.view-toggle {
  display: flex;
  justify-content: center;
}

/* Table styles */
.table-container .card {
  border-radius: 15px;
  overflow: hidden;
}

.table th.sortable {
  cursor: pointer;
  user-select: none;
  transition: background-color 0.2s ease;
}

.table th.sortable:hover {
  background-color: rgba(255,255,255,0.1);
}

.member-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid #e9ecef;
}

.role-badge {
  background: linear-gradient(45deg, #f8f9fa, #e9ecef);
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-size: 0.85rem;
  font-weight: 500;
  color: #495057;
}

.member-row {
  transition: background-color 0.2s ease;
}

.member-row:hover {
  background-color: #f8f9fa;
}

/* Cartes membres améliorées */
.section-header {
  margin-bottom: 1.5rem;
}

.section-title {
  font-size: 1.5rem;
  font-weight: 600;
  color: #2c3e50;
  border-bottom: 3px solid #667eea;
  padding-bottom: 0.5rem;
  display: inline-block;
}

.member-card {
  background: white;
  border-radius: 15px;
  overflow: hidden;
  box-shadow: 0 4px 20px rgba(0,0,0,0.1);
  transition: all 0.3s ease;
  height: 100%;
}

.member-card:hover {
  transform: translateY(-10px);
  box-shadow: 0 10px 40px rgba(0,0,0,0.2);
}

.card-image-container {
  position: relative;
  overflow: hidden;
}

.card-img-top {
  height: 250px;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.member-card:hover .card-img-top {
  transform: scale(1.05);
}

.card-overlay {
  position: absolute;
  top: 10px;
  right: 10px;
  opacity: 0;
  transition: opacity 0.3s ease;
}

.member-card:hover .card-overlay {
  opacity: 1;
}

.card-body {
  padding: 1.5rem;
}

.card-title {
  font-size: 1.25rem;
  font-weight: 600;
  color: #2c3e50;
  margin-bottom: 0.5rem;
}

.card-text {
  color: #6c757d;
  margin-bottom: 1rem;
}

.type-badge {
  font-size: 0.75rem;
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
}

.card-actions {
  padding: 1rem 1.5rem;
  background: #f8f9fa;
  display: flex;
  gap: 0.5rem;
}

.card-actions .btn {
  flex: 1;
  border-radius: 8px;
  font-size: 0.875rem;
  padding: 0.5rem;
}

/* Responsive */
@media (max-width: 768px) {
  .admin-header {
    padding: 1rem 0;
  }
  
  .page-title {
    font-size: 1.8rem;
  }
  
  .stat-card {
    margin-bottom: 1rem;
  }
  
  .view-toggle {
    justify-content: stretch;
  }
  
  .view-toggle .btn-group {
    width: 100%;
  }
  
  .view-toggle .btn {
    flex: 1;
  }
}

/* Animations */
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

.member-card, .stat-card {
  animation: fadeIn 0.6s ease-out;
}

/* Scrollbar personnalisée */
.table-responsive::-webkit-scrollbar {
  height: 8px;
}

.table-responsive::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 10px;
}

.table-responsive::-webkit-scrollbar-thumb {
  background: #667eea;
  border-radius: 10px;
}

.table-responsive::-webkit-scrollbar-thumb:hover {
  background: #5a6fd8;
}
</style>