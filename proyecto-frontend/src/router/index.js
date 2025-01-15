import { createRouter, createWebHistory } from 'vue-router'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'login',
      component: () => import('../components/Login.vue'), // Componente de login
    },
    {
      path: '/user/:id', // Ruta que acepta un parámetro 'id'
      name: 'user',
      component: () => import('../views/UserView.vue'), // Componente que muestra los datos del usuario
      props: true // Pasar el parámetro 'id' como prop al componente
    },
    {
      path: '/register', // Ruta para el registro
      name: 'register',
      component: () => import('../components/Register.vue'), // Componente del usuario
    },
  ],
})

export default router
