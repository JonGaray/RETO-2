<template>
    <div class="col-12">
        <div class="card p-3 mt-3">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4>Gestión de Secciones</h4>
                <button class="btn btn-egibide" @click="showCreateModal = true">+ Nueva Sección</button>
            </div>
            <input v-model="searchQuery" type="text" @input="searchSections" class="form-control mb-3"
                placeholder="Buscar por nombre de seccion" />
            <ul class="list-group">
                <li v-for="(section, index) in sections" :key="index"
                    class="list-group-item d-flex justify-content-between align-items-center flex-column flex-md-row">
                    <div class="text-left text-md-left">
                        <strong class="me-3">{{ section.niu }}</strong>
                        <strong>{{ section.name }}</strong>
                        <span class="text-muted d-block">{{ section.status === 'habilitado' ? 'Activo' : 'Deshabilitado'
                            }}</span>
                    </div>
                    <div class="d-flex align-items-center mt-3 mt-md-0">
                        <label class="switch me-3">
                            <div v-if="section.status === 'habilitado'">
                                <img class="activated" src="../img/boton-de-encendido.png" alt="Habilitado">
                            </div>
                            <div v-else>
                                <img class="desactivated" src="../img/interfaz.png" alt="Deshabilitado">
                            </div>
                            <input type="checkbox" :checked="section.status === 'habilitado'"
                                @change="toggleStatus(section)">
                            <span></span>
                        </label>
                        <button class="btn btn-outline-egibide btn-sm" @click="editSection(section)">
                            <img class="pencil" src="../img/lapiz-de-cejas.png" alt="Editar"> Editar
                        </button>
                    </div>
                </li>
            </ul>
        </div>
    </div>
    <div v-if="showCreateModal" class="modal-backdrop">
        <div class="modal show">
            <h2>Nueva Sección</h2>
            <form @submit.prevent="createSection">
                <div class="form-group">
                    <label class="mt-3">Nombre de Sección</label>
                    <input v-model="newSectionName" type="text" class="form-control" required />
                </div>
                <div class="form-group mb-3 dropdown-wrapper">
                    <label class="mt-3">Campus</label>
                    <div class="dropdown-icon-container">
                        <select v-model="newCampusId" class="form-control" required>
                            <option v-for="campus in campuses" :key="campus.id" :value="campus.id">{{ campus.name }}
                            </option>
                        </select>
                        <i class="fas fa-chevron-down dropdown-icon"></i>
                    </div>
                </div>
                <div class="form-group">
                    <label class="mt-3">NIU</label>
                    <input v-model="newNiu" type="number" class="form-control" placeholder="Introduce el NIU"
                        required />
                </div>
                <div class="d-flex justify-content-between mt-4">
                    <button class="btn btn-egibide" type="submit">Crear Sección</button>
                    <button class="btn btn-secondary" @click="closeModal">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
    <div v-if="showEditModal" class="modal-backdrop">
        <div class="modal show">
            <h2>Editar Sección</h2>
            <form @submit.prevent="saveSectionEdit">
                <div class="form-group">
                    <label class="mt-3">Nombre de Sección</label>
                    <input v-model="editedSectionName" type="text" class="form-control" required />
                </div>
                <div class="form-group mb-3 dropdown-wrapper">
                    <label class="mt-3">Campus</label>
                    <div class="dropdown-icon-container">
                        <select v-model="editedCampusId" class="form-control" required>
                            <option v-for="campus in campuses" :key="campus.id" :value="campus.id">{{ campus.name }}
                            </option>
                        </select>
                        <i class="fas fa-chevron-down dropdown-icon"></i>
                    </div>
                </div>
                <div class="form-group">
                    <label class="mt-3">NIU</label>
                    <input v-model="editedNiu" type="number" class="form-control" required />
                </div>
                <div class="d-flex justify-content-between mt-4">
                    <button class="btn btn-egibide" type="submit">Guardar</button>
                    <button class="btn btn-secondary" @click="closeModal">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2';

