<template>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <main>
    <div class="row mt-3">
      <div class="d-flex justify-content-between align-items-center">
        <h1 class="text-egibide">Sistema de Incidencias</h1>
        <div class="d-flex">
          <button class="btn btn-egibide me-5" @click="showModal = true">+ Nueva Incidencia</button>
          <button class="btn btn-outline-egibide" @click="logout">Cerrar Sesión</button>
        </div>
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

          <!-- Añadido icono al desplegable -->
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
            <label for="section" class="form-label">Sección</label>
            <div class="dropdown-icon-container">
              <select id="section" v-model="selectedSection" @change="fetchMachines" class="form-control" required>
                <option v-for="section in sections" :key="section.id" :value="section.id">{{ section.name }}</option>
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
                <option v-for="failuretype in failuretypes" :key="failuretype.id" :value="failuretype.id">{{ failuretype.name }}</option>
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
  </main>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router'; // Importar el router para la redirección

defineEmits(['newIncident']);
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
  failuretypes_id: null, // Cambio aquí para que coincida con el nuevo campo
});

const router = useRouter(); // Instancia del router para redirigir

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
    // Crear la incidencia
    const response = await axios.post('http://127.0.0.1:8000/api/auth/incidents/store', newIncident.value, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });

    // Verifica que la respuesta contenga el id de la incidencia
    if (!response.data.data || !response.data.data.id) {
      console.error('La respuesta no contiene el ID de la incidencia');
      return;
    }

    // Obtener el ID del usuario (suponemos que está en el token o en algún lugar)
    const user = JSON.parse(sessionStorage.getItem('user'));
    const userId = user.id;
    if (!userId) {
      console.error('No se pudo obtener el ID del usuario');
      return;
    }

    // Crear la relación en la tabla intermedia usersincidents
    await axios.post('http://127.0.0.1:8000/api/auth/userincidents/create', {
      users_id: userId,
      incidents_id: response.data.data.id, // Usamos el ID de la incidencia
    }, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });

    showModal.value = false; // Cerrar el modal después de la creación

    // Llamar a las funciones sin usar `this`
    fetchFailureTypes();
    fetchMachines();
    fetchSections();

  } catch (error) {
    console.error('Error al crear la incidencia o asociar usuario:', error);
  }
};

// Añadir el método para cerrar sesión
const logout = () => {
  // Eliminar el token del sessionStorage
  sessionStorage.removeItem('token');
  sessionStorage.removeItem('user'); // Si tienes el usuario en sessionStorage, también puedes eliminarlo

  // Redirigir al usuario a la página de inicio
  router.push('/');
};
</script>

<style scoped>
/* Estilo para mostrar el icono en los selectores */

</style>