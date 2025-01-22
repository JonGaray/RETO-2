<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import IncidentCard from '../components/IncidentTecnico.vue';
import TecnicoPanel from '../components/TecnicoPanel.vue';
import Header from '../components/Header.vue';
import InfiniteScrollTecnico from "@/components/InfiniteScrollTecnico.vue";

const incidents = ref([]);
const userId = ref(null);
const currentPage = ref(1);
const totalPages = ref(1);
const fetchIncidents = async (page = 1) => {
    const token = sessionStorage.getItem('token');
    try {
        const response = await axios.get(`http://127.0.0.1:8000/api/auth/incidents/getall?page=${page}`, {
            headers: {
                Authorization: `Bearer ${token}`,
            },
        });
        if (response.data && Array.isArray(response.data.data)) {
            incidents.value = response.data.data;
            totalPages.value = response.data.last_page;
            currentPage.value = page;
        } else {
            console.error('No se encontraron incidencias');
            incidents.value = [];
        }
    } catch (error) {
        console.error('Error al obtener las incidencias:', error);
    }
};
onMounted(() => {
    fetchIncidents();
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
                    <TecnicoPanel :id="userId" />
                </div>
                <div class="col-8">
                    <InfiniteScrollTecnico/>

                </div>
            </div>
        </div>
    </main>
</template>

<style scoped>
</style>
