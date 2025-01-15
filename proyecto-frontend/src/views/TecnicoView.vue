<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import IncidentCard from '../components/IncidentTecnico.vue'; // Importa el componente IncidentCard
import TecnicoPanel from '../components/TecnicoPanel.vue'; // Importa el componente TecnicoPanel
import Header from '../components/Header.vue'; // Importa el componente Header

// Variables reactivas
const incidents = ref([]); // Inicializa la lista de incidencias
const userId = ref(null); // Inicializa userId con null
const currentPage = ref(1); // Página actual
const totalPages = ref(1); // Total de páginas

// Función para obtener las incidencias con paginación
const fetchIncidents = async (page = 1) => {
    const token = sessionStorage.getItem('token'); // Obtén el token desde sessionStorage
    try {
        // Llamada a la API para obtener todas las incidencias con paginación
        const response = await axios.get(`http://127.0.0.1:8000/api/auth/incidents/getall?page=${page}`, {
            headers: {
                Authorization: `Bearer ${token}`,
            },
        });

        // Verificar si la respuesta tiene la propiedad 'data' y asignarla correctamente
        if (response.data && Array.isArray(response.data.data)) {
            incidents.value = response.data.data; // Asigna el array de incidencias a incidents
            totalPages.value = response.data.last_page; // Total de páginas
            currentPage.value = page; // Actualiza la página actual
        } else {
            console.error('No se encontraron incidencias');
            incidents.value = []; // Asigna un array vacío si no hay incidencias
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
                    <!-- Se renderiza el componente TecnicoPanel solo si userId tiene valor -->
                    <TecnicoPanel :id="userId" />
                </div>
                <div class="col-8">
                    <!-- Generamos tantas tarjetas como incidencias existan -->
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
