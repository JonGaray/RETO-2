<script setup>
import { useRoute } from 'vue-router'; // Importar useRoute para acceder a la ruta
import UserPanel from '../components/UserPanel.vue'
import Header from '../components/Header.vue'

// Obtener el id desde el sessionStorage o de la URL
const route = useRoute();
const userId = route.params.id || JSON.parse(sessionStorage.getItem('user'))?.id;
console.log('id en UserView:', userId);  // Verificar si el ID está disponible
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
