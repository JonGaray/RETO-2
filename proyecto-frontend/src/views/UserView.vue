<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import IncidentCard from '../components/Incident.vue'; // Importa el componente IncidentCard
import UserPanel from '../components/UserPanel.vue'; // Importa el componente UserPanel
import Header from '../components/Header.vue'; // Importa el componente Header

// Variables reactivas
const incidents = ref([]); // Lista de incidencias
const userId = ref(null); // ID del usuario
const currentPage = ref(1); // Página actual
const totalPages = ref(1); // Total de páginas

// Función para obtener las incidencias
const fetchIncidents = async (page = 1) => {
  const token = sessionStorage.getItem('token'); // Obtener el token desde sessionStorage
  try {
    const response = await axios.get(`http://127.0.0.1:8000/api/auth/incidents/getall?page=${page}`, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });

    // Verificar si la respuesta contiene los datos
    if (response.data && response.data.data) {
      incidents.value = response.data.data; // Asignar los datos de incidencias
      totalPages.value = response.data.last_page; // Total de páginas
      currentPage.value = page; // Actualizar la página actual
    } else {
      console.error('No se encontraron incidencias');
      incidents.value = []; // Array vacío si no se encuentran incidencias
    }
  } catch (error) {
    console.error('Error al obtener las incidencias:', error);
  }
};

// Llamar a fetchIncidents al cargar el componente
onMounted(() => {
  fetchIncidents(); // Cargar la primera página de incidencias
});
</script>

<template>
  <main>
    <div class="container">
      <div class="row">
        <div class="col-12">
          <Header />
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
            <button 
              class="btn btn-egibide" 
              :disabled="currentPage === 1" 
              @click="fetchIncidents(currentPage - 1)">
              Anterior
            </button>
            <span>Pagina {{ currentPage }} de {{ totalPages }}</span>
            <button 
              class="btn btn-egibide" 
              :disabled="currentPage === totalPages" 
              @click="fetchIncidents(currentPage + 1)">
              Siguiente
            </button>
          </div>
        </div>
      </div>
    </div>
  </main>
</template>

<style scoped>
.container {
  margin-top: 20px;
}
</style>
