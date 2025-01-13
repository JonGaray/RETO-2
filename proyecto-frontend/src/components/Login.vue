<template>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h2 class="card-title text-center">Iniciar Sesi&oacute;n</h2>
                        <form @submit.prevent="handleLogin">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input v-model="email" type="email" id="email" class="form-control"
                                    placeholder="Enter your email" required />
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input v-model="password" type="password" id="password" class="form-control"
                                    placeholder="Enter your password" required />
                            </div>
                            <button type="submit" class="btn btn-primary w-100" :disabled="isLoading">
                                Entrar
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
            password: '',
            error: null,
            isLoading: false,
        };
    },
    methods: {
        async handleLogin() {
            this.error = null;
            this.isLoading = true;
            try {
                const response = await axios.post(
                    'http://localhost:8000/api/auth/login',
                    {
                        email: this.email,
                        password: this.password,
                    }
                );
                // Guardar el token en localStorage o en Vuex
                localStorage.setItem('token', response.data.access_token);
                // Redirigir al usuario a la vista del usuario
                this.$router.push({ name: 'user' }); // Redirige a la vista de usuario
                alert('Sesion Iniciada');
            } catch (err) {
                this.error = err.response?.data?.error || 'Ha ocurrido un error.';
            } finally {
                this.isLoading = false;
            }
        },
    },
};
</script>

<style scoped>
.card {
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.card-title {
    font-size: 24px;
    margin-bottom: 20px;
}

button:disabled {
    background-color: #dcdcdc;
    cursor: not-allowed;
}
</style>