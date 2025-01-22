<template>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <div class="d-flex justify-content-between align-items-center">
    <h1 class="text-egibide">Sistema de Incidencias</h1>
    <div class="d-flex">
      <button class="btn btn-egibide me-5" @click="showModal = true">+ Nueva Incidencia</button>
      <button class="btn btn-outline-egibide" @click="logout">Cerrar Sesión</button>
    </div>
  </div>
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
        <div class="mb-3 dropdown-wrapper">
          <label for="campus" class="form-label">Campus</label>
          <div class="dropdown-icon-container">
            <select id="campus" v-model="selectedCampus" @change="fetchSections" class="form-control" required>
              <option v-for="campus in campuses" :key="campus.id" :value="campus.id">{{ campus.name }}</option>
            </select>
            <i class="fas fa-chevron-down dropdown-icon"></i>
          </div>
        </div>
        <div class="mb-3 dropdown-wrapper">
          <label for="section" class="form-label">Sección</label>
          <div class="dropdown-icon-container">
            <select id="section" v-model="selectedSection" @change="fetchMachines" class="form-control" required>
              <option v-for="section in sections" :key="section.id" :value="section.id">{{ section.name }}</option>
            </select>
            <i class="fas fa-chevron-down dropdown-icon"></i>
          </div>
        </div>
        <div class="mb-3 dropdown-wrapper">
          <label for="importance" class="form-label">Importancia</label>
          <div class="dropdown-icon-container">
            <select id="importance" v-model="newIncident.importance" class="form-control" required>
              <option value="parada">Parada</option>
              <option value="averia">Avería</option>
              <option value="aviso">Aviso</option>
            </select>
            <i class="fas fa-chevron-down dropdown-icon"></i>
          </div>
        </div>
        <div class="mb-3 dropdown-wrapper">
          <label for="machine" class="form-label">Máquina</label>
          <div class="dropdown-icon-container">
            <select id="machine" v-model="newIncident.machines_id" class="form-control" required>
              <option v-for="machine in machines" :key="machine.id" :value="machine.id">{{ machine.name }}</option>
            </select>
            <i class="fas fa-chevron-down dropdown-icon"></i>
          </div>
        </div>
        <div class="mb-3 dropdown-wrapper">
          <label for="failuretype" class="form-label">Tipo de Avería</label>
          <div class="dropdown-icon-container">
            <select id="failuretype" v-model="newIncident.failuretypes_id" class="form-control" required>
              <option v-for="failuretype in failuretypes" :key="failuretype.id" :value="failuretype.id">{{
                failuretype.name }}</option>
            </select>
            <i class="fas fa-chevron-down dropdown-icon"></i>
          </div>
        </div>
        <div class="mb-3 d-flex justify-content-between">
          <button type="submit" class="btn btn-egibide">Crear Incidencia</button>
          <button type="button" class="btn btn-secondary" @click="showModal = false">Cancelar</button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';
import Swal from 'sweetalert2';

defineEmits(['newIncident']);
const showModal = ref(false);
const campuses = ref([]);
const sections = ref([]);
const machines = ref([]);
const failuretypes = ref([]);
const selectedCampus = ref(null);
const selectedSection = ref(null);
const newIncident = ref({
  title: '',
  description: '',
  importance: 'parada',
  machines_id: null,
  failuretypes_id: null,
});
const router = useRouter();
const fetchCampuses = async () => {
  const token = sessionStorage.getItem('token');
  try {
    const response = await axios.get('http://127.0.0.1:8000/api/auth/campuses', {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });
    campuses.value = response.data;
  } catch (error) {
    console.error('Error al obtener los campuses:', error);
  }
};
const fetchSections = async () => {
  const token = sessionStorage.getItem('token');
  try {
    const response = await axios.get(`http://127.0.0.1:8000/api/auth/sections/getbycampus/${selectedCampus.value}`, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });
    sections.value = response.data;
  } catch (error) {
    console.error('Error al obtener las secciones:', error);
  }
};
const fetchMachines = async () => {
  if (!selectedSection.value) return;
  machines.value = [];
  const token = sessionStorage.getItem('token');
  try {
    const response = await axios.get('http://127.0.0.1:8000/api/auth/machines/getsections', {
      params: { sections_id: selectedSection.value },
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });
    machines.value = response.data.data;
  } catch (error) {
    console.error('Error al obtener las máquinas:', error);
  }
};
const fetchFailureTypes = async () => {
  const token = sessionStorage.getItem('token');
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
onMounted(() => {
  fetchCampuses();
  fetchFailureTypes();
});
watch(selectedSection, (newSection) => {
  fetchMachines();
});
const submitIncident = async () => {
  if (newIncident.value.title.trim().length > 255) {
    Swal.fire({
      icon: 'error',
      title: 'Exceso de caracteres',
      text: 'El título no puede superar los 255 caracteres',
    });
    return;
  }
  if (newIncident.value.description.trim().length > 255) {
    Swal.fire({
      icon: 'error',
      title: 'Exceso de caracteres',
      text: 'La descripción no puede superar los 255 caracteres',
    });
    return;
  }
  const token = sessionStorage.getItem('token');
  try {
    const response = await axios.post('http://127.0.0.1:8000/api/auth/incidents/store', newIncident.value, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });
    if (!response.data.data || !response.data.data.id) {
      console.error('La respuesta no contiene el ID de la incidencia');
      return;
    }
    const user = JSON.parse(sessionStorage.getItem('user'));
    const userId = user.id;
    // Asignar el incidente al usuario
    await axios.post('http://127.0.0.1:8000/api/auth/userincidents/create', {
      users_id: userId,
      incidents_id: response.data.data.id,
    }, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });
    showModal.value = false;
    fetchFailureTypes();
    fetchMachines();
    fetchSections();
    window.location.reload();
  } catch (error) {
    console.error('Error al crear la incidencia o asociar usuario:', error);
  }
};
const logout = () => {
  sessionStorage.removeItem('token');
  sessionStorage.removeItem('user');
  router.push('/');
};
</script>

<style scoped>
</style>
