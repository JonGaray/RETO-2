<template>

    <div class="card shadow-sm mb-3" style="border-radius: 15px;">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h5 class="card-title fw-bold badge-initcap">{{ title }}</h5>
                    <p class="card-text text-muted badge-initcap">{{ description }}</p>
                    <ul class="list-unstyled mb-0">
                        <li class="badge-initcap"><strong>Gravedad:</strong> {{ category }}</li>
                        <li class="badge-initcap"><strong>Tipo de fallo:</strong> {{ failure_type_name }}</li>
                        <li class="badge-initcap"><strong>Maquina:</strong> {{ machine_name }}</li>
                    </ul>
                </div>
                <span :class="badgeClass" class="badge p-2 fs-6 badge-initcap">{{ status }}</span>
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
        type: [String, Number],
        machines_id: [String, Number],
        status: String,
        date: String,
        machine_name: String,
        failure_type_name: String,
    },
    computed: {
        badgeClass() {
            if (this.status === 'nuevo') {
                return 'bg-danger';
            } else if (this.status === 'proceso') {
                return 'bg-warning';
            } else if (this.status === 'terminado') {
                return 'bg-success';
            }
            return 'bg-secondary';
        }
    },
    methods: {
        formattedDate(date) {
            if (!date) return 'Fecha no disponible';
            const fixedDate = date.replace(' ', 'T');
            const parsedDate = new Date(fixedDate);
            return isNaN(parsedDate.getTime()) ? 'Fecha no disponible' : parsedDate.toLocaleDateString();
        }
    }
};
</script>

<style scoped>
</style>