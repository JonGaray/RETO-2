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
                <span class="text-muted d-block">{{ user.role }}</span>
              </div>
              <div class="d-flex align-items-center">
                <label class="switch me-3">
                  <input type="checkbox" :checked="machine.status === 'habilitado'" 
                  @change="toggleStatus(user)">
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
            machines: []
        };
    },
    created() {
        const token = sessionStorage.getItem('token');
        axios.post('http://127.0.0.1:8000/api/machines', {}, {
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
    },
    methods: {
        toggleStatus(user) {
        // Cambia el estado del usuario localmente
        user.status = user.status === 'habilitado' ? 'deshabilitado' : 'habilitado';

        // (Opcional) Envía la actualización al servidor
        this.updateUserStatus(user);
        },
        async updateUserStatus(user) {
        try {
            await axios.put(`http://127.0.0.1:8000/api/users/${user.id}`, { status: user.status });
            console.log("Estado actualizado correctamente.");
        } catch (error) {
            console.error("Error al actualizar el estado:", error);
        }
        },
    },
};
  </script>
  
  <style scoped>

  </style>