<template>
    <div class="col-md-12 mt-5">
        <div class="custom-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4>Gestión de Secciones</h4>
                <button class="btn btn-egibide" @click="showCreateModal = true">+ Nueva Sección</button>
            </div>
            <ul class="list-group">
                <li v-for="(section, index) in sections" :key="index"
                    class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <strong>{{ section.name }} </strong>
                        <span class="text-muted d-block">{{ section.status === 'habilitado' ? 'Activo' : 'Deshabilitado' }}</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <label class="switch me-3">
                            <input type="checkbox" :checked="section.status === 'habilitado'" @change="toggleStatus(section)">
                            <span></span>
                        </label>
                        <button class="btn btn-outline-egibide btn-sm" @click="editSection(section)">Editar</button>
                    </div>
                </li>
            </ul>
        </div>
    </div>

    <!-- Modal para Crear Sección -->
    <div v-if="showCreateModal" class="modal-backdrop">
        <div class="modal show">
            <h2>Nueva Sección</h2>
            <input v-model="newSectionName" type="text" class="form-control mt-5" placeholder="Nombre de la sección">
            <label for="campusSelect" class="form-label mt-3">Campus:</label>
            <select v-model="newCampusId" class="form-control mt-3">
                <option v-for="campus in campuses" :key="campus.id" :value="campus.id">
                    {{ campus.name }}
                </option>
            </select>
            <div class="d-flex justify-content-between mt-5">
                <button type="button" class="btn btn-outline-egibide" @click="closeModal">Cancelar</button>
                <button type="button" class="btn btn-egibide" @click="createSection">Guardar</button>
            </div>
        </div>
    </div>

    <!-- Modal para Editar Sección -->
    <div v-if="showEditModal" class="modal-backdrop">
        <div class="modal show">
            <h2>Editar Sección</h2>
            <input v-model="editedSectionName" type="text" class="form-control mt-5" :placeholder="editSectionObj.name">
            <label for="campusSelect" class="form-label mt-3">Campus:</label>
            <select v-model="editedCampusId" class="form-control mt-3">
                <option v-for="campus in campuses" :key="campus.id" :value="campus.id">
                    {{ campus.name }}
                </option>
            </select>
            <div class="d-flex justify-content-between mt-5">
                <button type="button" class="btn btn-outline-egibide" @click="closeModal">Cancelar</button>
                <button type="button" class="btn btn-egibide" @click="saveSectionEdit">Guardar</button>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

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
            editSectionObj: null,
        };
    },
    created() {
        this.fetchSections();
        this.fetchCampuses();
    },
    methods: {
        async fetchSections() {
            const token = sessionStorage.getItem('token');
            try {
                const response = await axios.get('http://127.0.0.1:8000/api/auth/sections', {
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

        toggleStatus(section) {
            section.status = section.status === 'habilitado' ? 'deshabilitado' : 'habilitado';
            this.updateSectionStatus(section);
        },

        async updateSectionStatus(section) {
            const token = sessionStorage.getItem('token');
            try {
                const response = await axios.put(
                    `http://127.0.0.1:8000/api/auth/sections/${section.id}/status`,  
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
                status: 'habilitado',
            };
            axios.post('http://127.0.0.1:8000/api/auth/sections/create', sectionData, {
                headers: {
                    Authorization: `Bearer ${token}`,
                },
            })
            .then(response => {
                this.sections.push(response.data.section);
                this.closeModal();
            })
            .catch(error => {
                console.error('Error al crear la sección:', error);
            });
        },

        editSection(section) {
            this.editSectionObj = section;
            this.editedSectionName = section.name;
            this.editedCampusId = section.campus_id;
            this.showEditModal = true;
        },

        saveSectionEdit() {
            const token = sessionStorage.getItem('token');
            const sectionData = {
                name: this.editedSectionName,
                campus_id: this.editedCampusId,
                status: 'habilitado',
            };
            axios.put(`http://127.0.0.1:8000/api/auth/sections/${this.editSectionObj.id}/edit`, sectionData, {
                headers: {
                    Authorization: `Bearer ${token}`,
                },
            })
            .then(response => {
                const index = this.sections.findIndex(section => section.id === this.editSectionObj.id);
                if (index !== -1) {
                    this.sections[index].name = this.editedSectionName;
                    this.sections[index].campus_id = this.editedCampusId;
                }
                this.closeModal();
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
        },
    },
};
</script>

<style scoped>
/* Estilos del modal */

</style>
