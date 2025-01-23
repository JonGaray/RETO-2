<script setup>
import { ref, onMounted, watch } from 'vue';
import axios from 'axios';
import IncidentCard from '../components/IncidentTecnico.vue';

// Variables reactivas
const incidents = ref([]);
const currentPage = ref(1);
const totalPages = ref(1);
const loading = ref(false);
const hasMore = ref(true);
const searchQuery = ref('');
const isSearching = ref(false);

// Función para obtener las incidencias
const fetchIncidents = async (reset = false) => {
  if (loading.value || (!hasMore.value && !isSearching.value)) return;

  loading.value = true;
  const token = sessionStorage.getItem('token');

  try {
    const url = isSearching.value
      ? `http://127.0.0.1:8000/api/auth/incidents/search`
      : `http://127.0.0.1:8000/api/auth/incidents/getall?page=${currentPage.value}`;

    const params = isSearching.value ? { query: searchQuery.value } : {};

    const response = await axios.get(url, {
      params,
      headers: { Authorization: `Bearer ${token}` },
    });

    const data = response.data.data || [];

    if (reset) {
      incidents.value = data;
    } else {
      incidents.value = [...incidents.value, ...data];
    }

    totalPages.value = response.data.last_page || 1;
    hasMore.value = currentPage.value < totalPages.value;

    if (!isSearching.value && !reset) {
      currentPage.value++;
    }
  } catch (error) {
    console.error('Error al obtener las incidencias:', error);
  } finally {
    loading.value = false;
  }
};

// Manejar la búsqueda
const handleSearch = async () => {
  if (searchQuery.value.trim() === '') {
    resetInfiniteScroll();
    return;
  }

  isSearching.value = true;
  currentPage.value = 1;
  hasMore.value = false; // Desactivar scroll infinito durante la búsqueda
  await fetchIncidents(true); // Resetear incidencias en búsqueda
};

// Restablecer el scroll infinito
const resetInfiniteScroll = async () => {
  isSearching.value = false;
  currentPage.value = 1;
  totalPages.value = 1;
  hasMore.value = true;
  incidents.value = [];
  await fetchIncidents(true);
};

// Manejar el evento de scroll
const handleScroll = (event) => {
  const container = event.target;
  if (
    container.scrollTop + container.clientHeight >= container.scrollHeight - 10 &&
    !loading.value &&
    !isSearching.value
  ) {
    fetchIncidents();
  }
};

// Watcher para búsqueda con debounce
let debounceTimeout;
watch(searchQuery, () => {
  clearTimeout(debounceTimeout);
  debounceTimeout = setTimeout(handleSearch, 300); // Espera de 300ms antes de buscar
});

// Modal
const showModal = ref(false);
const handleNewIncident = () => {
  showModal.value = true;
};
const closeModal = () => {
  showModal.value = false;
};

// Cargar incidencias al montar el componente
onMounted(() => {
  fetchIncidents();
});
</script>

<template>
  <main>
    <div class="container">
      <div class="row justify-content-center">
        <!-- Barra de búsqueda -->
        <div class="col-9 mb-3">
          <input
            v-model="searchQuery"
            type="text"
            @input="handleSearch"
            class="form-control"
            placeholder="Buscar por nombre de incidencia"
          />
        </div>
        <div class="col-12 infinite-scroll-container" @scroll="handleScroll">
          <div v-for="incident in incidents" :key="incident.id">
            <IncidentCard :title="incident.title" :description="incident.description" :category="incident.importance"
              :type="String(incident.failuretypes_id)" :machines_id="String(incident.machines_id)"
              :status="incident.status" :register_date="incident.created_at" :machine_name="incident.machine_name"
              :failure_type_name="incident.failure_type_name" :incidents_id="incident.id" />
          </div>
          <div v-if="loading" class="loading-spinner">
            Cargando más incidencias...
          </div>
          <div v-if="!hasMore && !loading" class="no-more-items">
            No hay más incidencias para mostrar.
          </div>
        </div>
      </div>
    </div>
  </main>
</template>

<style scoped>
</style>