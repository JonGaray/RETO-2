<template>
    <div class="col-md-12 mt-5">
        <div class="custom-card">
          <div class="d-flex justify-content-between align-items-center mb-4">
            <h4>Gestión de Usuarios</h4>
            <button class="btn btn-egibide">+ Nuevo Usuario</button>
          </div>
          <ul class="list-group">
            <li v-for="(user, index) in users"
            :key="index" class="list-group-item d-flex justify-content-between align-items-center">
              <div>
                <strong>{{ user.name }} </strong>
                <span class="text-muted d-block">{{ user.role }}</span>
              </div>
              <div class="d-flex align-items-center">
                <label class="switch me-3">
                  <div v-if="user.status === 'habilitado'">
                    <img class="activated" src="../img/boton-de-encendido.png">
                  </div>
                  <div v-else>
                    <img class="desactivated" src="../img/interfaz.png">
                  </div>
                  <input type="checkbox" :checked="user.status === 'habilitado'" 
                  @change="toggleStatus(user)">
                  <span></span>
                </label>
                <button class="btn btn-outline-primary btn-sm"><img class="pencil" src="../img/lapiz-de-cejas.png">Editar</button>
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
                    users: []
                };
            },
            created() {
                axios.get('http://127.0.0.1:8000/api/users')
                    .then(response => {
                        this.users = response.data;
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
                    await axios.put(`http://127.0.0.1:8000/api/users/${user.id}/status`, { status: user.status });
                    console.log("Estado actualizado correctamente.");
                } catch (error) {
                    console.error("Error al actualizar el estado:", error);
                }
                },
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

  /* Iconos */
  .pencil{
    width: 20px;
    height: 20px;
    margin-right: 5px;
  }

  .activated{
    width: 25px;
    height: 25px;
    margin-right: 7px;
  }

  .desactivated{
    width: 30px;
    height: 30px;
    margin-right: 5px;
  }
  </style>