<script setup>
import { useRoute } from 'vue-router';
import UserPanel from '../components/UserPanel.vue'
import Header from '../components/Header.vue'

const route = useRoute();
const userId = route.params.id || JSON.parse(sessionStorage.getItem('user'))?.id;
</script>

<template>
  <main>
    <div class="container">
      <div class="row">
        <div class="col-12">
          <Header/>
        </div>
        <div class="col-4">
          <UserPanel :id="userId"/>
        </div>
      </div>
    </div>
  </main>
</template>
