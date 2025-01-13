<template>
    <div class="login-container">
        <h2>Login</h2>
        <form @submit.prevent="handleLogin">
            <div class="form-group">
                <label for="email">Email</label>
                <input v-model="email" type="email" id="email" required placeholder="Enter your email" />
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input v-model="password" type="password" id="password" required placeholder="Enter your password" />
            </div>

            <button type="submit" :disabled="isLoading">Login</button>

            <div v-if="error" class="error-message">
                <p>{{ error }}</p>
            </div>
        </form>
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
                const response = await axios.post('http://localhost:8000/api/auth/login', {
                    email: this.email,
                    password: this.password,
                });
                // Guardar el token en localStorage o en Vuex
                localStorage.setItem('token', response.data.access_token);
                // Redirigir al usuario a la página principal o dashboard
                this.$router.push({ name: 'home' }); // Asegúrate de tener una ruta 'home' definida
                alert("Sesion Iniciada");
            } catch (err) {
                this.error = err.response?.data?.error || 'An error occurred during login.';
            } finally {
                this.isLoading = false;
            }
        },
    },
};
</script>

<style scoped>
/* Agrega tus estilos de acuerdo a tus necesidades */
.login-container {
    width: 300px;
    margin: 0 auto;
    padding: 20px;
    background-color: #f9f9f9;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.form-group {
    margin-bottom: 15px;
}

input {
    width: 100%;
    padding: 8px;
    border-radius: 4px;
    border: 1px solid #ccc;
    margin-top: 5px;
}

button {
    width: 100%;
    padding: 10px;
    background-color: #4caf50;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

button:disabled {
    background-color: #dcdcdc;
    cursor: not-allowed;
}

.error-message {
    color: red;
    margin-top: 10px;
}
</style>