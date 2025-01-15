<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import IncidentCard from '../components/IncidentTecnico.vue'; // Importa el componente IncidentCard
import TecnicoPanel from '../components/TecnicoPanel.vue'; // Importa el componente UserPanel
import Header from '../components/Header.vue'; // Importa el componente Header

// Variables reactivas
const incidents = ref([]); // Inicializa la lista de incidencias
const userId = ref(null); // Inicializa userId con null

onMounted(async () => {
    // Obtener el objeto de usuario desde sessionStorage
    const user = JSON.parse(sessionStorage.getItem('user')); // Parsear el JSON del usuario
    if (user && user.id) {
        userId.value = user.id; // Asigna el ID del usuario
    }
    const token = sessionStorage.getItem('token'); // Obtén el token desde sessionStorage
    try {
        // Llamada a la API para obtener todas las incidencias
        const response = await axios.get('http://127.0.0.1:8000/api/auth/incidents/getall', {
            headers: {
                Authorization: `Bearer ${token}`,
            },
        });

        // Verificar si la respuesta tiene la propiedad 'data' y asignarla correctamente
        if (response.data && Array.isArray(response.data.data)) {
            incidents.value = response.data.data; // Asigna el array de incidencias a incidents
        } else {
            console.error('No se encontraron incidencias');
            incidents.value = []; // Asigna un array vacío si no hay incidencias
        }
    } catch (error) {
        console.error('Error al obtener las incidencias:', error);
    }
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
                    <TecnicoPanel v-if="userId" :id="userId" />
                </div>
                <div class="col-8">
                    <!-- Generamos tantas tarjetas como incidencias existan -->
                    <div v-for="incident in incidents" :key="incident.id">
                        <div v-for="incident in incidents" :key="incident.id">
                            <IncidentCard :title="incident.title" :description="incident.description"
                                :category="incident.importance" :type="String(incident.failuretypes_id)"
                                :machines_id="String(incident.machines_id)" :status="incident.status"
                                :register_date="incident.created_at" :machine_name="incident.machine_name"
                                :failure_type_name="incident.failure_type_name" :incidents_id="incident.id" />
                        </div>
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
