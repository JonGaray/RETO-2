<script setup>
import Header from '../components/Header.vue';
import TecnicoPanel from '@/components/TecnicoPanel.vue';
import IncidenTecnico from '@/components/IncidenTecnico.vue';
</script>

<template>
    <main>
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <Header />
                </div>
                <div class="col-4">
                    <!-- Aquí está tu panel de usuario -->
                    <TecnicoPanel :id="userId" />
                </div>
                <div class="col-8">
                    <!-- Generamos tantas tarjetas como incidencias existan -->
                    <div v-for="incident in incidents" :key="incident.id">
                        <IncidenTecnico :title="incident.title" :description="incident.description"
                            :category="incident.importance" :type="String(incident.failuretypes_id)"
                            :machines_id="String(incident.machines_id)" :status="incident.status"
                            :date="incident.created_at" :machine_name="incident.machine_name"
                            :failure_type_name="incident.failure_type_name" />
                    </div>
                </div>
            </div>
        </div>
    </main>
</template>

<style scoped></style>
