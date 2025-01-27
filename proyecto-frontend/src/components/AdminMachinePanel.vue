<template>
  <div class="col-12">
    <div class="card p-3 mt-3">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Gestión de Maquinas</h4>
        <button class="btn btn-egibide" @click="showCreateModal = true">+ Nueva Maquina</button>
      </div>
      <input v-model="searchQuery" type="text" @input="searchMachines" class="form-control mb-3"
        placeholder="Buscar por nombre de máquina" />
      <ul class="list-group">
        <li v-for="(machine, index) in machines" :key="index"
          class="list-group-item d-flex justify-content-between align-items-center">
          <div>
            <strong>{{ machine.name }} </strong>
            <span class="text-muted d-block">{{ getSectionName(machine.sections_id) }}</span>
          </div>
          <div class="d-flex align-items-center">
            <label class="switch me-3">
              <div v-if="machine.status === 'habilitado'">
                <img class="activated" src="../img/boton-de-encendido.png">
              </div>
              <div v-else>
                <img class="desactivated" src="../img/interfaz.png">
              </div>
              <input type="checkbox" :checked="machine.status === 'habilitado'" @change="toggleStatus(machine)">
              <span></span>
            </label>
            <button class="btn btn-outline-egibide btn-sm" @click="editMachine(machine)"><img class="pencil"
                src="../img/lapiz-de-cejas.png">Editar</button>
          </div>
        </li>
      </ul>
      <div v-if="showCreateModal" class="modal-backdrop">
        <div class="modal show">
          <h2>Nueva Máquina</h2>
          <label for="machiName" class="mt-2">Nombre de la Maquina</label>
          <input required v-model="newMachine.name" type="text" class="form-control mt-3" />
                    <div class="mb-3 dropdown-wrapper">
            <label for="prioritySelect" class="mt-2">Prioridad</label>
            <div class="dropdown-icon-container">
              <select required id="prioritySelect" v-model="newMachine.priority" class="form-control mt-1"
                placeholder="Prioridad">
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
              </select>
              <i class="fas fa-chevron-down dropdown-icon"></i>
            </div>
          </div>
          <div class="mb-3 dropdown-wrapper">
            <label for="campusC" class="mt-2">Campus</label>
            <div class="dropdown-icon-container">
              <select id="campusC" class="form-control mt-1" v-model="selectedCampus" @change="fetchSections">
                <option v-for="campus in campuses" :key="campus.id" :value="campus.id">
                  {{ campus.name }}
                </option>
              </select>
              <i class="fas fa-chevron-down dropdown-icon"></i>
            </div>
          </div>
          <div class="mb-3 dropdown-wrapper">
            <label for="sectionSelect" class="mt-2">Sección</label>
            <div class="dropdown-icon-container">
              <select required id="sectionSelect" v-model="newMachine.sections_id" class="form-control mt-1">
                <option v-for="section in sectionsCampus" :key="section.id" :value="section.id">
                  {{ section.name }}
                </option>
              </select>
              <i class="fas fa-chevron-down dropdown-icon"></i>
            </div>
          </div>
          <div class="d-flex justify-content-between mt-4">
            <button type="button" class="btn btn-egibide" @click="createMachine">Crear Maquina</button>
            <button type="button" class="btn btn-secondary" @click="closeModal">Cancelar</button>
          </div>
        </div>
      </div>
      <div v-if="showEditModal" class="modal-backdrop">
        <div class="modal show">
          <h2>Editar Máquina</h2>
          <label>Nombre de la Maquina</label>
          <input required v-model="editedMachine.name" type="text" class="form-control mt-3" />
          <div class="mb-3 dropdown-wrapper">
            <label for="priorityEditSelect" class="mt-2">Prioridad:</label>
            <div class="dropdown-icon-container">
              <select required id="priorityEditSelect" v-model="editedMachine.priority" class="form-control mt-1">
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
              </select>
              <i class="fas fa-chevron-down dropdown-icon"></i>
            </div>
          </div>
          <div class="mb-3 dropdown-wrapper">
            <label for="campus" class="mt-2">Campus:</label>
            <div class="dropdown-icon-container">
              <select id="campus" class="form-control mt-1" v-model="selectedCampus" @change="fetchSections">
                <option v-for="campus in campuses" :key="campus.id" :value="campus.id">
                  {{ campus.name }}
                </option>
              </select>
              <i class="fas fa-chevron-down dropdown-icon"></i>
            </div>
          </div>
          <div class="mb-3 dropdown-wrapper">
            <label for="sectionEditSelect" class="mt-2">Seleccionar sección:</label>
            <div class="dropdown-icon-container">
              <select required id="sectionEditSelect" v-model="editedMachine.sections_id" class="form-control mt-1">
                <option v-for="section in sectionsCampus" :key="section.id" :value="section.id">
                  {{ section.name }}
                </option>
              </select>
              <i class="fas fa-chevron-down dropdown-icon"></i>
            </div>
          </div>
          <div class="d-flex justify-content-between mt-4">
            <button type="button" class="btn btn-egibide" @click="saveMachineEdit">Guardar</button>
            <button type="button" class="btn btn-secondary" @click="closeModal">Cancelar</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2';

