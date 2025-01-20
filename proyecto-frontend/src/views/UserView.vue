<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import IncidentCard from '../components/Incident.vue';
import UserPanel from '../components/UserPanel.vue';
import Header from '../components/Header.vue';

// Variables reactivas
const incidents = ref([]);
const userId = ref(null);
const currentPage = ref(1);
const totalPages = ref(1);

// Estado para el modal
const showModal = ref(false);

// Función para obtener las incidencias
const fetchIncidents = async (page = 1) => {
  const token = sessionStorage.getItem('token');
  try {
    const response = await axios.get(`http://127.0.0.1:8000/api/auth/incidents/getall?page=${page}`, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });

    if (response.data && response.data.data) {
      incidents.value = response.data.data;
      totalPages.value = response.data.last_page;
      currentPage.value = page;
    } else {
      console.error('No se encontraron incidencias');
      incidents.value = [];
    }
  } catch (error) {
    console.error('Error al obtener las incidencias:', error);
  }
};

// Escuchar el evento para abrir el modal
const handleNewIncident = () => {
  console.log('Modal abierto:', showModal.value);
  showModal.value = true;
  console.log('Modal después de abrir:', showModal.value);
};

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
        <div class="col-12">
          <!-- Escucha del evento personalizado -->
          <Header @new-incident="handleNewIncident" />
        </div>
        <div class="col-4">
          <UserPanel :id="userId" />
        </div>
        <div class="col-8">
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

          <!-- Controles de paginación -->
          <div class="d-flex justify-content-evenly mt-1">
            <button class="btn btn-egibide" :disabled="currentPage === 1" @click="fetchIncidents(currentPage - 1)">
              Anterior
            </button>
            <span>Pagina {{ currentPage }} de {{ totalPages }}</span>
            <button class="btn btn-egibide" :disabled="currentPage === totalPages" @click="fetchIncidents(currentPage + 1)">
              Siguiente
            </button>
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

</style>
