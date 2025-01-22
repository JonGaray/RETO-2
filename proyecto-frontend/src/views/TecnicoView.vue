<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import TecnicoPanel from '../components/TecnicoPanel.vue';
import Header from '../components/Header.vue';
import InfiniteScrollTecnico from "@/components/InfiniteScrollTecnico.vue";

// Variables reactivas
const incidents = ref([]);
const userId = ref(null);
const currentPage = ref(1);
const totalPages = ref(1);
const selectedFilter = ref(''); // Filtro seleccionado
const selectedFilterType = ref(''); // Tipo de filtro seleccionado

// Función para manejar el cambio de filtros desde el componente hijo
const onFiltersChanged = (filters) => {
    if (filters.selectedCampus !== undefined) {
        selectedFilter.value = filters.selectedCampus;
        selectedFilterType.value = 'campus';
    } else if (filters.selectedSection !== undefined) {
        selectedFilter.value = filters.selectedSection;
        selectedFilterType.value = 'section';
    } else if (filters.selectedFailureType !== undefined) {
        selectedFilter.value = filters.selectedFailureType;
        selectedFilterType.value = 'failureType';
    } else if (filters.selectedImportance !== undefined) {
        selectedFilter.value = filters.selectedImportance;
        selectedFilterType.value = 'importance';
    }

    // Aquí llamamos al componente `InfiniteScrollTecnico` para actualizar la lista
    // usando el filtro seleccionado
    fetchIncidentsWithFilters();
};

// Método para obtener incidencias con el filtro seleccionado
const fetchIncidentsWithFilters = () => {
    const infiniteScrollTecnico = document.querySelector('InfiniteScrollTecnico');
    if (infiniteScrollTecnico) {
        infiniteScrollTecnico.fetchIncidents(selectedFilterType.value, selectedFilter.value);
    }
};
</script>

<template>
    <main>
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <Header />
                </div>
                <div class="col-4">
                    <!-- Conectar el evento del filtro -->
                    <TecnicoPanel :id="userId" @filtersChanged="onFiltersChanged" />
                </div>
                <div class="col-8">
                    <InfiniteScrollTecnico :filterType="selectedFilterType" :filterValue="selectedFilter" />
                </div>
            </div>
        </div>
    </main>
</template>

<style scoped>
.container {
    margin-top: 20px;
}
</style>
