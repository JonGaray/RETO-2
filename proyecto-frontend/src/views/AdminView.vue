<script setup>
import HeaderAdmin from '../components/HeaderAdmin.vue';
import AdminPagePanel from '../components/AdminPagePanel.vue';
import AdminUserPanel from '../components/AdminUserPanel.vue';
import AdminMachinePanel from '../components/AdminMachinePanel.vue';
import AdminCampusPanel from '@/components/AdminCampusPanel.vue';
import AdminSectionPanel from '@/components/AdminSectionPanel.vue';
import AdminFailureTypePanel from '@/components/AdminFailureTypePanel.vue';
import AdminMaintenancePanel from '@/components/AdminMaintenancePanel.vue';
import { ref } from 'vue';

const activePanel = ref('section');
const setActivePanel = (panel) => {
  activePanel.value = panel;
};
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
    case 'maintenance':
      return AdminMaintenancePanel;
    default:
      return AdminSectionPanel;
  }
}
</script>

<template>
  <main>
    <div class="container py-4">
      <div class="row">
        <div class="col-12">
          <HeaderAdmin />
        </div>
        <div class="col-lg-4 col-md-6 col-sm-12">
          <AdminPagePanel @panelChange="setActivePanel" />
        </div>
        <div class="col-lg-8 col-md-6 col-sm-12">
          <component :is="getComponent(activePanel)" />
        </div>
      </div>
    </div>
  </main>
</template>

<style scoped>
</style>