export default {
  data() {
    return {
      machines: [],
      sections: [],
      campuses: [],
      sectionsCampus: [],
      showCreateModal: false,
      showEditModal: false,
      newMachine: {
        name: "",
        priority: "",
        last_maintenance: "",
        status: "habilitado",
        sections_id: null,
      },
      editedMachine: null,
      selectedCampus: null,
      searchQuery: "",
    }
  },
  created() {
    const token = sessionStorage.getItem('token');
    axios.get('http://172.20.228.150:8080/api/auth/machines', {
      headers: {
        Authorization: `Bearer ${token}`,
      }
    })
      .then(response => {
        this.machines = response.data;
      })
      .catch(error => {
        console.error(error);
      });
    axios.get('http://172.20.228.150:8080/api/auth/sections/getsections', {
      headers: {
        Authorization: `Bearer ${token}`,
      }
    })
      .then(response => {
        this.sections = response.data;
      })
      .catch(error => {
        console.error(error);
      });
    axios.get('http://172.20.228.150:8080/api/auth/campuses', {
      headers: {
        Authorization: `Bearer ${token}`,
      }
    })
      .then(response => {
        this.campuses = response.data;
      })
      .catch(error => {
        console.error(error);
      });
  },
  methods: {
    searchMachines() {
      const token = sessionStorage.getItem("token");
      axios
        .get("http://172.20.228.150:8080/api/auth/machines/search", {
          params: { query: this.searchQuery },
          headers: {
            Authorization: `Bearer ${token}`,
          },
        })
        .then((response) => {
          this.machines = response.data;
        })
        .catch((error) => {
          console.error("Error al buscar máquinas:", error);
        });
    },
    async fetchSections() {
      if (!this.selectedCampus) return;
      const token = sessionStorage.getItem("token");
      const response = await axios.get(`http://172.20.228.150:8080/api/auth/sections/getbycampus/${this.selectedCampus}`,
        {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        });
      this.sectionsCampus = response.data;
      this.selectedSection = null;
    },
    createMachine() {
      const currentDate = new Date().toISOString().split('T')[0];
      this.newMachine.last_maintenance = currentDate;
      if (!this.newMachine.name || !this.newMachine.priority || !this.newMachine.sections_id) {
        alert("Todos los campos son obligatorios.");
        return;
      }
      const token = sessionStorage.getItem("token");
      if (this.newMachine.name.trim().length > 255) {
        Swal.fire({
          icon: 'error',
          title: 'Exceso de caracteres',
          text: 'El nombre no puede superar los 255 caracteres',
        });
        return;
      }
      axios
        .post(
          "http://172.20.228.150:8080/api/auth/machines/create",
          this.newMachine,
          {
            headers: {
              Authorization: `Bearer ${token}`,
            },
          }
        )
        .then((response) => {
          this.machines.push(response.data.maquina);
          this.closeModal();
        })
        .catch((error) => {
          console.error("Error al crear la máquina:", error);
        });
    },
    editMachine(machine) {
      this.editedMachine = { ...machine };
      this.showEditModal = true;
    },
    saveMachineEdit() {
      const token = sessionStorage.getItem("token");
      if (this.editedMachine.name.trim().length > 255) {
        Swal.fire({
          icon: 'error',
          title: 'Exceso de caracteres',
          text: 'El nombre no puede superar los 255 caracteres',
        });
        return;
      }
      axios
        .put(`http://172.20.228.150:8080/api/auth/machines/${this.editedMachine.id}/edit`, this.editedMachine, {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        })
        .then(() => {
          const index = this.machines.findIndex((m) => m.id === this.editedMachine.id);
          if (index !== -1) {
            this.machines.splice(index, 1, this.editedMachine);
          }
          this.closeModal();
        })
        .catch((error) => {
          console.error("Error al editar la máquina:", error);
        });
    },
    toggleStatus(machine) {
      machine.status =
        machine.status === "habilitado" ? "deshabilitado" : "habilitado";
      this.updateMachineStatus(machine);
    },
    async updateMachineStatus(machine) {
      const token = sessionStorage.getItem("token");
      try {
        await axios.put(
          `http://172.20.228.150:8080/api/auth/machines/${machine.id}/status`,
          { status: machine.status },
          {
            headers: {
              Authorization: `Bearer ${token}`,
            },
          }
        );
        console.log("Estado actualizado correctamente.");
      } catch (error) {
        console.error("Error al actualizar el estado:", error);
      }
    },
    getSectionName(sectionId) {
      const section = this.sections.find(sec => sec.id === sectionId);
      return section ? section.name : 'Desconocida';
    },
    closeModal() {
      this.showCreateModal = false;
      this.showEditModal = false;
      this.newMachine = {
        name: "",
        priority: "",
        last_maintenance: "",
        status: "habilitado",
        sections_id: null,
      };
      this.editedMachine = null;
    },
  },
};
</script>

<style scoped></style>