<template>
  <div class="col-12">
    <div class="card mt-5">
      <div class="card-body mt-2 mb-2">
        <div class="d-flex align-items-center justify-content-evenly mb-2">
          <img src="../img/foto-default.png" alt="Foto de perfil" class="rounded-circle img-fluid foto-perfil-usu">
          <h5 class="fw-bold mb-0 text-nowrap">{{ userName }}</h5>
        </div>
        <div class="d-flex justify-content-evenly">
          <span class="fw-bold">Incidencias realizadas:</span>
          <span>{{ incidentsCount }}</span>
        </div>
      </div>
    </div>
    <div class="w-100"></div>
    <div class="col-12 mt-5">
      <img src="../img/logo-egi.jpg" alt="Egibide" class="img-fluid mt-5">
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    const user = JSON.parse(sessionStorage.getItem('user'));
    return {
      userName: user ? user.name : '',
      incidentsCount: 0,
      userId: user ? user.id : null
    };
  },
  created() {
    const token = sessionStorage.getItem('token');
    if (token && this.userId) {
      axios.get(`http://127.0.0.1:8000/api/auth/userincidents/${this.userId}/count`, {
        headers: {
          Authorization: `Bearer ${token}`,
        }
      })
      .then(response => {
        this.incidentsCount = response.data.count;
        console.log(response.data.count);
      })
      .catch(error => {
        console.error("Error al obtener el número de incidencias:", error);
      });
    } else {
      console.error("No se encontró el token de autenticación o el ID del usuario.");
    }
  }
};
</script>

<style scoped>
</style>
