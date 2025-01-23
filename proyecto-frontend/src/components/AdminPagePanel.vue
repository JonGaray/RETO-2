<template>
  <div class="container-fluid mt-5">
    <div class="card p-4 shadow-sm">
      <h4 class="text-center mb-3">Panel de Administración</h4>
      <p class="text-muted text-center mb-4">
        Este es el lugar desde el cual el administrador puede administrar la aplicación.
      </p>
      <div class="d-grid gap-3">
        <div class="row">
          <div class="col-12 col-md-6 mb-3">
            <button class="btn btn-outline-egibide w-100" @click="$emit('panelChange', 'section')">Secciones</button>
          </div>
          <div class="col-12 col-md-6 mb-3">
            <button class="btn btn-outline-egibide w-100" @click="$emit('panelChange', 'campus')">Campus</button>
          </div>
          <div class="col-12 col-md-6 mb-3">
            <button class="btn btn-outline-egibide w-100" @click="$emit('panelChange', 'user')">Usuarios</button>
          </div>
          <div class="col-12 col-md-6 mb-3">
            <button class="btn btn-outline-egibide w-100" @click="$emit('panelChange', 'machine')">Máquinas</button>
          </div>
          <div class="col-12 col-md-6 mb-3">
            <button class="btn btn-outline-egibide w-100" @click="$emit('panelChange', 'maintenance')">Mantenimientos</button>
          </div>
          <div class="col-12 col-md-6 mb-3">
            <button class="btn btn-outline-egibide w-100" @click="$emit('panelChange', 'failureType')">Tipo de Fallos</button>
          </div>
        </div>
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
</style>