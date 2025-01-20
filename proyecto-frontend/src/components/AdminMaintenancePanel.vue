<template>
    <div class="col-md-12 mt-5">
        <div class="custom-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4>Gestión de Mantenimientos</h4>
                <div>
                    <button class="btn btn-egibide me-3" @click="showCreateMaintenanceModal = true">+ Nuevo Mantenimiento</button>
                    <button class="btn btn-egibide ml-2" @click="showCreateAssociationModal = true">+ Nueva Asociación</button>
                </div>
            </div>

            <h5 class="mt-4">Asociaciones Existentes</h5>
            <ul class="list-group">
                <li v-for="(machinemaintenance, index) in machinemaintenances" :key="index"
                    class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <strong>{{ machinemaintenance.machine_name }} - {{ machinemaintenance.maintenance_name }} (Cada {{ machinemaintenance.regularity }} días)</strong>
                    </div>
                </li>
            </ul>
        </div>
    </div>

    <!-- Modal para Crear Mantenimiento -->
    <div v-if="showCreateMaintenanceModal" class="modal-backdrop">
        <div class="modal show">
            <h2>Nuevo Mantenimiento</h2>
            <input v-model="newMaintenanceName" type="text" class="form-control mt-2" placeholder="Nombre del mantenimiento">
            <input v-model="newMaintenanceRegularity" type="number" class="form-control mt-2" placeholder="Regularidad en días">
            <div class="d-flex justify-content-between mt-4">
                <button type="button" class="btn btn-outline-egibide" @click="closeModal">Cancelar</button>
                <button type="button" class="btn btn-egibide" @click="createMaintenance">Guardar</button>
            </div>
        </div>
    </div>

    <!-- Modal para Crear Asociación Máquina-Mantenimiento -->
    <div v-if="showCreateAssociationModal" class="modal-backdrop">
        <div class="modal show">
            <h2>Nueva Asociación Máquina-Mantenimiento</h2>
            <label>Máquina</label>
            <select v-model="selectedMachineId" class="form-control mt-2">
                <option v-for="machine in machines" :key="machine.id" :value="machine.id">{{ machine.name }}</option>
            </select>
            <label>Mantenimiento</label>
            <select v-model="selectedMaintenanceId" class="form-control mt-2">
                <option v-for="maintenance in maintenances" :key="maintenance.id" :value="maintenance.id">{{ maintenance.name }}</option>
            </select>
            <div class="d-flex justify-content-between mt-4">
                <button type="button" class="btn btn-outline-egibide" @click="closeModal">Cancelar</button>
                <button type="button" class="btn btn-egibide" @click="createAssociation">Guardar</button>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            machinemaintenances: [],
            machines: [],
            maintenances: [],
            showCreateMaintenanceModal: false,
            showCreateAssociationModal: false,
            newMaintenanceName: '',
            newMaintenanceRegularity: '',
            selectedMachineId: null,
            selectedMaintenanceId: null,
        };
    },
    created() {
        this.fetchMachineMaintenances();
        this.fetchMachines();
        this.fetchMaintenances();
    },
    methods: {
        async fetchMachineMaintenances() {
            const token = sessionStorage.getItem('token');
            try {
                const response = await axios.post('http://127.0.0.1:8000/api/auth/machinemaintenances', {}, {
                    headers: {
                        Authorization: `Bearer ${token}`,
                    },
                });
                this.machinemaintenances = response.data;
            } catch (error) {
                console.error('Error al obtener las asociaciones:', error);
            }
        },
        async fetchMachines() {
            const token = sessionStorage.getItem('token');
            try {
                const response = await axios.get('http://127.0.0.1:8000/api/auth/machines', {
                    headers: {
                        Authorization: `Bearer ${token}`,
                    },
                });
                this.machines = response.data;
            } catch (error) {
                console.error('Error al obtener las máquinas:', error);
            }
        },
        async fetchMaintenances() {
            const token = sessionStorage.getItem('token');
            try {
                const response = await axios.post('http://127.0.0.1:8000/api/auth/maintenances', {}, {
                    headers: {
                        Authorization: `Bearer ${token}`,
                    },
                });
                this.maintenances = response.data;
            } catch (error) {
                console.error('Error al obtener los mantenimientos:', error);
            }
        },

        createMaintenance() {
            const token = sessionStorage.getItem('token');
            const maintenanceData = {
                name: this.newMaintenanceName,
                regularity: this.newMaintenanceRegularity,
            };
            axios.post('http://127.0.0.1:8000/api/auth/maintenances/create', maintenanceData, {
                headers: {
                    Authorization: `Bearer ${token}`,
                },
            })
            .then(response => {
                this.maintenances.push(response.data.maintenance);
                this.closeModal();
                this.fetchMachineMaintenances();
                this.fetchMachines();
                this.fetchMaintenances();
            })
            .catch(error => {
                console.error('Error al crear el mantenimiento:', error);
            });
        },

        createAssociation() {
            const token = sessionStorage.getItem('token');
            const associationData = {
                machines_id: this.selectedMachineId,
                maintenances_id: this.selectedMaintenanceId,
            };
            axios.post('http://127.0.0.1:8000/api/auth/machinemaintenances/create', associationData, {
                headers: {
                    Authorization: `Bearer ${token}`,
                },
            })
            .then(response => {
                this.machinemaintenances.push(response.data.asociacion);
                this.closeModal();
                this.fetchMachineMaintenances();
                this.fetchMachines();
                this.fetchMaintenances();
            })
            .catch(error => {
                console.error('Error al crear la asociación:', error);
            });
        },

        closeModal() {
            this.showCreateMaintenanceModal = false;
            this.showCreateAssociationModal = false;
            this.newMaintenanceName = '';
            this.newMaintenanceRegularity = '';
            this.selectedMachineId = null;
            this.selectedMaintenanceId = null;
        },
    },
};
</script>

<style scoped>
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
</style>
