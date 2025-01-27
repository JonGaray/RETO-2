<template>
  <div class="col-12">
    <div class="card mt-5">
      <div class="card-body mt-2 mb-2">
        <div class="d-flex align-items-center justify-content-evenly mb-2">
          <img src="../img/foto-default.png" alt="Foto de perfil" class="rounded-circle img-fluid foto-perfil-usu" />
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
        <div class="d-flex justify-content-center">
          <button type="button" class="btn btn-egibide mt-5" @click="modale = true">Ver incidencias activas</button>
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
        <select class="form-control text-center" v-model="selectedFailureType" @change="onFilterChange('failuretype')">
          <option v-for="failuretype in failureTypes" :key="failuretype.id" :value="failuretype.id">
            {{ failuretype.name }}
        </option>
      </select>
      <i class="fas fa-chevron-down dropdown-icon"></i>
            </div>
          </div>
      <div class="mb-3 dropdown-wrapper">
      <label class="mt-3">Gravedad</label>
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
      <div class="mb-3 dropdown-wrapper">
      <label class="mt-3">Estado</label>
      <div class="dropdown-icon-container">
      <select class="form-control text-center" v-model="selectedStatus" @change="onFilterChange('status')">
        <option value="nuevo">Nuevo</option>
        <option value="proceso">En proceso</option>
        <option value="terminado">Terminado</option>
      </select>
      <i class="fas fa-chevron-down dropdown-icon"></i>
            </div>
          </div>
      <div class="mb-3 dropdown-wrapper">
      <label class="mt-3">Máquina</label>
      <div class="dropdown-icon-container">
        <select class="form-control text-center" v-model="selectedMachine" @change="onFilterChange('machine')">
          <option v-for="machine in machines" :key="machine.id" :value="machine.id">
            {{ machine.name }}
        </option>
      </select>
      <i class="fas fa-chevron-down dropdown-icon"></i>
            </div>
          </div>
      <button class="btn btn-egibide mt-3" @click="resetFilter">Reiniciar filtros</button>
    </div>
  </div>
  <div v-if="modale" class="modal-backdrop">
    <div class="modal">
      <div class="d-flex justify-content-between mb-3">
        <h2 style="display: inline">Mis incidencias</h2>
        <button type="button" class="btn btn-egibide" @click="modale = false">Salir</button>
      </div>
      <div v-for="incident in incidents" :key="incident.id" :value="incident.id">
        <IncidentActiveTecnico :title="incident.title" :description="incident.description"
          :category="incident.importance" :type="String(incident.failuretypes_id)"
          :machines_id="String(incident.machines_id)" :status="incident.status" :register_date="incident.created_at"
          :machine_name="incident.machine.name" :failure_type_name="incident.failure_type.name"
          :incidents_id="incident.id" />
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import IncidentActiveTecnico from "@/components/IncidentActiveTecnico.vue";

export default {
  components: { IncidentActiveTecnico },
  props: ['id'],
  emits: ['filtersChanged'],
  data() {
    const user = JSON.parse(sessionStorage.getItem("user"));
    return {
      modale: false,
      userName: user ? user.name : "",
      incidentsCount: 0,
      solvedtoday: 0,
      sections: [],
      campuses: [],
      failureTypes: [],
      machines: [],
      incidents: [],
      selectedCampus: null,
      selectedSection: null,
      selectedFailureType: null,
      selectedImportance: null,
      selectedStatus: null,
      selectedMachine: null,
    };
  },
  created() {
    const token = sessionStorage.getItem("token");
    if (token) {
      axios.get(`http://172.20.228.150:8080/api/auth/incidents/activeincidents`, {
        headers: { Authorization: `Bearer ${token}` }
      })
        .then((response) => { this.incidentsCount = response.data.count; })
        .catch((error) => { console.error("Error al obtener el número de incidencias:", error); });
      axios.get(`http://172.20.228.150:8080/api/auth/incidents/solvedtoday`, {
        headers: { Authorization: `Bearer ${token}` }
      })
        .then((response) => { this.solvedtoday = response.data.count; })
        .catch((error) => { console.error("Error al obtener las incidencias resueltas hoy:", error); });
      axios.get(`http://172.20.228.150:8080/api/auth/campuses`, {
        headers: { Authorization: `Bearer ${token}` }
      })
        .then((response) => { this.campuses = response.data; })
        .catch((error) => { console.error("Error al obtener los campuses:", error); });
      axios.get(`http://172.20.228.150:8080/api/auth/sections`, {
        headers: { Authorization: `Bearer ${token}` }
      })
        .then((response) => { this.sections = response.data.data; })
        .catch((error) => { console.error("Error al obtener las secciones:", error); });
      axios.get(`http://172.20.228.150:8080/api/auth/failuretypes`, {
        headers: { Authorization: `Bearer ${token}` }
      })
        .then((response) => { this.failureTypes = response.data.data; })
        .catch((error) => { console.error("Error al obtener los tipos de fallos:", error); });
      axios.get(`http://172.20.228.150:8080/api/auth/machines`, {
        headers: { Authorization: `Bearer ${token}` }
      })
        .then((response) => { this.machines = response.data; })
        .catch((error) => { console.error("Error al obtener las máquinas:", error); });
      axios.get("http://172.20.228.150:8080/api/auth/incidents/last-accepted", {
        headers: {
          Authorization: `bearer ${token}`,
        },
      }).then((response) => {
        this.incidents = response.data
      }
      ).catch((error) => {
        console.error("Error al obtener las incidencias:", error);
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
        case 'failuretype':
          updatedFilter.selectedFailureType = this.selectedFailureType;
          break;
        case 'importance':
          updatedFilter.selectedImportance = this.selectedImportance;
          break;
        case 'status':
          updatedFilter.selectedStatus = this.selectedStatus;
          break;
        case 'machine':
          updatedFilter.selectedMachine = this.selectedMachine;
          break;
        default:
          updatedFilter.selectedReboot = "";
          break;
      }
      this.$emit("filtersChanged", updatedFilter);
    },
    resetFilter() {
      this.selectedCampus = null;
      this.selectedSection = null;
      this.selectedFailureType = null;
      this.selectedImportance = null;
      this.selectedStatus = null;
      this.selectedMachine = null;
      this.$emit("filtersChanged", {
        selectedCampus: null,
        selectedSection: null,
        selectedFailureType: null,
        selectedImportance: null,
        selectedStatus: null,
        selectedMachine: null
      });
    }
  },
};
</script>

<style scoped></style>