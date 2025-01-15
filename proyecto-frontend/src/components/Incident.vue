<template>
    <div class="card shadow-sm mb-3" style="border-radius: 15px;">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h5 class="card-title fw-bold">{{ title }}</h5>
                    <p class="card-text text-muted">{{ description }}</p>
                    <ul class="list-unstyled mb-0">
                        <li><strong>Gravedad:</strong> {{ category }}</li>
                        <li><strong>Tipo de fallo:</strong> {{ failure_type_name }}</li>
                        <li><strong>Maquina:</strong> {{ machine_name }}</li>
                    </ul>
                </div>
                <!-- Badge con clases dinámicas para el color -->
                <span :class="badgeClass" class="badge p-2 fs-6">{{ status }}</span>
            </div>
            <div class="d-flex justify-content-end">
                <small class="text-muted">{{ new Date(date).toLocaleDateString() }}</small>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        title: String,
        description: String,
        category: String,
        type: [String, Number], // Acepta tanto String como Number
        machines_id: [String, Number], // Acepta tanto String como Number
        status: String,
        date: String,
        machine_name: String,
        failure_type_name: String,
    },
    computed: {
        // Computed property para determinar la clase del badge
        badgeClass() {
            if (this.status === 'nuevo') {
                return 'bg-danger';
            } else if (this.status === 'proceso') {
                return 'bg-warning';
            } else if (this.status === 'terminado') {
                return 'bg-success';
            }
            return 'bg-secondary'; // Por defecto, si no hay estado conocido
        }
    }
};
</script>

<style scoped>
.card {
    max-width: 700px;
    margin: 0 auto;
}
</style>
