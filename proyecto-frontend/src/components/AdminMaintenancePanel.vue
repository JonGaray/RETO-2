<template>
    <div class="col-12">
        <div class="card p-3 mt-3">
            <div class="mb-4">
                <h4>Gestión de Mantenimientos</h4>
                <div class="d-flex align-items-center">
                <button class="btn btn-egibide mt-3 mt-md-0" @click="showCreateMaintenanceModal = true">+ Nuevo Mantenimiento</button>
                <button class="btn btn-egibide mt-3 mt-md-0" @click="showCreateAssociationModal = true">+ Nueva Asociación</button>
                </div>
            </div>
            <h5 class="mt-4">Asociaciones Existentes</h5>
            <ul class="list-group" v-if="machinemaintenances.length > 0">
                <li v-for="(machinemaintenance, index) in machinemaintenances" :key="index"
                    class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <strong>
                            {{ machinemaintenance.machine_name || 'Nombre de máquina no disponible' }} -
                            {{ machinemaintenance.maintenance_name || 'Nombre de mantenimiento no disponible' }}
                            (Cada {{ machinemaintenance.regularity || 'Regularidad no disponible' }} días)
                        </strong>
                    </div>
                </li>
            </ul>
            <div v-else>
                <p>Cargando...</p>
            </div>
        </div>
        <div v-if="showCreateMaintenanceModal" class="modal-backdrop">
            <div class="modal show">
                <h2>Nuevo Mantenimiento</h2>
                <label class="mt-5">Nombre del Mantenimiento</label>
                <input v-model="newMaintenanceName" type="text" class="form-control mt-2"
                    placeholder="Nombre del mantenimiento">
                <label class="mt-3">Regularidad</label>
                <input v-model="newMaintenanceRegularity" type="number" class="form-control mt-2"
                    placeholder="Regularidad en días">
                <div class="d-flex justify-content-between mt-4">
                    <button type="button" class="btn btn-egibide" @click="createMaintenance">Crear
                        Mantenimiento</button>
                    <button type="button" class="btn btn-secondary" @click="closeModal">Cancelar</button>
                </div>
            </div>
        </div>
        <div v-if="showCreateAssociationModal" class="modal-backdrop">
            <div class="modal show">
                <h2>Nueva Asociación Máquina-Mantenimiento</h2>
                <div class="mb-3 dropdown-wrapper">
                    <label class="mt-5">Máquina</label>
                    <div class="dropdown-icon-container">
                        <select v-model="selectedMachineId" class="form-control mt-2">
                            <option v-for="machine in machines" :key="machine.id" :value="machine.id">{{ machine.name }}
                            </option>
                        </select>
                        <i class="fas fa-chevron-down dropdown-icon"></i>
                    </div>
                </div>
                <div class="mb-3 dropdown-wrapper">
                    <label>Mantenimiento</label>
                    <div class="dropdown-icon-container">
                        <select v-model="selectedMaintenanceId" class="form-control mt-2">
                            <option v-for="maintenance in maintenances" :key="maintenance.id" :value="maintenance.id">{{
                                maintenance.name }}</option>
                        </select>
                        <i class="fas fa-chevron-down dropdown-icon"></i>
                    </div>
                </div>
                <div class="d-flex justify-content-between mt-4">
                    <button type="button" class="btn btn-egibide" @click="createAssociation">Crear Asociación</button>
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
                this.machinemaintenances = [];
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
            if (maintenanceData.name.trim().length > 255) {
                Swal.fire({
                    icon: 'error',
                    title: 'Exceso de caracteres',
                    text: 'El nombre no puede superar los 255 caracteres',
                });
                return;
            }
            if (maintenanceData.regularity.toString().length > 12) {
                Swal.fire({
                    icon: 'error',
                    title: 'Exceso de caracteres',
                    text: 'La regularidad no puede superar los 12 caracteres',
                });
                return;
            }
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
                    if (response.data && response.data.data) {
                        this.machinemaintenances.push(response.data.data);
                    } else {
                        console.error('La respuesta no contiene la propiedad "data" con la asociación.');
                    }
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

<style scoped></style>
