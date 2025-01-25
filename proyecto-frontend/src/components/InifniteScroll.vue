<script setup>
import { ref, onMounted, watch } from 'vue';
import axios from 'axios';
import IncidentCard from '../components/Incident.vue';
import UserPanel from '../components/UserPanel.vue';
import Header from '../components/Header.vue';

const incidents = ref([]);
const userId = ref(null);
const currentPage = ref(1);
const totalPages = ref(1);
const loading = ref(false);
const hasMore = ref(true);
const isSearching = ref(false);
const scrollContainer = ref(null);
const searchQuery = ref("");
const fetchIncidents = async (reset = false) => {
  if (loading.value || (!hasMore.value && !isSearching.value)) return;
  loading.value = true;
  try {
    const token = sessionStorage.getItem('token');
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
    console.error('Error al obtener incidencias:', error);
  } finally {
    loading.value = false;
  }
};
const searchIncidents = async () => {
  if (searchQuery.value.trim() === '') {
    resetInfiniteScroll();
    return;
  }
  isSearching.value = true;
  incidents.value = [];
  currentPage.value = 1;
  await fetchIncidents();
};
const handleSearch = async () => {
  if (searchQuery.value.trim() === '') {
    resetInfiniteScroll();
    return;
  }
  isSearching.value = true;
  currentPage.value = 1;
  hasMore.value = false;
  await fetchIncidents(true);
};
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
let debounceTimeout;
watch(searchQuery, () => {
  clearTimeout(debounceTimeout);
  debounceTimeout = setTimeout(handleSearch, 300);
});
const handleNewIncident = () => {
  showModal.value = true;
};
const showModal = ref(false);
const closeModal = () => {
  showModal.value = false;
};
onMounted(() => {
  fetchIncidents();
});
const resetInfiniteScroll = async () => {
  isSearching.value = false;
  currentPage.value = 1;
  totalPages.value = 1;
  hasMore.value = true;
  incidents.value = [];
  await fetchIncidents(true);
};
</script>

<template>
  <main>
    <div class="container justify-content-center">
      <div class="row justify-content-center mb-4">
        <div class="col-9">
          <input v-model="searchQuery" type="text" @input="searchIncidents" class="form-control"
            placeholder="Buscar por nombre de incidencia" />
        </div>
      </div>
      <div class="col-12 infinite-scroll-container" @scroll="(e) => {
        if (!isSearching && e.target.scrollTop + e.target.clientHeight >= e.target.scrollHeight - 10) {
          fetchIncidents();
        }
      }">
        <div v-for="incident in incidents" :key="incident.id">
          <IncidentCard :title="incident.title" :description="incident.description" :category="incident.importance"
            :type="String(incident.failuretypes_id)" :machines_id="String(incident.machines_id)"
            :status="incident.status" :date="incident.created_at" :machine_name="incident.machine_name"
            :failure_type_name="incident.failure_type_name" />
        </div>
        <div v-if="loading" class="loading-spinner">
          Cargando más incidencias...
        </div>
        <div v-if="!hasMore && !loading" class="no-more-items">
          No hay más incidencias para mostrar.
        </div>
      </div>
    </div>
    <div v-if="showModal" class="modal-backdrop">
      <div class="modal show">
        <h2>Nueva Incidencia</h2>
        <button type="button" class="btn btn-secondary" @click="closeModal">Cancelar</button>
      </div>
    </div>
  </main>
</template>

<style scoped></style>