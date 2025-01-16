<template>
    <div class="col-md-12 mt-5">
        <div class="custom-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4>Gestión de Campus</h4>
                <button class="btn btn-egibide">+ Nuevo Campus</button>
            </div>
            <ul class="list-group">
                <li v-for="(campus, index) in campuses" :key="index"
                    class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <strong>{{ campus.name }} </strong>
                        <span class="text-muted d-block">{{ campus.status === 'habilitado' ? 'Activo' : 'Deshabilitado'
                            }}</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <label class="switch me-3">
                            <input type="checkbox" :checked="campus.status === 'habilitado'"
                                @change="toggleStatus(campus)">
                            <span></span>
                        </label>
                        <button class="btn btn-outline-egibide btn-sm">Editar</button>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import { useRouter } from 'vue-router';

export default {
    data() {
        return {
            campuses: [],
        };
    },
    created() {
        this.fetchCampuses();
    },
    methods: {
        async fetchCampuses() {
            const token = sessionStorage.getItem('token');
            try {
                const response = await axios.get('http://127.0.0.1:8000/api/auth/campuses', {
                    headers: {
                        Authorization: `Bearer ${token}`,
                    },
                });
                this.campuses = response.data;
            } catch (error) {
                console.error('Error al obtener los campus:', error);
            }
        },

        toggleStatus(campus) {
            // Cambiar el estado entre 'habilitado' y 'deshabilitado'
            campus.status = campus.status === 'habilitado' ? 'deshabilitado' : 'habilitado';
            this.updateCampusStatus(campus);
        },

        async updateCampusStatus(campus) {
            const token = sessionStorage.getItem('token');
            try {
                // Hacer la petición PUT a la API para actualizar el estado del campus
                const response = await axios.put(
                    `http://127.0.0.1:8000/api/auth/campuses/${campus.id}/status`,  // Actualización de la URL según la ruta de tu API
                    { status: campus.status },  // Pasamos el nuevo estado del campus
                    {
                        headers: {
                            Authorization: `Bearer ${token}`,
                        },
                    }
                );

                // Si la respuesta es exitosa, actualizar el campus en la lista
                if (response.data && response.data.campus) {
                    console.log("Estado del campus actualizado:", response.data.campus);
                }
            } catch (error) {
                console.error("Error al actualizar el estado del campus:", error);
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

.switch input:checked+span {
    background-color: #790253;
}

.switch input:checked+span::before {
    transform: translateX(20px);
}
</style>