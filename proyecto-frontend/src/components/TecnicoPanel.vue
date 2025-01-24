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
        <div class="d-flex justify-content-center align-items-center">
          <div class="mt-5">
            <button class="btn btn-egibide mb-3 mb-sm-0 me-0 me-sm-3" @click="modale = true">Ver incidencias activas</button>
          </div>
        </div>
      </div>
    </div>

    <div v-if="modale" class="modal-backdrop">
      <div class="modal">
        <div class="d-flex justify-content-between">
        <h2 style="display: inline">Mis incidencias</h2>
        <button type="button" class="btn btn-secondary" @click="modale = false">Salir</button>
        </div>
        <div v-for="incident in incidents" :key="incident.id" :value="incident.id">
        <IncidentActiveTecnico :title="incident.title" :description="incident.description" :category="incident.importance"
        :type="String(incident.failuretypes_id)" :machines_id="String(incident.machines_id)"
        :status="incident.status" :register_date="incident.created_at" :machine_name="incident.machine.name"
        :failure_type_name="incident.failure_type.name" :incidents_id="incident.id"/>
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
import IncidentActiveTecnico from "@/components/IncidentActiveTecnico.vue";
import IncidentCard from "@/components/IncidentTecnico.vue";

export default {
  components: {IncidentCard, IncidentActiveTecnico},
  data() {
    const user = JSON.parse(sessionStorage.getItem("user"));
    return {
      modale: false, // Change from ref(false) to a boolean
      userName: user ? user.name : "",
      incidentsCount: 0,
      solvedtoday: 0,
      sections: [],
      campuses: [],
      failureTypes: [],
      incidents: [],
      selectedCampus: null,
      selectedSection: null,
      selectedFailureType: null,
      selectedImportance: null,
      newIncident:{
        title: "",
        description: "",
        importance: "",
        machines_id: null,
        failuretypes_id: null
      }
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
      axios.get("http://127.0.0.1:8000/api/auth/incidents/last-accepted",{
        headers: {
          Authorization: `bearer ${token}`,
        },
      }).then((response)=>{
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

    submitIncident() {
      const token = sessionStorage.getItem("token");
      if (token) {
        axios.post(`http://127.0.0.1:8000/api/auth/incidents`, this.newIncident ,{
          headers: {
            Authorization: `Bearer ${token}`,
          },
        })
            .then(response => {
              console.log('Incidencia creada exitosamente', response.data);
              this.modale = false;
              // Reset form or handle success
            })
            .catch(error => {
              console.error('Error al crear la incidencia', error);
              // Handle error
            });
      }else {
        console.error("No se encontró el token de autenticación. Por favor, inicia sesión.");
      }

    },
  finishIncident() {
    const token = sessionStorage.getItem('token');
    axios.post(`http://127.0.0.1:8000/api/auth/incidents/${this.incidents_id}/finish`, {}, {
      headers: { Authorization: `Bearer ${token}` }
    }).then(() => {
      this.$emit('refresh');
      window.location.reload();
    }).catch(error => {
      console.error('Error al finalizar la incidencia:', error);
    });
  },
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

    async fetchSections() {
      const token = sessionStorage.getItem("token");
      if (token && this.selectedCampus) {
        try {
          const response = await axios.get(`http://127.0.0.1:8000/api/auth/sections/campus/${this.selectedCampus}`, {
            headers: {
              Authorization: `Bearer ${token}`,
            },
          });
          this.sections = response.data.data;
          console.log("Secciones", this.sections)
        } catch (error) {
          console.error("Error al obtener las secciones:", error);
        }
      }
    },
    async fetchMachines() {
      const token = sessionStorage.getItem("token");
      if (token && this.selectedSection) {
        try {
          const response = await axios.get(`http://127.0.0.1:8000/api/auth/machines/section/${this.selectedSection}`, {
            headers: {
              Authorization: `Bearer ${token}`,
            },
          });
          this.machines = response.data.data;
          console.log("Secciones", this.machines)
        } catch (error) {
          console.error("Error al obtener las secciones:", error);
        }
      }
    },

    async resetFilter(){
      window.location.reload();
    }
  },
};
const fetchIncidents = async (reset = false) => {
  if (loading.value || !hasMore.value) return;  // Prevenir cargas duplicadas
  loading.value = true;  // Estado de carga activo
  const token = sessionStorage.getItem('token');  // Obtener token de sesión
  try {
    let url = `http://127.0.0.1:8000/api/auth/incidents/getall?page=${currentPage.value}`;
    if (props.filterType && props.filterValue) {
      url = `http://127.0.0.1:8000/api/auth/incidents/${props.filterValue}/${props.filterType}?page=${currentPage.value}`;
    }
    const response = await axios.get(url, {
      headers: { Authorization: `Bearer ${token}` },
    });
    const data = response.data.data || [];
    if (reset) {
      incidents.value = data;
    } else {
      incidents.value = [...incidents.value, ...data];
    }
    totalPages.value = response.data.last_page || 1;
    hasMore.value = currentPage.value < totalPages.value;
    if (!reset) {
      currentPage.value++;
    }
  } catch (error) {
    console.error('Error al obtener las incidencias:', error);
  } finally {
    loading.value = false;
  }
};
</script>