<script setup>
import HeaderAdmin from '../components/HeaderAdmin.vue';
import AdminPagePanel from '../components/AdminPagePanel.vue';
import AdminUserPanel from '../components/AdminUserPanel.vue';
import AdminMachinePanel from '../components/AdminMachinePanel.vue'
import AdminCampusPanel from '@/components/AdminCampusPanel.vue';
import AdminSectionPanel from '@/components/AdminSectionPanel.vue';
import AdminFailureTypePanel from '@/components/AdminFailureTypePanel.vue';
</script>

<template>
    <main>
    <div class="container">
      <div class="row">
        <div class="col-12">
          <HeaderAdmin />
        </div>
        <div class="col-4">
            <AdminPagePanel />
        </div>
        <div class="col-8">
          <AdminSectionPanel />
        </div>
      </div>
    </div>
  </main>

</template>

<style scoped>

</style>
