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
        selectedFilterType.value = 'failuretype';
    } else if (filters.selectedImportance !== undefined) {
        selectedFilter.value = filters.selectedImportance;
        selectedFilterType.value = 'importance';
    } else if (filters.selectedStatus !== undefined) {
        selectedFilter.value = filters.selectedStatus;
        selectedFilterType.value = 'status';
    } else if (filters.selectedMachine !== undefined) {
        selectedFilter.value = filters.selectedMachine;
        selectedFilterType.value = 'machine';
    }
    fetchIncidentsWithFilters();
};

const fetchIncidentsWithFilters = () => {
    if (InfiniteScrollTecnico.value) {
        InfiniteScrollTecnico.value.fetchIncidents(selectedFilterType.value, selectedFilter.value);
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
                    <TecnicoPanel :id="userId" @filtersChanged="onFiltersChanged" />
                </div>
                <div class="col-8">
                    <InfiniteScrollTecnico ref="InfiniteScroll" :filterType="selectedFilterType"
                        :filterValue="selectedFilter" />
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
