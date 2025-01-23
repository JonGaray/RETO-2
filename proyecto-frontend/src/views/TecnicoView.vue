<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import TecnicoPanel from '../components/TecnicoPanel.vue';
import Header from '../components/Header.vue';
import InfiniteScrollTecnico from "@/components/InfiniteScrollTecnico.vue";

const incidents = ref([]);
const userId = ref(null);
const currentPage = ref(1);
const totalPages = ref(1);
const selectedFilter = ref('');
const selectedFilterType = ref('');

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
    fetchIncidentsWithFilters();
};

const fetchIncidentsWithFilters = () => {
    const infiniteScrollTecnico = document.querySelector('InfiniteScrollTecnico');
    if (infiniteScrollTecnico) {
        infiniteScrollTecnico.fetchIncidents(selectedFilterType.value, selectedFilter.value);
    }
};
</script>
<template>
    <main>
        <div class="container py-4">
            <div class="row">
                <div class="col-12">
                    <Header />
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12">
                    <TecnicoPanel :id="userId" @filtersChanged="onFiltersChanged" />
                </div>
                <div class="col-lg-8 col-md-6 col-sm-12">
                    <InfiniteScrollTecnico :filterType="selectedFilterType" :filterValue="selectedFilter" />
                </div>
            </div>
        </div>
    </main>
</template>

<style scoped>
</style>
