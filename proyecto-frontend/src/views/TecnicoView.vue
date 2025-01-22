<script setup>
import {ref, onMounted} from 'vue';
import axios from 'axios';
import IncidentCard from '../components/IncidentTecnico.vue';
import TecnicoPanel from '../components/TecnicoPanel.vue';
import Header from '../components/Header.vue';
import InfiniteScrollTecnico from "@/components/InfiniteScrollTecnico.vue";

// Variables reactivas
const incidents = ref([]); // Inicializa la lista de incidencias
const userId = ref(null); // Inicializa userId con null
const currentPage = ref(1); // Página actual
const totalPages = ref(1); // Total de páginas
const selectedFilter = ref(''); // Filtro seleccionado

// Función para obtener las incidencias con paginación y filtro
const fetchIncidents = async (filterType, page = 1) => {
  const token = sessionStorage.getItem('token'); // Obtén el token desde sessionStorage
  try {
    let response;

    // Dependiendo del filtro, hacemos una llamada diferente
    if (filterType  === 'importance') {
      response = await axios.get(`http://127.0.0.1:8000/api/auth/incidents/${selectedFilter.value}/importance?page=${page}`, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });
  } else if (filterType  === 'campus') {
    response = await axios.get(`http://127.0.0.1:8000/api/auth/incidents/${selectedFilter.value}/campus?page=${page}`, {
    headers: {
      Authorization: `Bearer ${token}`,
    },
  });
} else if (filterType  === 'section') {
  response = await axios.get(`http://127.0.0.1:8000/api/auth/incidents/${selectedFilter.value}/section?page=${page}`, {
  headers: {
    Authorization: `Bearer ${token}`,
  },
});
} else if (filterType  === 'failureType') {
  response = await axios.get(`http://127.0.0.1:8000/api/auth/incidents/${selectedFilter.value}/failuretype?page=${page}`, {
  headers: {
    Authorization: `Bearer ${token}`,
  },
});
} else {
  // Por defecto
  response = await axios.get(`http://127.0.0.1:8000/api/auth/incidents/getall?page=${page}`, {
  headers: {
    Authorization: `Bearer ${token}`,
  },
});
}

// Verificar si la respuesta tiene la propiedad 'data' y asignarla correctamente
if (response.data && Array.isArray(response.data.data)) {
  incidents.value = response.data.data; // Asigna el array de incidencias a incidents
  totalPages.value = response.data.last_page; // Total de páginas
  currentPage.value = page; // Actualiza la página actual
} else {
  console.error('No se encontraron incidencias');
  incidents.value = []; // Asigna un array vacío si no hay incidencias
}
} catch (error) {
  console.error('Error al obtener las incidencias:', error);
}
};

// Llamar a fetchIncidents al cargar el componente
onMounted(() => {
  fetchIncidents('default'); // Llamada por defecto sin filtro
});

// Método para recibir el evento desde el componente hijo
const onFiltersChanged = (filters) => {
  /// Detectamos qué filtro ha sido actualizado y asignamos su valor
  let filterType = '';  // Esta variable guardará el tipo de filtro

  if (filters.selectedCampus !== undefined) {
    selectedFilter.value = filters.selectedCampus;
    filterType = 'campus';  // Establecemos que el filtro es de tipo 'campus'
  } else if (filters.selectedSection !== undefined) {
    selectedFilter.value = filters.selectedSection;
    filterType = 'section';  // Establecemos que el filtro es de tipo 'section'
  } else if (filters.selectedFailureType !== undefined) {
    selectedFilter.value = filters.selectedFailureType;
    filterType = 'failureType';  // Establecemos que el filtro es de tipo 'failureType'
  } else if (filters.selectedImportance !== undefined) {
    selectedFilter.value = filters.selectedImportance;
    filterType = 'importance';  // Establecemos que el filtro es de tipo 'importance'
  }
  // Hacer la llamada a la API con el filtro seleccionado
  fetchIncidents(filterType, currentPage.value);
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
.container {
  margin-top: 20px;
}
</style>