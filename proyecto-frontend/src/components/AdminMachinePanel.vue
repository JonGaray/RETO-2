<template>
    <div class="col-md-12 mt-5">
        <div class="custom-card">
          <div class="d-flex justify-content-between align-items-center mb-4">
            <h4>Gestión de Maquinas</h4>
            <button class="btn btn-egibide" @click="showCreateModal = true">+ Nueva Maquina</button>
          </div>
          <ul class="list-group">
            <li v-for="(machine, index) in machines"
            :key="index" class="list-group-item d-flex justify-content-between align-items-center">
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
                  <input type="checkbox" :checked="machine.status === 'habilitado'" 
                  @change="toggleStatus(machine)">
                  <span></span>
                </label>
                <button class="btn btn-outline-primary btn-sm" @click="editMachine(machine)"><img class="pencil" src="../img/lapiz-de-cejas.png"  >Editar</button>
              </div>
            </li>
          </ul>
          <!-- Pop-up creacion maquina -->
          <div v-if="showCreateModal" class="modal-backdrop">
          <div class="modal show">
            <h2>Nueva Máquina</h2>
            <input required v-model="newMachine.name" type="text" class="form-control mt-3" placeholder="Nombre de la máquina" />
            <label for="prioritySelect" class="mt-2">Prioridad:</label>
            <select required id="prioritySelect" v-model="newMachine.priority" class="form-control mt-1" placeholder="Prioridad">
              <option value="1">1</option>
              <option value="2">2</option>
              <option value="3">3</option>
            </select>
            <label for="campusC" class="mt-2">Campus:</label>
            <select id="campusC" class="form-control mt-1" v-model="selectedCampus" @change="fetchSections">
              <option v-for="campus in campuses" :key="campus.id" :value="campus.id">
                {{ campus.name }}
              </option>
            </select>
            <label for="sectionSelect" class="mt-2">Seleccionar seccion:</label>
            <select required id="sectionSelect" v-model="newMachine.sections_id" class="form-control mt-1">
              <option v-for="section in sectionsCampus" :key="section.id" :value="section.id">
                {{ section.name }}
              </option>
            </select>
            <div class="d-flex justify-content-between mt-5">
              <button type="button" class="btn btn-outline-egibide" @click="closeModal">Cancelar</button>
              <button type="button" class="btn btn-egibide" @click="createMachine">Crear</button>
            </div>
          </div>
        </div>

        <!-- pop-up editar maquina -->
        <div v-if="showEditModal" class="modal-backdrop">
          <div class="modal show">
            <h2>Editar Máquina</h2>
            <input
              required
              v-model="editedMachine.name"
              type="text"
              class="form-control mt-3"
              placeholder="Nombre de la máquina"
            />
            <label for="priorityEditSelect" class="mt-2">Prioridad:</label>
            <select
              required
              id="priorityEditSelect"
              v-model="editedMachine.priority"
              class="form-control mt-1"
            >
              <option value="1">1</option>
              <option value="2">2</option>
              <option value="3">3</option>
            </select>
            <label for="campus" class="mt-2">Campus:</label>
            <select id="campus" class="form-control mt-1" v-model="selectedCampus" @change="fetchSections">
              <option v-for="campus in campuses" :key="campus.id" :value="campus.id">
                {{ campus.name }}
              </option>
            </select>
            <label for="sectionEditSelect" class="mt-2">Seleccionar sección:</label>
            <select
              required
              id="sectionEditSelect"
              v-model="editedMachine.sections_id"
              class="form-control mt-1"
            >
              <option v-for="section in sectionsCampus" :key="section.id" :value="section.id">
                {{ section.name }}
              </option>
            </select>
            <div class="d-flex justify-content-between mt-5">
              <button type="button" class="btn btn-outline-egibide" @click="closeModal">Cancelar</button>
              <button type="button" class="btn btn-egibide" @click="saveMachineEdit">Guardar</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script>

import axios from 'axios';

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
            selectedCampus: null
      }
    },
    created() {
        const token = sessionStorage.getItem('token');
        
        axios.get('http://127.0.0.1:8000/api/auth/machines', {
            headers: {
                    Authorization: `Bearer ${token}`,  // Añadir el token al header
                }
        })
            .then(response => {
                this.machines = response.data;
            })
            .catch(error => {
                console.error(error);
            });
        axios.get('http://127.0.0.1:8000/api/auth/sections/getsections', {
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
        axios.get('http://127.0.0.1:8000/api/auth/campuses', {
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
      async fetchSections() {
        
      if (!this.selectedCampus) return;
      const token = sessionStorage.getItem("token");
      const response = await axios.get(`http://127.0.0.1:8000/api/auth/sections/getbycampus/${this.selectedCampus}`, 
          {
            headers: {
              Authorization: `Bearer ${token}`,
            },
          });
      this.sectionsCampus = response.data;
      this.selectedSection = null; // Reinicia la selección de secciones
    },
    createMachine() {
        const currentDate = new Date().toISOString().split('T')[0]; // Obtiene la fecha en formato YYYY-MM-DD
      this.newMachine.last_maintenance = currentDate;

      if (!this.newMachine.name || !this.newMachine.priority || !this.newMachine.sections_id) {
        alert("Todos los campos son obligatorios.");
        return;
    }
      const token = sessionStorage.getItem("token");
      console.log(this.newMachine);
      axios
        .post(
          "http://127.0.0.1:8000/api/auth/machines/create",
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
      axios
        .put(`http://127.0.0.1:8000/api/auth/machines/${this.editedMachine.id}/edit`, this.editedMachine, {
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
      // Cambiar estado localmente
      machine.status =
        machine.status === "habilitado" ? "deshabilitado" : "habilitado";

      // Enviar actualización al servidor
      this.updateMachineStatus(machine);
    },
    async updateMachineStatus(machine) {
      const token = sessionStorage.getItem("token");
      try {
        await axios.put(
          `http://127.0.0.1:8000/api/auth/machines/${machine.id}/status`,
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
  
  <style scoped>

  </style>