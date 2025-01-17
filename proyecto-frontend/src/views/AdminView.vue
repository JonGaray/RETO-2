<script setup>
import HeaderAdmin from '../components/HeaderAdmin.vue';
import AdminPagePanel from '../components/AdminPagePanel.vue';
import AdminUserPanel from '../components/AdminUserPanel.vue';
import AdminMachinePanel from '../components/AdminMachinePanel.vue';
import AdminCampusPanel from '@/components/AdminCampusPanel.vue';
import AdminSectionPanel from '@/components/AdminSectionPanel.vue';
import AdminFailureTypePanel from '@/components/AdminFailureTypePanel.vue';
import { ref } from 'vue'; // Importamos ref para la reactividad

// Creamos una propiedad reactiva para controlar el componente activo
const activePanel = ref('section'); // Valor inicial es "section", pero puedes poner "user" u otro

// Función para cambiar el panel activo desde AdminPagePanel
const setActivePanel = (panel) => {
  activePanel.value = panel;
};

// Función para obtener el componente a renderizar dinámicamente
function getComponent(panel) {
  switch (panel) {
    case 'user':
      return AdminUserPanel;
    case 'machine':
      return AdminMachinePanel;
    case 'campus':
      return AdminCampusPanel;
    case 'section':
      return AdminSectionPanel;
    case 'failureType':
      return AdminFailureTypePanel;
    default:
      return AdminSectionPanel; // Componente por defecto si no hay match
  }
}
</script>

<template>
  <main>
    <div class="container">
      <div class="row">
        <div class="col-12">
          <HeaderAdmin />
        </div>
        <div class="col-4">
          <!-- Pasamos la función para cambiar el panel activo -->
          <AdminPagePanel @panelChange="setActivePanel" />
        </div>
        <div class="col-8">
          <!-- Renderizado condicional basado en el valor de activePanel -->
          <component :is="getComponent(activePanel)" />
        </div>
      </div>
    </div>
  </main>
</template>

<style scoped>
/* Aquí podrías agregar estilos si lo deseas */
</style>
