<template>
  <main>
    <div class="row mt-3">
      <div class="d-flex justify-content-between align-items-center">
        <h1 class="text-egibide">Sistema de Incidencias</h1>
        <button class="btn btn-egibide" @click="showModal = true">Nueva Incidencia</button>
      </div>
    </div>

    <div class="row">
      <div class="col-12">
        <p>Gestión de incidencias - Egibide</p>
      </div>
    </div>

    <!-- Modal de creación de nueva incidencia -->
    <div v-if="showModal" class="modal-backdrop">
      <div class="modal">
        <h2>Nueva Incidencia</h2>

        <form @submit.prevent="submitIncident">
          <div class="mb-3">
            <label for="title" class="form-label">Título</label>
            <input type="text" id="title" v-model="newIncident.title" class="form-control" required>
          </div>

          <div class="mb-3">
            <label for="description" class="form-label">Descripción</label>
            <textarea id="description" v-model="newIncident.description" class="form-control" required></textarea>
          </div>

          <div class="mb-3">
            <label for="importance" class="form-label">Importancia</label>
            <select id="importance" v-model="newIncident.importance" class="form-control" required>
              <option value="parada">Parada</option>
              <option value="averia">Avería</option>
              <option value="aviso">Aviso</option>
              <option value="mantenimiento">Mantenimiento</option>
            </select>
          </div>

          <div class="mb-3">
            <label for="section" class="form-label">Sección</label>
            <select id="section" v-model="selectedSection" @change="fetchMachines" class="form-control" required>
              <option v-for="section in sections" :key="section.id" :value="section.id">{{ section.name }}</option>
            </select>
          </div>

          <div class="mb-3">
            <label for="machine" class="form-label">Máquina</label>
            <select id="machine" v-model="newIncident.machines_id" class="form-control" required>
              <option v-for="machine in machines" :key="machine.id" :value="machine.id">{{ machine.name }}</option>
            </select>
          </div>

          <div class="mb-3">
            <label for="failuretype" class="form-label">Tipo de Avería</label>
            <select id="failuretype" v-model="newIncident.failuretypes_id" class="form-control" required>
              <option v-for="failuretype in failuretypes" :key="failuretype.id" :value="failuretype.id">{{ failuretype.name }}</option>
            </select>
          </div>

          <div class="mb-3 d-flex justify-content-between">
            <button type="submit" class="btn btn-egibide">Crear Incidencia</button>
            <button type="button" class="btn btn-secondary" @click="showModal = false">Cancelar</button>
          </div>
        </form>
      </div>
    </div>
  </main>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const showModal = ref(false);
const sections = ref([]);
const machines = ref([]);
const failuretypes = ref([]);
const selectedSection = ref(null);
const newIncident = ref({
  title: '',
  description: '',
  importance: 'parada',
  machines_id: null,
  failuretypes_id: null // Cambio aquí para que coincida con el nuevo campo
});

const fetchSections = async () => {
  const token = sessionStorage.getItem('token'); // Obtener el token de sessionStorage
  try {
    const response = await axios.get('http://127.0.0.1:8000/api/auth/sections', {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });
    sections.value = response.data.data;
  } catch (error) {
    console.error('Error al obtener las secciones:', error);
  }
};

const fetchMachines = async () => {
  const token = sessionStorage.getItem('token'); // Obtener el token de sessionStorage
  try {
    console.log("Requesting machines for section_id:", selectedSection.value); // Agregar un log para ver el ID de la sección
    const response = await axios.get('http://127.0.0.1:8000/api/auth/machines/getsections', {
      params: { sections_id: selectedSection.value },
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });

    machines.value = response.data.data;
    console.log('Máquinas:', machines.value); // Verifica que las máquinas se hayan recibido correctamente
  } catch (error) {
    console.error('Error al obtener las máquinas:', error.response ? error.response.data : error.message);
  }
};

const fetchFailureTypes = async () => {
  const token = sessionStorage.getItem('token'); // Obtener el token de sessionStorage
  try {
    const response = await axios.get('http://127.0.0.1:8000/api/auth/failuretypes', {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });
    failuretypes.value = response.data.data;
  } catch (error) {
    console.error('Error al obtener los tipos de fallas:', error);
  }
};

// Cargar secciones y tipos de fallas al montar el componente
onMounted(() => {
  fetchSections();
  fetchFailureTypes();
});

const submitIncident = async () => {
  const token = sessionStorage.getItem('token'); // Obtener el token de sessionStorage
  try {
    const response = await axios.post('http://127.0.0.1:8000/api/auth/incidents/store', newIncident.value, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });
    console.log('Incidencia creada exitosamente:', response.data);
    showModal.value = false; // Cerrar el modal después de la creación
  } catch (error) {
    console.error('Error al crear la incidencia:', error);
  }
};
</script>

<style scoped>
.modal-backdrop {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1050;
}

.modal {
  background-color: white;
  padding: 20px;
  border-radius: 10px;
  width: 500px;
  display: block; /* Cambiar de display: none a display: block */
}

.modal.show {
  display: block !important;
  opacity: 1;
  z-index: 1060;
}
</style>
