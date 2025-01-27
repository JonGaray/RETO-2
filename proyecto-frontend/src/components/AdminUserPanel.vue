<template>
  <div class="col-12">
    <div class="card p-3 mt-3">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Gestión de Usuarios</h4>
        <button class="btn btn-egibide mt-3 mt-md-0" @click="showCreateModal = true">+ Nuevo Usuario</button>
      </div>
      <input v-model="searchQuery" type="text" @input="searchUsers" class="form-control mb-3"
        placeholder="Buscar por nombre de usuario" />
      <ul class="list-group">
        <li v-for="(user, index) in users" :key="index"
          class="list-group-item d-flex justify-content-between align-items-center flex-column flex-md-row">
          <div class="text-left text-md-left">
            <strong>{{ user.name }} </strong>
            <span class="text-muted d-block badge-initcap">{{ user.role }}</span>
          </div>
          <div class="d-flex align-items-center mt-3 mt-md-0">
            <label class="switch me-3">
              <div v-if="user.status === 'habilitado'">
                <img class="activated" src="../img/boton-de-encendido.png" alt="Habilitado">
              </div>
              <div v-else>
                <img class="desactivated" src="../img/interfaz.png" alt="Deshabilitado">
              </div>
              <input type="checkbox" :checked="user.status === 'habilitado'" @change="toggleStatus(user)">
              <span></span>
            </label>
            <button class="btn btn-outline-egibide btn-sm" @click="openEditUserModal(user)">
              <img class="pencil" src="../img/lapiz-de-cejas.png" alt="Editar"> Editar
            </button>
          </div>
        </li>
      </ul>
      <div v-if="showCreateModal" class="modal-backdrop">
        <div class="modal show">
          <h2>Crear Nuevo Usuario</h2>
          <form @submit.prevent="createUser">
            <div class="form-group">
              <label class="mt-3">Nombre</label>
              <input v-model="newUser.name" type="text" class="form-control" required />
            </div>
            <div class="form-group">
              <label class="mt-3">Correo electrónico</label>
              <input v-model="newUser.email" type="email" class="form-control" required />
            </div>
            <div class="form-group">
              <label class="mt-3">Contraseña</label>
              <input v-model="newUser.password" type="text" class="form-control" required />
            </div>
            <div class="form-group">
              <label class="mt-3">Primer apellido</label>
              <input v-model="newUser.username1" type="text" class="form-control" required />
            </div>
            <div class="form-group">
              <label class="mt-3">Segundo apellido</label>
              <input v-model="newUser.username2" type="text" class="form-control" required />
            </div>
            <div class="mb-3 dropdown-wrapper">
              <label class="mt-3">Rol</label>
              <div class="dropdown-icon-container">
                <select v-model="newUser.role" class="form-control" required>
                  <option value="user">User</option>
                  <option value="tecnico">Técnico</option>
                  <option value="admin">Admin</option>
                </select>
                <i class="fas fa-chevron-down dropdown-icon"></i>
              </div>
            </div>
            <div class="d-flex justify-content-between mt-4">
              <button class="btn btn-egibide" type="submit">Crear Usuario</button>
              <button class="btn btn-secondary" @click="closeModal">Cancelar</button>
            </div>
          </form>
        </div>
      </div>
      <div v-if="showEditUserModal" class="modal-backdrop">
        <div class="modal show">
          <h2>Editar Usuario</h2>
          <form @submit.prevent="saveEditedUser">
            <div class="form-group">
              <label class="mt-3">Nombre</label>
              <input v-model="editedUser.name" type="text" class="form-control" required />
            </div>
            <div class="form-group">
              <label class="mt-3">Correo electrónico</label>
              <input v-model="editedUser.email" type="email" class="form-control" required />
            </div>
            <div class="form-group">
              <label class="mt-3">Primer apellido</label>
              <input v-model="editedUser.username1" type="text" class="form-control" required />
            </div>
            <div class="form-group">
              <label class="mt-3">Segundo apellido</label>
              <input v-model="editedUser.username2" type="text" class="form-control" required />
            </div>
            <div class="mb-3 dropdown-wrapper">
              <label class="mt-3">Rol</label>
              <div class="dropdown-icon-container">
                <select v-model="editedUser.role" class="form-control" required>
                  <option value="user">User</option>
                  <option value="tecnico">Técnico</option>
                  <option value="admin">Admin</option>
                </select>
                <i class="fas fa-chevron-down dropdown-icon"></i>
              </div>
            </div>
            <div class="d-flex justify-content-between mt-4">
              <button type="submit" class="btn btn-egibide">Guardar</button>
              <button type="button" class="btn btn-secondary" @click="closeEditUserModal">Cancelar</button>
            </div>
          </form>
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
    axios.get('http://172.20.228.150:8080/api/auth/users', {
      headers: {
        Authorization: `Bearer ${token}`,
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
        .get("http://172.20.228.150:8080/api/auth/users/search", {
          params: { query: this.searchQuery },
          headers: {
            Authorization: `Bearer ${token}`,
          },
        })
        .then((response) => {
          this.users = response.data;
        })
        .catch((error) => {
          console.error("Error al buscar máquinas:", error);
        });
    },
    toggleStatus(user) {
      user.status = user.status === 'habilitado' ? 'deshabilitado' : 'habilitado';
      this.updateUserStatus(user);
    },
    async updateUserStatus(user) {
      try {
        const token = sessionStorage.getItem('token');
        await axios.put(`http://172.20.228.150:8080/api/auth/users/${user.id}/status`, { status: user.status }, {
          headers: {
            Authorization: `Bearer ${token}`,
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
        const email = this.newUser.email.trim();
        if (this.newUser.name.trim().length > 255) {
          Swal.fire({
            icon: 'error',
            title: 'Exceso de caracteres',
            text: 'El nombre no puede superar los 255 caracteres',
          });
          return;
        }
        if (this.newUser.username1.trim().length > 255) {
          Swal.fire({
            icon: 'error',
            title: 'Exceso de caracteres',
            text: 'El primer apellido no puede superar los 255 caracteres',
          });
          return;
        }
        if (this.newUser.username2.trim().length > 255) {
          Swal.fire({
            icon: 'error',
            title: 'Exceso de caracteres',
            text: 'El segundo apellido no puede superar los 255 caracteres',
          });
          return;
        }
        if (!validarDominioCorreo(email)) {
          Swal.fire({
            icon: 'error',
            title: 'Correo inválido',
            text: 'El correo debe tener el dominio @ikasle.egibide.org o @egibide.org',
          });
          return;
        }
        if (this.newUser.password.length < 8) {
          Swal.fire({
            icon: 'error',
            title: 'Contraseña inválida',
            text: 'La contraseña debe tener al menos 8 caracteres.',
          });
          return;
        }
        const response = await axios.post(
          'http://172.20.228.150:8080/api/auth/users/create',
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
      }
    },
    openEditUserModal(user) {
      this.editedUser = { ...user };
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
      if (this.newUser.name.trim().length > 255) {
        Swal.fire({
          icon: 'error',
          title: 'Exceso de caracteres',
          text: 'El nombre no puede superar los 255 caracteres',
        });
        return;
      }
      if (this.newUser.username1.trim().length > 255) {
        Swal.fire({
          icon: 'error',
          title: 'Exceso de caracteres',
          text: 'El primer apellido no puede superar los 255 caracteres',
        });
        return;
      }
      if (this.newUser.username2.trim().length > 255) {
        Swal.fire({
          icon: 'error',
          title: 'Exceso de caracteres',
          text: 'El segundo apellido no puede superar los 255 caracteres',
        });
        return;
      }
      if (!validarDominioCorreo(email)) {
        Swal.fire({
          icon: 'error',
          title: 'Correo inválido',
          text: 'El correo debe tener el dominio @ikasle.egibide.org o @egibide.org',
        });
        return;
      }
      await axios.put(`http://172.20.228.150:8080/api/auth/users/${this.editedUser.id}/save`, this.editedUser, {
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
function validarDominioCorreo(correo) {
  const regex = /^[^@\s]+@(ikasle\.egibide\.org|egibide\.org)$/;
  return regex.test(correo);
}
</script>

<style scoped></style>