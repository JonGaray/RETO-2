<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import IncidentCard from '../components/Incident.vue';
import UserPanel from '../components/UserPanel.vue';
import Header from '../components/Header.vue';

// Variables reactivas
const incidents = ref([]); // Lista de incidencias
const userId = ref(null); // ID del usuario (si aplica)
const currentPage = ref(1); // Página actual
const totalPages = ref(1); // Total de páginas
const loading = ref(false); // Estado de carga
const hasMore = ref(true); // Indica si hay más incidencias por cargar

// Función para obtener las incidencias
const fetchIncidents = async () => {
  if (loading.value || !hasMore.value) return; // Evitar múltiples llamadas si ya está cargando o no hay más

  const token = sessionStorage.getItem('token');
  loading.value = true; // Iniciar estado de carga

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

      // Concatenar las nuevas incidencias a las existentes
      incidents.value = [...incidents.value, ...newIncidents];

      // Actualizar total de páginas y página actual
      totalPages.value = response.data.last_page;

      // Incrementar página solo si hay más datos
      if (currentPage.value < totalPages.value) {
        currentPage.value++;
      } else {
        hasMore.value = false; // No hay más páginas para cargar
      }
    } else {
      hasMore.value = false; // Detener scroll infinito si no hay datos
    }
  } catch (error) {
    console.error('Error al obtener las incidencias:', error);
  } finally {
    loading.value = false; // Finalizar estado de carga
  }
};

// Manejar el evento de scroll infinito
const handleScroll = (event) => {
  const container = event.target;
  if (container.scrollTop + container.clientHeight >= container.scrollHeight - 10) {
    fetchIncidents();
  }
};

// Escuchar el evento para abrir el modal
const handleNewIncident = () => {
  showModal.value = true;
};

// Estado del modal
const showModal = ref(false);

// Cerrar el modal
const closeModal = () => {
  showModal.value = false;
};

// Llamar a fetchIncidents al montar el componente
onMounted(() => {
  fetchIncidents();
});
</script>

<template>
  <main>
    <div class="container">
      <div class="row">

        <div class="col-12 infinite-scroll-container" @scroll="handleScroll">
          <!-- Mostrar las incidencias -->
          <div v-for="incident in incidents" :key="incident.id">
            <IncidentCard
                :title="incident.title"
                :description="incident.description"
                :category="incident.importance"
                :type="String(incident.failuretypes_id)"
                :machines_id="String(incident.machines_id)"
                :status="incident.status"
                :date="incident.created_at"
                :machine_name="incident.machine_name"
                :failure_type_name="incident.failure_type_name"
            />
          </div>

          <!-- Mostrar spinner mientras se cargan más incidencias -->
          <div v-if="loading" class="loading-spinner">
            Cargando más incidencias...
          </div>

          <!-- Mostrar mensaje si no hay más incidencias -->
          <div v-if="!hasMore && !loading" class="no-more-items">
            No hay más incidencias para mostrar.
          </div>
        </div>
      </div>
    </div>

    <!-- Modal de creación de nueva incidencia -->
    <div v-if="showModal" class="modal-backdrop">
      <div class="modal show">
        <h2>Nueva Incidencia</h2>
        <button type="button" class="btn btn-secondary" @click="closeModal">Cancelar</button>
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



.infinite-scroll-container {
  height: 80vh;
  overflow-y: auto;

  /* Firefox scrollbar styling */
  scrollbar-width: thin; /* Barra delgada */
  scrollbar-color: #800080 #e0e0e0; /* Morado para el deslizador y gris claro para el fondo */
}

.infinite-scroll-container::-webkit-scrollbar {
  width: 8px; /* Ancho de la barra */
  height: 8px; /* Altura de la barra horizontal, si aplica */
}

.infinite-scroll-container::-webkit-scrollbar-thumb {
  background-color: #800080; /* Color del deslizador (morado) */
  border-radius: 10px; /* Bordes redondeados */
}

.infinite-scroll-container::-webkit-scrollbar-thumb:hover {
  background-color: #9a009a; /* Color más claro al pasar el cursor */
}

.infinite-scroll-container::-webkit-scrollbar-track {
  background: #e0e0e0; /* Fondo de la barra (gris claro) */
  border-radius: 10px; /* Bordes redondeados */
}



</style>

