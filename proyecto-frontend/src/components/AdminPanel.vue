<template>
    <div class="col-12">
      <div class="card mt-5">
        <div class="card-body mt-2 mb-2">
          <div class="d-flex align-items-center justify-content-evenly mb-2">
            <img src="../img/foto-default.png" alt="Foto de perfil" class="rounded-circle img-fluid foto-perfil-usu">
            <h5 class="fw-bold mb-0 text-nowrap">{{ userName }}</h5>
          </div>
          <div class="d-flex justify-content-between">
          </div>
          <div class="d-flex justify-content-center">
            <button class="btn btn-egibide mt-3 boton-perfil-usu">Ver perfil</button>
          </div>
        </div>
      </div>
      <div class="w-100"></div>
      <div class="card mt-5 p-3">
          <h5 class="text-start">Panel de Administración</h5>
          <p class="text-start text-muted">Gestión de usuarios del sistema</p>
          <p class="text-start">Técnicos Disponibles <span class="fw-bold">2</span></p>
          <ul class="list-group">
            <li class="list-group-item d-flex justify-content-between align-items-center">
              Juan Pérez <span class="">Técnico</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              Miguel Fernandez <span class="">Alumno</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              Maria Gimenez <span class="">Profesor</span>
            </li>
          </ul>
          <button class="btn btn-egibide w-100 mt-3">Panel de Administrador</button>
        </div>
    </div>
  </template>
  
  <script>

  </script>
  
  <style scoped>

  </style>