<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import IncidentCard from '../components/AdminUserPanel.vue';

const incidents = ref([]);
const userId = ref(null);
const currentPage = ref(1);
const totalPages = ref(1);
const loading = ref(false);
const hasMore = ref(true);
const fetchIncidents = async () => {
  if (loading.value || !hasMore.value) return;
  const token = sessionStorage.getItem('token');
  loading.value = true;
  try {
    const response = await axios.get(
        `http://127.0.0.1:8000/api/auth/incidents/getall?page=${currentPage.value}`,
        {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        }
    );
    if (response.data && response.data.data) {
      const newIncidents = response.data.data;
      incidents.value = [...incidents.value, ...newIncidents];
      totalPages.value = response.data.last_page;
      if (currentPage.value < totalPages.value) {
        currentPage.value++;
      } else {
        hasMore.value = false;
      }
    } else {
      hasMore.value = false;
    }
  } catch (error) {
    console.error('Error al obtener las incidencias:', error);
  } finally {
    loading.value = false;
  }
};
const handleScroll = (event) => {
  const container = event.target;
  if (container.scrollTop + container.clientHeight >= container.scrollHeight - 10) {
    fetchIncidents();
  }
};
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
</script>

<template>
  <main>
    <div class="container">
      <div class="row">
        <div class="col-12 infinite-scroll-container" @scroll="handleScroll">
          <div v-for="incident in incidents" :key="incident.id">
            <IncidentCard
                :title="incident.title"
                :description="incident.description"
                :category="incident.importance"
                :type="String(incident.failuretypes_id)"
                :machines_id="String(incident.machines_id)"
                :status="incident.status"
                :register_date="incident.created_at"
                :machine_name="incident.machine_name"
                :failure_type_name="incident.failure_type_name"
                :incidents_id="incident.id"
            />
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
    <div v-if="showModal" class="modal-backdrop">
      <div class="modal show">
        <h2>Nueva Incidencia</h2>
        <button type="button" class="btn btn-secondary" @click="closeModal">Cancelar</button>
      </div>
    </div>
  </main>
</template>

<style scoped>
</style>