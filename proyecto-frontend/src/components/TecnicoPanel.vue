<template>
  <div class="col-12">
    <div class="card mt-5">
      <div class="card-body mt-2 mb-2">
        <div class="d-flex align-items-center justify-content-evenly mb-2">
          <img src="../img/foto-default.png" alt="Foto de perfil" class="rounded-circle img-fluid foto-perfil-usu"/>
          <h5 class="fw-bold mb-0 text-nowrap">{{ userName }}</h5>
        </div>
        <div class="d-flex justify-content-between mt-5">
          <span class="fw-bold">Incidencias activas:</span>
          <span>{{ incidentsCount }}</span>
        </div>
        <div class="d-flex justify-content-between">
          <span class="fw-bold">Resueltas hoy:</span>
          <span>{{ solvedtoday }}</span>
        </div>
      </div>
    </div>
    <div class="w-100"></div>
    <div class="card mt-5 p-3">
      <h4 class="text-center mb-4">Filtrar Incidencias</h4>
      <p class="text-muted text-center mb-4">
        Este es el lugar desde el cual el técnico puede filtrar las incidencias.
      </p>
      <label class="mt-3">Campus</label>
      <select class="form-control text-center" v-model="selectedCampus" @change="onFilterChange('campus')">
        <option v-for="campus in campuses" :key="campus.id" :value="campus.id">
          {{ campus.name }}
        </option>
      </select>
      <label class="mt-3">Sección</label>
      <select class="form-control text-center" v-model="selectedSection" @change="onFilterChange('section')">
        <option v-for="section in sections" :key="section.id" :value="section.id">
          {{ section.name }}
        </option>
      </select>
      <label class="mt-3">Tipo de Fallo</label>
      <select class="form-control text-center" v-model="selectedFailureType" @change="onFilterChange('failureType')" >
        <option v-for="failuretype in failureTypes" :key="failuretype.id" :value="failuretype.id">
          {{ failuretype.name }}
        </option>
      </select>
      <label class="mt-3">Importancia</label>
      <select class="form-control text-center" v-model="selectedImportance" @change="onFilterChange('importance')">
        <option value="parada">Parada</option>
        <option value="averia">Averia</option>
        <option value="aviso">Aviso</option>
        <option value="mantenimiento">Mantenimiento</option>
      </select>
      <button class="btn btn-egibide mt-3" @click="resetFilter">Reiniciar filtros </button>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  data() {
    const user = JSON.parse(sessionStorage.getItem("user"));
    return {
      userName: user ? user.name : "", // Definimos userName y le asignamos el valor del sessionStorage
      incidentsCount: 0, // Inicializamos el contador de incidencias
      solvedtoday: 0,
      sections: [],
      campuses: [],
      failureTypes: [],

      // Filtros seleccionados
      selectedCampus: null,
      selectedSection: null,
      selectedFailureType: null,
      selectedImportance: null,
    };
  },
  created() {
    const token = sessionStorage.getItem("token"); // Recupera el token desde sessionStorage
    if (token) {
      // Obtener datos iniciales
      axios.get(`http://127.0.0.1:8000/api/auth/incidents/activeincidents`, {
      headers: {
        Authorization: `Bearer ${token}`, // Añadir el token al header
      },
    })
  .then((response) => {
      this.incidentsCount = response.data.count;
    })
        .catch((error) => {
          console.error("Error al obtener el número de incidencias:", error);
        });

    axios.get(`http://127.0.0.1:8000/api/auth/incidents/solvedtoday`, {
    headers: {
      Authorization: `Bearer ${token}`, // Añadir el token al header
    },
  })
.then((response) => {
  this.solvedtoday = response.data.count;
})
    .catch((error) => {
      console.error("Error al obtener el número de incidencias:", error);
    });

axios.get(`http://127.0.0.1:8000/api/auth/campuses`, {
headers: {
  Authorization: `Bearer ${token}`, // Añadir el token al header
},
})
.then((response) => {
  this.campuses = response.data;
})
    .catch((error) => {
      console.error("Error al obtener los campuses:", error);
    });

axios.get(`http://127.0.0.1:8000/api/auth/sections`, {
headers: {
  Authorization: `Bearer ${token}`, // Añadir el token al header
},
})
.then((response) => {
  this.sections = response.data.data;
})
    .catch((error) => {
      console.error("Error al obtener las secciones:", error);
    });

axios.get(`http://127.0.0.1:8000/api/auth/failuretypes`, {
headers: {
  Authorization: `Bearer ${token}`, // Añadir el token al header
},
})
.then((response) => {
  this.failureTypes = response.data.data;
})
    .catch((error) => {
      console.error("Error al obtener los tipos de fallos:", error);
    });
} else {
  console.error("No se encontró el token de autenticación. Por favor, inicia sesión.");
}
},
methods: {
  async onFilterChange(filterType) {
    // Emitir solo el filtro que ha sido modificado
    let updatedFilter = {};

    switch (filterType) {
      case 'campus':
        updatedFilter.selectedCampus = this.selectedCampus;
        break;
      case 'section':
        updatedFilter.selectedSection = this.selectedSection;
        break;
      case 'failureType':
        updatedFilter.selectedFailureType = this.selectedFailureType;
        break;
      case 'importance':
        updatedFilter.selectedImportance = this.selectedImportance;
        break;
      default:
        updatedFilter.selectedReboot = "";
        break;
    }

    // Emitir solo el filtro actualizado
    this.$emit("filtersChanged", updatedFilter);
  },
  async resetFilter(){
    window.location.reload();
  }
},
};
</script>

<style scoped>
/* Aquí puedes añadir tus estilos */
</style>