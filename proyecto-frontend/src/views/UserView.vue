<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import UserPanel from '../components/UserPanel.vue';
import Header from '../components/Header.vue';
import InifniteScroll from "@/components/InifniteScroll.vue";
import Swal from 'sweetalert2';

const incidents = ref([]);
const userId = ref(null);
const currentPage = ref(1);
const totalPages = ref(1);
const showModal = ref(false);
const searchQuery = ref('');
const fetchIncidents = async (page = 1) => {
  const token = sessionStorage.getItem('token');
  try {
    const response = await axios.get(`http://172.20.228.150:8080/api/auth/incidents/getall?page=${page}`, {
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
const handleNewIncident = () => {
  console.log('Modal abierto:', showModal.value);
  showModal.value = true;
  console.log('Modal después de abrir:', showModal.value);
};
const closeModal = () => {
  showModal.value = false;
};
const checkTokenExpiration = () => {
  const tokenExpiration = sessionStorage.getItem('token_expiration');
  if (tokenExpiration) {
    const expirationTime = parseInt(tokenExpiration);
    const currentTime = Date.now();
    const timeRemaining = expirationTime - currentTime;
    if (timeRemaining <= 0) {
      sessionStorage.removeItem('token');
      sessionStorage.removeItem('token_expiration');
      sessionStorage.removeItem('user');
      Swal.fire({
        icon: 'error',
        title: 'Sesión caducada',
        text: 'Inicie sesión de nuevo para continuar',
      }); this.$router.push({ name: 'login' });
    }
  } else {
    this.$router.push({ name: 'login' });
  }
};
onMounted(() => {
  checkTokenExpiration();
  fetchIncidents();
});
</script>

<template>
  <main class="py-4">
    <div class="container">
      <div class="row">
        <div class="col-12">
          <Header @new-incident="handleNewIncident" />
        </div>
        <div class="col-lg-4 col-md-6 col-sm-12">
          <UserPanel :id="userId" />
        </div>
        <div class="col-lg-8 col-md-6 col-sm-12 justify-content-center">
          <InifniteScroll />
        </div>
      </div>
    </div>
    <div v-if="showModal" class="modal-backdrop">
      <div class="modal show" tabindex="-1" style="display: block;" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Nueva Incidencia</h5>
              <button type="button" class="close" @click="closeModal">
                <span>&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <p>Formulario de nueva incidencia</p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" @click="closeModal">Cancelar</button>
              <button type="button" class="btn btn-primary">Guardar</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
</template>

<style scoped></style>
