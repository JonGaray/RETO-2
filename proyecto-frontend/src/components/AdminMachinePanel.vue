<template>
    <div class="col-md-12 mt-5">
        <div class="custom-card">
          <div class="d-flex justify-content-between align-items-center mb-4">
            <h4>Gestión de Maquinas</h4>
            <button class="btn btn-egibide">+ Nueva Maquina</button>
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
                  <input type="checkbox" :checked="machine.status === 'habilitado'" 
                  @change="toggleStatus(machine)">
                  <span></span>
                </label>
                <button class="btn btn-outline-primary btn-sm">Editar</button>
              </div>
            </li>
          </ul>
        </div>
      </div>
  </template>
  
  <script>
         import axios from 'axios';

export default {
    data() {
        return {
            machines: [],
            sections: []
        };
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
    },
    methods: {
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
        }
    },
};
  </script>
  
  <style scoped>
  .switch {
      display: inline-flex;
      align-items: center;
    }
    .switch input {
      display: none;
    }
    .switch span {
      width: 40px;
      height: 20px;
      background-color: #ccc;
      border-radius: 10px;
      position: relative;
      cursor: pointer;
    }
    .switch span::before {
      content: '';
      width: 16px;
      height: 16px;
      background-color: #fff;
      border-radius: 50%;
      position: absolute;
      top: 2px;
      left: 2px;
      transition: 0.3s;
    }
    .switch input:checked + span {
      background-color: #790253;
    }
    .switch input:checked + span::before {
      transform: translateX(20px);
    }
  </style>