export default {
    data() {
        return {
            sections: [],
            campuses: [],
            showCreateModal: false,
            showEditModal: false,
            newSectionName: '',
            editedSectionName: '',
            newCampusId: null,
            editedCampusId: null,
            newNiu: null,
            editedNiu: null,
            editSectionObj: null,
            searchQuery: "",
        };
    },
    created() {
        this.fetchSections();
        this.fetchCampuses();
    },
    methods: {
        searchSections() {
            const token = sessionStorage.getItem("token");
            axios
                .get("http://172.20.228.150:8080/api/auth/sections/search", {
                    params: { query: this.searchQuery },
                    headers: {
                        Authorization: `Bearer ${token}`,
                    },
                })
                .then((response) => {
                    this.sections = response.data;
                })
                .catch((error) => {
                    console.error("Error al buscar secciones:", error);
                });
        },
        async fetchSections() {
            const token = sessionStorage.getItem('token');
            try {
                const response = await axios.get('http://172.20.228.150:8080/api/auth/sections', {
                    headers: {
                        Authorization: `Bearer ${token}`,
                    },
                });
                this.sections = response.data.data;
            } catch (error) {
                console.error('Error al obtener las secciones:', error);
            }
        },
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
        toggleStatus(section) {
            section.status = section.status === 'habilitado' ? 'deshabilitado' : 'habilitado';
            this.updateSectionStatus(section);
        },
        async updateSectionStatus(section) {
            const token = sessionStorage.getItem('token');
            try {
                const response = await axios.put(
                    `http://172.20.228.150:8080/api/auth/sections/${section.id}/status`,
                    { status: section.status },
                    {
                        headers: {
                            Authorization: `Bearer ${token}`,
                        },
                    }
                );
                console.log("Estado de la sección actualizado:", response.data);
            } catch (error) {
                console.error("Error al actualizar el estado de la sección:", error);
            }
        },
        createSection() {
            const token = sessionStorage.getItem('token');
            const sectionData = {
                name: this.newSectionName,
                campus_id: this.newCampusId,
                niu: this.newNiu,
                status: 'habilitado',
            };
            if (sectionData.name.trim().length > 255) {
                Swal.fire({
                    icon: 'error',
                    title: 'Exceso de caracteres',
                    text: 'El nombre no puede superar los 255 caracteres',
                });
            }
            if (sectionData.niu.toString().length > 12) {
                Swal.fire({
                    icon: 'error',
                    title: 'Exceso de caracteres',
                    text: 'El NIU no puede superar los 12 caracteres',
                });
            }
            axios.post('http://172.20.228.150:8080/api/auth/sections/create', sectionData, {
                headers: {
                    Authorization: `Bearer ${token}`,
                },
            })
                .then(response => {
                    this.sections.push(response.data.section);
                    this.closeModal();
                    this.fetchSections();
                })
                .catch(error => {
                    console.error('Error al crear la sección:', error);
                });
        },
        editSection(section) {
            this.editSectionObj = section;
            this.editedSectionName = section.name;
            this.editedCampusId = section.campus_id;
            this.editedNiu = section.niu;
            this.showEditModal = true;
        },
        saveSectionEdit() {
            const token = sessionStorage.getItem('token');
            const sectionData = {
                name: this.editedSectionName,
                campus_id: this.editedCampusId,
                niu: this.editedNiu,
                status: 'habilitado',
            };
            if (sectionData.name.trim().length > 255) {
                Swal.fire({
                    icon: 'error',
                    title: 'Exceso de caracteres',
                    text: 'El nombre no puede superar los 255 caracteres',
                });
            }
            if (sectionData.niu.toString().length > 12) {
                Swal.fire({
                    icon: 'error',
                    title: 'Exceso de caracteres',
                    text: 'El NIU no puede superar los 12 caracteres',
                });
            }
            axios.put(`http://172.20.228.150:8080/api/auth/sections/${this.editSectionObj.id}/edit`, sectionData, {
                headers: {
                    Authorization: `Bearer ${token}`,
                },
            })
                .then(response => {
                    const index = this.sections.findIndex(section => section.id === this.editSectionObj.id);
                    if (index !== -1) {
                        this.sections[index].name = this.editedSectionName;
                        this.sections[index].campus_id = this.editedCampusId;
                        this.sections[index].niu = this.editedNiu;
                    }
                    this.closeModal();
                    this.fetchSections();
                })
                .catch(error => {
                    console.error('Error al editar la sección:', error);
                });
        },
        closeModal() {
            this.showCreateModal = false;
            this.showEditModal = false;
            this.newSectionName = '';
            this.editedSectionName = '';
            this.newCampusId = null;
            this.editedCampusId = null;
            this.newNiu = null;
            this.editedNiu = null;
        },
    },
};
</script>

<style scoped></style>