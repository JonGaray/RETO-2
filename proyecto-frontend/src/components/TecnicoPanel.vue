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
      <div class="mb-3 dropdown-wrapper">
        <label class="mt-3">Campus</label>
        <div class="dropdown-icon-container">
          <select class="form-control text-center" v-model="selectedCampus" @change="onFilterChange('campus')">
            <option v-for="campus in campuses" :key="campus.id" :value="campus.id">
              {{ campus.name }}
            </option>
          </select>
          <i class="fas fa-chevron-down dropdown-icon"></i>
        </div>
      </div>
      <div class="mb-3 dropdown-wrapper">
        <label class="mt-3">Sección</label>
        <div class="dropdown-icon-container">
          <select class="form-control text-center" v-model="selectedSection" @change="onFilterChange('section')">
            <option v-for="section in sections" :key="section.id" :value="section.id">
              {{ section.name }}
            </option>
          </select>
          <i class="fas fa-chevron-down dropdown-icon"></i>
        </div>
      </div>
      <div class="mb-3 dropdown-wrapper">
        <label class="mt-3">Tipo de Fallo</label>
        <div class="dropdown-icon-container">
          <select class="form-control text-center" v-model="selectedFailureType" @change="onFilterChange('failureType')" >
            <option v-for="failuretype in failureTypes" :key="failuretype.id" :value="failuretype.id">
              {{ failuretype.name }}
            </option>
          </select>
          <i class="fas fa-chevron-down dropdown-icon"></i>
        </div>
      </div>
      <div class="mb-3 dropdown-wrapper">
        <label class="mt-3">Importancia</label>
        <div class="dropdown-icon-container">
          <select class="form-control text-center" v-model="selectedImportance" @change="onFilterChange('importance')">
            <option value="parada">Parada</option>
            <option value="averia">Averia</option>
            <option value="aviso">Aviso</option>
            <option value="mantenimiento">Mantenimiento</option>
          </select>
          <i class="fas fa-chevron-down dropdown-icon"></i>
        </div>
      </div>
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
      userName: user ? user.name : "",
      incidentsCount: 0,
      solvedtoday: 0,
      sections: [],
      campuses: [],
      failureTypes: [],
      selectedCampus: null,
      selectedSection: null,
      selectedFailureType: null,
      selectedImportance: null,
    };
  },
  created() {
    const token = sessionStorage.getItem("token");
    if (token) {
      axios.get(`http://127.0.0.1:8000/api/auth/incidents/activeincidents`, {
        headers: {
          Authorization: `Bearer ${token}`,
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
          Authorization: `Bearer ${token}`,
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
          Authorization: `Bearer ${token}`,
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
          Authorization: `Bearer ${token}`,
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
          Authorization: `Bearer ${token}`,
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
      this.$emit("filtersChanged", updatedFilter);
    },
    async resetFilter() {
      window.location.reload();
    }
  },
};
</script>

<style scoped>
</style>