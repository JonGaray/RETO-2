
<template>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <main>
        <div class="row mt-3">
          <div class="d-flex justify-content-between align-items-center">
            <h1 class="text-egibide">Panel de Administrador</h1>
            <button class="btn btn-outline-egibide" @click="logout">Cerrar Sesion</button>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <p>Gestion de incidencias - Egibide</p>
          </div>
        </div>
  
    </main>
  </template>
<script>
export default {
  methods: {
    logout() {
      // Eliminar el token del sessionStorage
      sessionStorage.removeItem('token');
      sessionStorage.removeItem('user');

      // Redirigir al usuario a la página de inicio
      this.$router.push('/');
    }
  }
};
</script>