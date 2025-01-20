<template>
    <div class="container mt-5">
        <div class="d-flex justify-content-center align-items-center" style="height: 100vh;">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h2 class="card-title text-center">Registrar Usuario</h2>
                        <form @submit.prevent="handleRegister">
                            <div class="mb-3">
                                <label for="email" class="form-label">Correo Electronico</label>
                                <input v-model="email" type="email" id="email" class="form-control"
                                    placeholder="Enter your email" required />
                            </div>
                            <div class="mb-3">
                                <label for="name" class="form-label">Nombre</label>
                                <input v-model="name" type="text" id="name" class="form-control"
                                    placeholder="Enter your name" required />
                            </div>
                            <div class="mb-3">
                                <label for="username1" class="form-label">Primer Apellido</label>
                                <input v-model="username1" type="text" id="username1" class="form-control"
                                    placeholder="Enter your first username" required />
                            </div>
                            <div class="mb-3">
                                <label for="username2" class="form-label">Segundo Apellido</label>
                                <input v-model="username2" type="text" id="username2" class="form-control"
                                    placeholder="Enter your second username" />
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Contraseña</label>
                                <input v-model="password" type="password" id="password" class="form-control"
                                    placeholder="Enter your password" required />
                            </div>
                            <div class="mb-3">
                                <label for="role" class="form-label">Rol</label>
                                <select v-model="role" id="role" class="form-control" required>
                                    <option value="" disabled>Selecciona un rol</option>
                                    <option value="admin">Administrador</option>
                                    <option value="user">Usuario</option>
                                    <option value="tecnico">Tecnico</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="status" class="form-label">Estado</label>
                                <select v-model="status" id="status" class="form-control" required>
                                    <option value="" disabled>Selecciona un estado</option>
                                    <option value="habilitado">Habilitado</option>
                                    <option value="deshabilitado">Deshabilitado</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary w-100" :disabled="isLoading">
                                Registrarse
                            </button>
                            <div v-if="error" class="alert alert-danger mt-3">
                                <p>{{ error }}</p>
                            </div>
                        </form>
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
            email: '',
            name: '',
            username1: '',
            username2: '',
            password: '',
            role: '',
            status: '',
            error: null,
            isLoading: false,
        };
    },
    methods: {
        async handleRegister() {
            this.error = null;
            this.isLoading = true;

            // Obtén el token de sessionStorage (o localStorage, según lo uses)
            const token = sessionStorage.getItem('token');
            
            if (!token) {
                this.error = 'No estás autenticado. Inicia sesión nuevamente.';
                this.isLoading = false;
                return;
            }

            try {
                // Envía la solicitud de registro junto con el token en el header Authorization
                const response = await axios.post(
                    'http://localhost:8000/api/auth/register',
                    {
                        email: this.email,
                        name: this.name,
                        username1: this.username1,
                        username2: this.username2,
                        password: this.password,
                        role: this.role,
                        status: this.status,  // Incluye el status
                    },
                    {
                        headers: {
                            'Authorization': `Bearer ${token}`, // Incluye el token en el encabezado
                        }
                    }
                );

                // Si el registro fue exitoso, redirigir al usuario
                this.$router.push({ name: 'user' });
                
            } catch (err) {
                this.error = err.response?.data || 'Ha ocurrido un error durante el registro.';
            } finally {
                this.isLoading = false;
            }
        },
    },
};
</script>

<style scoped>

</style>
