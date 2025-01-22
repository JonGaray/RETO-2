import { createRouter, createWebHistory } from 'vue-router'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'login',
      component: () => import('../components/Login.vue'),
    },
    {
      path: '/user/:id',
      name: 'user',
      component: () => import('../views/UserView.vue'),
      props: true
    },
    {
      path: '/tecnico/:id',
      name: 'tecnico',
      component: () => import('../views/TecnicoView.vue'),
      props: true
    },
    {
      path: '/admin/:id',
      name: 'admin',
      component: () => import('../views/AdminView.vue'),
      props: true
    },
  ],
})

export default router
