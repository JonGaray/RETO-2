<template>
    <div class="col-12">
      <div class="card mt-5">
        <div class="card-body mt-2 mb-2">
          <div class="d-flex align-items-center justify-content-evenly mb-2">
            <img src="../img/foto-default.png" alt="Foto de perfil" class="rounded-circle img-fluid foto-perfil-usu">
            <h5 class="fw-bold mb-0 text-nowrap">{{ userName }}</h5>
          </div>
          <div class="d-flex justify-content-between">
          </div>
          <div class="d-flex justify-content-center">
            <button class="btn btn-egibide mt-3 boton-perfil-usu">Ver perfil</button>
          </div>
        </div>
      </div>
      <div class="w-100"></div>
      <div class="card mt-5 p-3">
          <h5 class="text-start">Panel de Administración</h5>
          <p class="text-start text-muted">Gestión de usuarios del sistema</p>
          <p class="text-start">Técnicos Disponibles: <span class="fw-bold">{{ techniciansCount }}</span></p>
          <ul class="list-group">
            <li v-for="user in users"
                :key="user.name"
                class="list-group-item d-flex justify-content-between align-items-center">
                {{ user.name }} <span>{{ user.role }}</span>
            </li>
          </ul>
          <button class="btn btn-egibide w-100 mt-3">Panel de Administrador</button>
        </div>
    </div>
  </template>
  
  <script>
    import axios from "axios";

        export default {
        data() {
            return {
            techniciansCount: 0, // Aquí guardaremos el número de técnicos disponibles
            };
        },
        mounted() {
            this.fetchTechniciansCount();
        },
        methods: {
            async fetchTechniciansCount() {
            try {
                const response = await axios.get('http://127.0.0.1:8000/api/technicians');
                this.techniciansCount = response.data.techniciansCount;
            } catch (error) {
                console.error("Error al obtener el número de técnicos:", error);
            }
            },
        },
    };
  </script>
  
  <style scoped>

  </style>