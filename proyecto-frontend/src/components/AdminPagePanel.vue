<template>
  <div class="col-12">
    <div class="card p-3 mt-5">
      <h4 class="text-center mb-4">Panel de Administración</h4>
      <p class="text-muted text-center mb-4">Este es el lugar desde el cual el administrador puede administrar la aplicación.</p>
      <div class="d-grid gap-3">
        <button class="btn btn-outline-egibide mt-3" @click="$emit('panelChange', 'user')">Usuarios</button>
        <button class="btn btn-outline-egibide mt-3" @click="$emit('panelChange', 'machine')">Máquinas</button>
        <button class="btn btn-outline-egibide mt-3" @click="$emit('panelChange', 'maintenance')">Mantenimientos</button>
        <button class="btn btn-outline-egibide mt-3" @click="$emit('panelChange', 'failureType')">Tipo de Fallos</button>
        <button class="btn btn-outline-egibide mt-3" @click="$emit('panelChange', 'section')">Secciones</button>
        <button class="btn btn-outline-egibide mt-3" @click="$emit('panelChange', 'campus')">Centros</button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AdminPagePanel'
}
</script>

<style scoped>
/* Aquí podrías agregar estilos si lo deseas */
</style>
