<template>
  <div class="col-md-12 mt-5">
    <div class="custom-card">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Gestión de Usuarios</h4>
        <button class="btn btn-egibide" @click="showCreateModal = true">+ Nuevo Usuario</button>
      </div>
          <!-- Barra de busqueda -->
          <input
            v-model="searchQuery"
            type="text"
            @input="searchUsers"
            class="form-control mb-3"
            placeholder="Buscar por nombre de usuario"
          />
      <ul class="list-group">
        <li v-for="(user, index) in users" :key="index"
          class="list-group-item d-flex justify-content-between align-items-center">
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
              <input type="checkbox" :checked="user.status === 'habilitado'" @change="toggleStatus(user)">
              <span></span>
            </label>
            <button class="btn btn-outline-egibide btn-sm" @click="openEditUserModal(user)"><img class="pencil"
                src="../img/lapiz-de-cejas.png">Editar</button>
          </div>
        </li>
      </ul>

      <!-- Pop-up para crear un nuevo usuario -->
      <div v-if="showCreateModal" class="modal-backdrop">
        <div class="modal show">
          <h2>Crear Nuevo Usuario</h2>
          <label class="mt-5">Nombre</label>
          <input v-model="newUser.name" type="text" class="form-control" required />
          <label class="mt-3">Correo electrónico</label>
          <input v-model="newUser.email" type="email" class="form-control" required />
            <label class="mt-3">Contraseña</label>
          <input v-model="newUser.password" type="text" class="form-control" required />
          <label class="mt-3">Primer apellido</label>
          <input v-model="newUser.username1" type="text" class="form-control" required />
          <label class="mt-3">Segundo apellido</label>
          <input v-model="newUser.username2" type="text" class="form-control" required />

          <div class="mb-3 dropdown-wrapper">
            <label for="roleSelect" class="mt-2">Rol</label>
            <div class="dropdown-icon-container">
              <select id="roleSelect" v-model="newUser.role" class="form-control mt-1" required>
                <option value="user">User</option>
                <option value="tecnico">Técnico</option>
                <option value="admin">Admin</option>
              </select>
              <i class="fas fa-chevron-down dropdown-icon"></i>
            </div>
          </div>

          <div class="d-flex justify-content-between mt-5">
            <button class="btn btn-egibide" @click="createUser">Crear Usuario</button>
            <button class="btn btn-secondary" @click="closeModal">Cancelar</button>
          </div>
        </div>
      </div>

      <!-- Pop-up para editar un nuevo usuario -->
      <div v-if="showEditUserModal" class="modal-backdrop">
        <div class="modal show">
          <h2>Editar Usuario</h2>
          <label class="mt-5">Nombre</label>
          <input required v-model="editedUser.name" type="text" class="form-control mt-3"
            placeholder="Nombre del usuario" />
            <label class="mt-5">Correo electrónico</label>
          <input required id="emailEdit" v-model="editedUser.email" type="email" class="form-control mt-1"
            placeholder="Correo electrónico" />
            <label class="mt-5">Primer apellido</label>
          <input required id="username1Edit" v-model="editedUser.username1" type="text" class="form-control mt-1"
            placeholder="Username 1" />
            <label class="mt-5">Segundo apellido</label>
          <input required id="username2Edit" v-model="editedUser.username2" type="text" class="form-control mt-1"
            placeholder="Username 2" />
            <div class="mb-3 dropdown-wrapper">
          <label for="roleEdit" class="mt-2">Rol</label>
          <div class="dropdown-icon-container">
          <select required id="roleEdit" v-model="editedUser.role" class="form-control mt-1">
            <option value="user">Usuario</option>
            <option value="tecnico">Técnico</option>
            <option value="admin">Administrador</option>
          </select>
          <i class="fas fa-chevron-down dropdown-icon"></i>
            </div>
          </div>
          <div class="d-flex justify-content-between mt-5">
            <button type="button" class="btn btn-egibide" @click="saveEditedUser">Guardar</button>
            <button type="button" class="btn btn-secondary" @click="closeEditUserModal">Cancelar</button>
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
      users: [],
      showCreateModal: false,
      showEditUserModal: false,
      newUser: {
        name: '',
        email: '',
        password: '',
        username1: '',
        username2: '',
        role: '',
      },
      editedUser: null,
                    searchQuery: "",
    };
  },
  created() {
    const token = sessionStorage.getItem('token');
    axios.get('http://127.0.0.1:8000/api/auth/users', {
      headers: {
        Authorization: `Bearer ${token}`,  // Añadir el token al header
      }
    })
      .then(response => {
        this.users = response.data;
      })
      .catch(error => {
        console.error(error);
      });
  },
  methods: {
              searchUsers() {
              const token = sessionStorage.getItem("token");
              axios
                .get("http://127.0.0.1:8000/api/auth/users/search", {
                  params: { query: this.searchQuery }, // Enviar el término de búsqueda como parámetro
                  headers: {
                    Authorization: `Bearer ${token}`,
                  },
                })
                .then((response) => {
                  this.users = response.data; // Actualizar la lista con los resultados
                })
                .catch((error) => {
                  console.error("Error al buscar máquinas:", error);
                });
            },
    toggleStatus(user) {
      // Cambia el estado del usuario localmente
      user.status = user.status === 'habilitado' ? 'deshabilitado' : 'habilitado';

      // Envía la actualización al servidor
      this.updateUserStatus(user);
    },
    async updateUserStatus(user) {
      try {
        const token = sessionStorage.getItem('token');
        await axios.put(`http://127.0.0.1:8000/api/auth/users/${user.id}/status`, { status: user.status }, {
          headers: {
            Authorization: `Bearer ${token}`,  // Añadir el token al header
          }
        })
        console.log("Estado actualizado correctamente.");
      } catch (error) {
        console.error("Error al actualizar el estado:", error);
      }
    },
    async createUser() {
      try {
        const token = sessionStorage.getItem('token');
        const response = await axios.put(
          'http://127.0.0.1:8000/api/auth/users/create',
          this.newUser,
          {
            headers: {
              Authorization: `Bearer ${token}`,
            },
          }
        );
        this.users.push(response.data);
        this.closeModal();
      } catch (error) {
        console.error('Error al crear usuario:', error);
      }
    },
    openEditUserModal(user) {
      this.editedUser = { ...user }; // Copia los datos del usuario seleccionado
      this.showEditUserModal = true;
    },
    closeEditUserModal() {
      this.showEditUserModal = false;
      this.resetEditedUser();
    },
    resetEditedUser() {
      this.editedUser = null;
    },
    async saveEditedUser() {
      alert(this.editedUser)

      const token = sessionStorage.getItem('token');
      await axios.put(`http://127.0.0.1:8000/api/auth/users/${this.editedUser.id}/save`, this.editedUser, {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      })
        .then(() => {
          const index = this.users.findIndex((u) => u.id === this.editedUser.id);
          if (index !== -1) {
            this.users.splice(index, 1, this.editedUser);
          }
          this.closeEditUserModal();
        })
        .catch((error) => {
          console.error("Error al editar la máquina:", error);
        })
    },
    closeModal() {
      this.showCreateModal = false;
      this.newUser = {
        name: '',
        email: '',
        password: '',
        username1: '',
        username2: '',
        role: '',
      };
    },
  },
};
</script>

<style scoped></style>