<script setup>
import { ref, watch, onMounted } from 'vue';
import axios from 'axios';
import IncidentCard from '../components/IncidentTecnico.vue';

const incidents = ref([]);
const currentPage = ref(1);
const totalPages = ref(1);
const loading = ref(false);
const hasMore = ref(true);
const props = defineProps({
  filterType: String,
  filterValue: [String, Number],
});
const fetchIncidents = async (reset = false) => {
  if (loading.value || !hasMore.value) return;
  loading.value = true;
  const token = sessionStorage.getItem('token');
  try {
    let url = `http://127.0.0.1:8000/api/auth/incidents/getall?page=${currentPage.value}`;
    if (props.filterType && props.filterValue) {
      url = `http://127.0.0.1:8000/api/auth/incidents/${props.filterValue}/${props.filterType}?page=${currentPage.value}`;
    }
    const response = await axios.get(url, {
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
    if (!reset) {
      currentPage.value++;
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
watch(
  () => [props.filterType, props.filterValue],
  () => {
    currentPage.value = 1;
    hasMore.value = true;
    incidents.value = [];
    fetchIncidents(true);
  }
);
onMounted(() => {
  fetchIncidents();
});
</script>

<template>
  <main>
    <div class="container">
      <div class="row justify-content-center">
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

<style scoped></style>