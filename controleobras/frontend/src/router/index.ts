import { createRouter, createWebHistory, RouteRecordRaw } from 'vue-router';

const routes: Array<RouteRecordRaw> = [
  {
    path: '/',
    name: 'home',
    component: () => import('../views/Home.vue')
  },
  {
    path: '/obras',
    name: 'obras',
    component: () => import('../views/obras/ObrasList.vue')
  },
  {
    path: '/obras/criar',
    name: 'obras-criar',
    component: () => import('../views/obras/ObraForm.vue')
  },
  {
    path: '/obras/:id',
    name: 'obras-detalhes',
    component: () => import('../views/obras/ObraDetails.vue'),
    props: true
  },
  {
    path: '/obras/:id/editar',
    name: 'obras-editar',
    component: () => import('../views/obras/ObraForm.vue'),
    props: true
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes
});

export default router;
