import './assets/bootstrap-5.0.2/dist/css/bootstrap.css'
import "./assets/bootstrap-5.0.2/dist/css/styles.css"

import { createApp } from 'vue'
import App from './App.vue'
import router from './router'

const app = createApp(App)

app.use(router)

app.mount('#app')
