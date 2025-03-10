<template>
    <div class="col-12">
        <div class="card p-3 mt-3">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="w-100 text-center text-md-start mb-3 mb-md-0">Gestión de Campus</h4>
                <button class="btn btn-egibide mt-3 mt-md-0 text-nowrap" @click="showCreateModal = true">+ Nuevo Campus</button>
            </div>
            <ul class="list-group">
                <li v-for="(campus, index) in campuses" :key="index"
                    class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <strong>{{ campus.name }}</strong>
                        <span class="text-muted d-block">{{ campus.status === 'habilitado' ? 'Activo' : 'Deshabilitado' }}</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <label class="switch me-3">
                            <div v-if="campus.status === 'habilitado'">
                                <img class="activated" src="../img/boton-de-encendido.png">
                            </div>
                            <div v-else>
                                <img class="desactivated" src="../img/interfaz.png">
                            </div>
                            <input type="checkbox" :checked="campus.status === 'habilitado'"
                                @change="toggleStatus(campus)">
                            <span></span>
                        </label>
                        <button class="btn btn-outline-egibide btn-sm" @click="editCampus(campus)">
                            <img class="pencil" src="../img/lapiz-de-cejas.png"> Editar
                        </button>
                    </div>
                </li>
            </ul>
        </div>
        <div v-if="showCreateModal" class="modal-backdrop">
            <div class="modal show">
                <h2>Nuevo Campus</h2>
                <label for="campusName" class="mt-2">Nombre del Campus</label>
                <input v-model="newCampusName" type="text" class="form-control mt-3">
                <div class="d-flex justify-content-between mt-4">
                    <button type="button" class="btn btn-egibide" @click="createCampus">Crear Campus</button>
                    <button type="button" class="btn btn-secondary" @click="closeModal">Cancelar</button>
                </div>
            </div>
        </div>
        <div v-if="showEditModal" class="modal-backdrop">
            <div class="modal show">
                <h2>Editar Campus</h2>
                <label for="editCampusName" class="mt-2">Nombre del Campus</label>
                <input v-model="editedCampusName" type="text" class="form-control mt-3" :placeholder="editCampusObj.name">
                <div class="d-flex justify-content-between mt-4">
                    <button type="button" class="btn btn-egibide" @click="saveCampusEdit">Guardar</button>
                    <button type="button" class="btn btn-secondary" @click="closeModal">Cancelar</button>
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
            campuses: [],
            showCreateModal: false,
            showEditModal: false,
            newCampusName: '',
            editedCampusName: '',
            editCampusObj: null,
        };
    },
    created() {
        this.fetchCampuses();
    },
    methods: {
        async fetchCampuses() {
            const token = sessionStorage.getItem('token');
            try {
                const response = await axios.get('http://172.20.228.150:8080/api/auth/campuses', {
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
            campus.status = campus.status === 'habilitado' ? 'deshabilitado' : 'habilitado';
            this.updateCampusStatus(campus);
        },
        async updateCampusStatus(campus) {
            const token = sessionStorage.getItem('token');
            try {
                const response = await axios.put(
                    `http://172.20.228.150:8080/api/auth/campuses/${campus.id}/status`,
                    { status: campus.status },
                    {
                        headers: {
                            Authorization: `Bearer ${token}`,
                        },
                    }
                );
                console.log("Estado del campus actualizado:", response.data);
            } catch (error) {
                console.error("Error al actualizar el estado del campus:", error);
            }
        },
        createCampus() {
            const token = sessionStorage.getItem('token');
            const campusData = {
                name: this.newCampusName,
            };
            if (campusData.name.length > 255) {
                Swal.fire({
                    icon: 'error',
                    title: 'Exceso de caracteres',
                    text: 'El nombre no puede superar los 255 caracteres',
                });
            }
            axios.post('http://172.20.228.150:8080/api/auth/campuses/create', campusData, {
                headers: {
                    Authorization: `Bearer ${token}`,
                },
            })
                .then(response => {
                    this.campuses.push(response.data.campus);
                    this.closeModal();
                    this.fetchCampuses();
                })
                .catch(error => {
                    console.error('Error al crear el campus:', error);
                });
        },
        editCampus(campus) {
            this.editCampusObj = campus;
            this.editedCampusName = campus.name;
            this.showEditModal = true;
        },
        saveCampusEdit() {
            const token = sessionStorage.getItem('token');
            const campusData = {
                name: this.editedCampusName,
            };
            if (campusData.name.length > 255) {
                Swal.fire({
                    icon: 'error',
                    title: 'Exceso de caracteres',
                    text: 'El nombre no puede superar los 255 caracteres',
                });
            }
            axios.put(`http://172.20.228.150:8080/api/auth/campuses/${this.editCampusObj.id}/edit`, campusData, {
                headers: {
                    Authorization: `Bearer ${token}`,
                },
            })
                .then(response => {
                    const index = this.campuses.findIndex(campus => campus.id === this.editCampusObj.id);
                    if (index !== -1) {
                        this.campuses[index].name = this.editedCampusName;
                    }
                    this.closeModal();
                })
                .catch(error => {
                    console.error('Error al editar el campus:', error);
                });
        },
        closeModal() {
            this.showCreateModal = false;
            this.showEditModal = false;
            this.newCampusName = '';
            this.editedCampusName = '';
        },
    },
};
</script>

<style scoped></style>