<template>
  <div class="card shadow-sm p-3 mb-4 bg-white rounded mt-5 carta-usu-principal">
    <div class="card-body">
      <div class="d-flex align-items-center justify-content-between mb-2">
        <img src="../img/foto-default.png" alt="Foto de perfil" class="rounded-circle img-fluid foto-perfil-usu">
        <h5 class="fw-bold mb-0 text-nowrap">Usuario</h5>
      </div>
      <div class="d-flex justify-content-between">
        <span class="fw-bold">Incidencias realizadas:</span>
        <span>{{ incidentsCount }}</span>
      </div>
      <button class="btn btn-egibide mt-3 w-100">Ver perfil</button>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  props: ['id'], // Recibimos el ID del usuario como prop
  data() {
    return {
      incidentsCount: 0,  // Inicializamos el contador de incidencias
    };
  },
  created() {
    const token = sessionStorage.getItem('token'); // Recupera el token desde sessionStorage
    if (token) {
      console.log('ID recibido en UserPanel:', this.id);
      // Agrega el token a los encabezados de la solicitud
      axios.get(`http://127.0.0.1:8000/api/auth/userincidents/${this.id}/count`, {
        headers: {
          Authorization: `Bearer ${token}`,  // Añadir el token al header
        }
      })
      .then(response => {
        console.log("Incidencias response:", response.data);
        this.incidentsCount = response.data.count;
      })
      .catch(error => {
        console.error("Error al obtener el número de incidencias:", error);
      });
    } else {
      console.error("No se encontró el token de autenticación. Por favor, inicia sesión.");
    }
  }
};
</script>

<style scoped>
/* Aquí puedes añadir tus estilos */
</style>
