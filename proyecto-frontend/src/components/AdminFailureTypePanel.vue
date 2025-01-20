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
                        <button class="btn btn-outline-egibide btn-sm" @click="editFailuretype(failuretype)"><img class="pencil" src="../img/lapiz-de-cejas.png">Editar</button>
                    </div>
                </li>
            </ul>
        </div>
    </div>

    <!-- Modal para Crear Tipo de Fallo -->
    <div v-if="showCreateModal" class="modal-backdrop">
        <div class="modal show">
            <h2>Nuevo Tipo de Fallo</h2>
            <input v-model="newFailuretypeName" type="text" class="form-control mt-5" placeholder="Nombre del tipo de fallo">
            <div class="d-flex justify-content-between mt-5">
                <button type="button" class="btn btn-outline-egibide" @click="closeModal">Cancelar</button>
                <button type="button" class="btn btn-egibide" @click="createFailuretype">Guardar</button>
            </div>
        </div>
    </div>

    <!-- Modal para Editar Tipo de Fallo -->
    <div v-if="showEditModal" class="modal-backdrop">
        <div class="modal show">
            <h2>Editar Tipo de Fallo</h2>
            <input v-model="editedFailuretypeName" type="text" class="form-control mt-5" :placeholder="editFailuretypeObj.name">
            <div class="d-flex justify-content-between mt-5">
                <button type="button" class="btn btn-outline-egibide" @click="closeModal">Cancelar</button>
                <button type="button" class="btn btn-egibide" @click="saveFailuretypeEdit">Guardar</button>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

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
            axios.post('http://127.0.0.1:8000/api/auth/failuretypes/create', failuretypeData, {
                headers: {
                    Authorization: `Bearer ${token}`,
                },
            })
            .then(response => {
                this.failuretypes.push(response.data.failuretype);
                this.closeModal();
                this.fetchFailuretypes(); //Recargar la select
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

<style scoped>
/* Estilos del modal */
.modal-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1050;
}

.modal {
    background-color: white;
    padding: 20px;
    border-radius: 10px;
    width: 500px;
    z-index: 1060;
    display: block;
}

/* Iconos */
.pencil{
  width: 20px;
  height: 20px;
  margin-right: 5px;
}

.activated{
  width: 25px;
  height: 25px;
  margin-right: 7px;
}

.desactivated{
  width: 30px;
  height: 30px;
  margin-right: 5px;
}
</style>
