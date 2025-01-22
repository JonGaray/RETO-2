<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import UserPanel from '../components/UserPanel.vue';
import Header from '../components/Header.vue';
import InifniteScroll from "@/components/InifniteScroll.vue";

const incidents = ref([]);
const userId = ref(null);
const currentPage = ref(1);
const totalPages = ref(1);
const showModal = ref(false);
const searchQuery = ref('');
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
const handleNewIncident = () => {
  console.log('Modal abierto:', showModal.value);
  showModal.value = true;
  console.log('Modal después de abrir:', showModal.value);
};
const closeModal = () => {
  showModal.value = false;
};
onMounted(() => {
  fetchIncidents();
});

</script>

<template>
  <main>
    <div class="container">
      <div class="row">
        <div class="col-12">
          <Header @new-incident="handleNewIncident" />
        </div>
        <div class="col-4">
          <UserPanel :id="userId" />
        </div>
        <div class="col-8 justify-content-center">
            <InifniteScroll/>
        </div>
      </div>
    </div>
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
