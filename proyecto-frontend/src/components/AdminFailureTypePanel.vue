<template>
    <div class="col-md-12 mt-5">
        <div class="custom-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4>Gestión de Tipos de Fallo</h4>
                <button class="btn btn-egibide" @click="showCreateModal = true">+ Nuevo Tipo de Fallo</button>
            </div>
            <ul class="list-group">
                <li v-for="(failuretype, index) in failuretypes" :key="index"
                    class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <strong>{{ failuretype.name }} </strong>
                    </div>
                    <div class="d-flex align-items-center">
                        <button class="btn btn-outline-egibide btn-sm" @click="editFailuretype(failuretype)"><img
                                class="pencil" src="../img/lapiz-de-cejas.png">Editar</button>
                    </div>
                </li>
            </ul>
        </div>
    </div>
    <div v-if="showCreateModal" class="modal-backdrop">
        <div class="modal show">
            <h2>Nuevo Tipo de Fallo</h2>
            <label class="mt-5">Nombre del Tipo de Fallo</label>
            <input v-model="newFailuretypeName" type="text" class="form-control">
            <div class="d-flex justify-content-between mt-5">
                <button type="button" class="btn btn-egibide" @click="createFailuretype">Crear Tipo de Fallo</button>
                <button type="button" class="btn btn-secondary" @click="closeModal">Cancelar</button>
            </div>
        </div>
    </div>
    <div v-if="showEditModal" class="modal-backdrop">
        <div class="modal show">
            <h2>Editar Tipo de Fallo</h2>
            <label class="mt-5">Nombre del Tipo de Fallo</label>
            <input v-model="editedFailuretypeName" type="text" class="form-control"
                :placeholder="editFailuretypeObj.name">
            <div class="d-flex justify-content-between mt-5">
                <button type="button" class="btn btn-egibide" @click="saveFailuretypeEdit">Guardar</button>
                <button type="button" class="btn btn-secondary" @click="closeModal">Cancelar</button>
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
            failuretypes: [],
            showCreateModal: false,
            showEditModal: false,
            newFailuretypeName: '',
            editedFailuretypeName: '',
            editFailuretypeObj: null,
        };
    },
    created() {
        this.fetchFailuretypes();
    },
    methods: {
        async fetchFailuretypes() {
            const token = sessionStorage.getItem('token');
            try {
                const response = await axios.get('http://127.0.0.1:8000/api/auth/failuretypes', {
                    headers: {
                        Authorization: `Bearer ${token}`,
                    },
                });
                this.failuretypes = response.data.data;
            } catch (error) {
                console.error('Error al obtener los tipos de fallo:', error);
            }
        },
        createFailuretype() {
            const token = sessionStorage.getItem('token');
            const failuretypeData = {
                name: this.newFailuretypeName,
            };
            if (failuretypeData.name.trim().length > 255) {
                Swal.fire({
                    icon: 'error',
                    title: 'Exceso de caracteres',
                    text: 'El nombre no puede superar los 255 caracteres',
                });
                return;
            }
            axios.post('http://127.0.0.1:8000/api/auth/failuretypes/create', failuretypeData, {
                headers: {
                    Authorization: `Bearer ${token}`,
                },
            })
                .then(response => {
                    this.failuretypes.push(response.data.failuretype);
                    this.closeModal();
                    this.fetchFailuretypes();
                })
                .catch(error => {
                    console.error('Error al crear el tipo de fallo:', error);
                });
        },
        editFailuretype(failuretype) {
            this.editFailuretypeObj = failuretype;
            this.editedFailuretypeName = failuretype.name;
            this.showEditModal = true;
        },
        saveFailuretypeEdit() {
            const token = sessionStorage.getItem('token');
            const failuretypeData = {
                name: this.editedFailuretypeName,
            };
            if (failuretypeData.name.trim().length > 255) {
                Swal.fire({
                    icon: 'error',
                    title: 'Exceso de caracteres',
                    text: 'El nombre no puede superar los 255 caracteres',
                });
                return;
            }
            axios.put(`http://127.0.0.1:8000/api/auth/failuretypes/${this.editFailuretypeObj.id}/edit`, failuretypeData, {
                headers: {
                    Authorization: `Bearer ${token}`,
                },
            })
                .then(response => {
                    const index = this.failuretypes.findIndex(failuretype => failuretype.id === this.editFailuretypeObj.id);
                    if (index !== -1) {
                        this.failuretypes[index].name = this.editedFailuretypeName;
                    }
                    this.closeModal();
                    this.fetchFailuretypes();
                })
                .catch(error => {
                    console.error('Error al editar el tipo de fallo:', error);
                });
        },
        closeModal() {
            this.showCreateModal = false;
            this.showEditModal = false;
            this.newFailuretypeName = '';
            this.editedFailuretypeName = '';
        },
    },
};
</script>

<style scoped></style>