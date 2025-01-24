<template>
  <div class="card shadow-sm mb-3" style="border-radius: 15px;">
    <div class="card-body">
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-start">
        <div>
          <h5 class="card-title fw-bold badge-initcap">{{ title }}</h5>
          <p class="card-text text-muted badge-initcap">{{ description }}</p>
          <ul class="list-unstyled mb-0">
            <li class="badge-initcap"><strong>Gravedad:</strong> {{ category }}</li>
            <li class="badge-initcap"><strong>Tipo de fallo:</strong> {{ failure_type_name }}</li>
            <li class="badge-initcap"><strong>Maquina:</strong> {{ machine_name }}</li>
          </ul>
        </div>
        <span :class="badgeClass" class="badge p-2 fs-6 badge-initcap mt-2 mt-md-0">{{ status }}</span>
      </div>
      <div class="d-flex flex-column flex-md-row justify-content-end mt-2">
        <div v-if="status === 'proceso'" class="d-flex gap-2">
          <button @click="finishIncident" class="btn btn-outline-egibide btn-sm">
            Finalizar
          </button>
        </div>
      </div>

    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  props: {
    title: String,
    description: String,
    category: String,
    type: [String, Number],
    machines_id: [String, Number],
    status: String,
    register_date: String,
    start_date: String,
    end_date: String,
    machine_name: String,
    failure_type_name: String,
    incidents_id: Number,
  },computed: {
    badgeClass() {
      if (this.status === 'nuevo') return 'bg-danger';
      if (this.status === 'proceso') return 'bg-warning';
      if (this.status === 'terminado') return 'bg-success';
      return 'bg-secondary';
    },
  },
  methods:{
  finishIncident() {
    const token = sessionStorage.getItem('token');
    axios.post(`http://127.0.0.1:8000/api/auth/incidents/${this.incidents_id}/finish`, {}, {
      headers: { Authorization: `Bearer ${token}` }
    }).then(() => {
      this.$emit('refresh');
      window.location.reload();
    }).catch(error => {
      console.error('Error al finalizar la incidencia:', error);
    });
  }
  }

};

</script>