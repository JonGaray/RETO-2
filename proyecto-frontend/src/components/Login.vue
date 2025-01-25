<template>
    <div class="container">
        <div class="d-flex justify-content-center align-items-center" style="height: 97vh;">
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
                            <button type="submit" class="btn btn-lola w-100" :disabled="isLoading">
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
                    'http://172.20.228.150:8080/api/auth/login',
                    {
                        email: this.email,
                        password: this.password,
                    }
                );
                sessionStorage.setItem('token', response.data.access_token);
                sessionStorage.setItem('user', JSON.stringify(response.data.user));
                const expiresIn = 3600;
                const expirationTime =  Date.now() + expiresIn * 1000;
                sessionStorage.setItem('token_expiration', expirationTime);
                setTimeout(() => {
                    sessionStorage.removeItem('token');
                    sessionStorage.removeItem('token_expiration');
                    sessionStorage.removeItem('user');
                    alert('Tu sesión ha caducado. Por favor, inicia sesión de nuevo.');
                    this.$router.push({ name: 'login' });
                }, expiresIn * 1000);
                const userId = response.data.user.id;
                const userRole = response.data.user.role;
                if (userRole === 'tecnico') {
                    this.$router.push(`/tecnico/${userId}`);
                } else if (userRole === 'admin') {
                    this.$router.push(`/admin/${userId}`);
                } else {
                    this.$router.push(`/user/${userId}`);
                }
            } catch (err) {
                this.error = err.response?.data?.error || 'Ha ocurrido un error.';
            } finally {
                this.isLoading = false;
            }
        },
    },
};
</script>

<style scoped></style>