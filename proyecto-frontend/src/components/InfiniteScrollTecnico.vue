<script setup>
import { ref, watch, onMounted } from 'vue';
import axios from 'axios';
import IncidentCard from '../components/IncidentTecnico.vue';

// Variables reactivas
const incidents = ref([]);
const currentPage = ref(1);  // Página actual
const totalPages = ref(1);   // Total de páginas disponibles
const loading = ref(false);  // Estado de carga
const hasMore = ref(true);   // Indica si hay más incidencias para cargar

// Props para los filtros
const props = defineProps({
  filterType: String,
  filterValue: String,
});

// Función para obtener las incidencias con los filtros
const fetchIncidents = async (reset = false) => {
  if (loading.value || !hasMore.value) return;  // Prevenir cargas duplicadas

  loading.value = true;  // Estado de carga activo
  const token = sessionStorage.getItem('token');  // Obtener token de sesión

  try {
    let url = `http://127.0.0.1:8000/api/auth/incidents/getall?page=${currentPage.value}`;

    // Si hay un filtro seleccionado, construimos el endpoint según el filtro
    if (props.filterType && props.filterValue) {
      url = `http://127.0.0.1:8000/api/auth/incidents/${props.filterValue}/${props.filterType}?page=${currentPage.value}`;
    }

    const response = await axios.get(url, {
      headers: { Authorization: `Bearer ${token}` },
    });

    const data = response.data.data || [];

    if (reset) {
      incidents.value = data;  // Resetear las incidencias si se especifica
    } else {
      incidents.value = [...incidents.value, ...data];  // Añadir más incidencias
    }

    totalPages.value = response.data.last_page || 1;  // Establecer el total de páginas
    hasMore.value = currentPage.value < totalPages.value;  // Determinar si hay más páginas

    if (!reset) {
      currentPage.value++;  // Incrementar la página si no es reset
    }
  } catch (error) {
    console.error('Error al obtener las incidencias:', error);
  } finally {
    loading.value = false;  // Desactivar el estado de carga
  }
};

// Función para detectar si el scroll llega al final
const handleScroll = (event) => {
  const container = event.target;
  // Si el usuario ha llegado al final del contenedor
  if (container.scrollTop + container.clientHeight >= container.scrollHeight - 10) {
    fetchIncidents();  // Cargar más incidencias
  }
};

// Watch para los cambios en los filtros
watch(
  () => [props.filterType, props.filterValue],
  () => {
    // Resetear el scroll e incidencias cuando cambia el filtro
    currentPage.value = 1;
    hasMore.value = true;
    incidents.value = [];
    fetchIncidents(true);  // Resetear incidencias
  }
);

// Cargar incidencias al montar el componente
onMounted(() => {
  fetchIncidents();
});
</script>

<template>
  <main>
    <div class="container">
      <div class="row justify-content-center">
        <!-- Contenedor de incidencias con scroll infinito -->
        <div class="col-12 infinite-scroll-container" @scroll="handleScroll">
          <div v-for="incident in incidents" :key="incident.id">
            <IncidentCard :title="incident.title" :description="incident.description" :category="incident.importance"
              :type="String(incident.failuretypes_id)" :machines_id="String(incident.machines_id)"
              :status="incident.status" :register_date="incident.created_at" :machine_name="incident.machine_name"
              :failure_type_name="incident.failure_type_name" :incidents_id="incident.id" />
          </div>
          <!-- Spinner de carga -->
          <div v-if="loading" class="loading-spinner">
            Cargando más incidencias...
          </div>
          <!-- Mensaje si no hay más incidencias -->
          <div v-if="!hasMore && !loading" class="no-more-items">
            No hay más incidencias para mostrar.
          </div>
        </div>
      </div>
    </div>
  </main>
</template>

<style scoped>
.infinite-scroll-container {
  height: 80vh;
  overflow-y: auto;
}

.loading-spinner {
  text-align: center;
  padding: 1rem;
}

.no-more-items {
  text-align: center;
  padding: 1rem;
  font-style: italic;
  color: gray;
}
</style>
