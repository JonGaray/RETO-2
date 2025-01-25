<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import TecnicoPanel from '../components/TecnicoPanel.vue';
import Header from '../components/Header.vue';
import InfiniteScrollTecnico from "@/components/InfiniteScrollTecnico.vue";
import Swal from 'sweetalert2';

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
const checkTokenExpiration = () => {
    const tokenExpiration = sessionStorage.getItem('token_expiration');
    if (tokenExpiration) {
        const expirationTime = parseInt(tokenExpiration);
        const currentTime = Date.now();
        const timeRemaining = expirationTime - currentTime;
        if (timeRemaining <= 0) {
            sessionStorage.removeItem('token');
            sessionStorage.removeItem('token_expiration');
            sessionStorage.removeItem('user');
            Swal.fire({
                icon: 'error',
                title: 'Sesión caducada',
                text: 'Inicie sesión de nuevo para continuar',
            }); this.$router.push({ name: 'login' });
        }
    } else {
        this.$router.push({ name: 'login' });
    }
};
onMounted(() => {
    checkTokenExpiration();
});
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

<style scoped></style>
