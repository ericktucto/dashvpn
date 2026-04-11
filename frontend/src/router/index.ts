import { createWebHistory, createRouter } from 'vue-router'

import auth from './middlewares/auth'
import LoginView from '@/pages/auth/LoginView.vue'
import DashboardView from '@/pages/DashboardView.vue'
import ServerListView from '@/modules/servers/pages/ServerListView.vue'

const routes = [
    { path: '/', component: LoginView, name: 'auth@login' },
    { path: '/dashboard', component: DashboardView, name: 'dashboard' },
    { path: '/server', component: ServerListView, name: 'server@index' },
]

const router = createRouter({
    history: createWebHistory(),
    routes,
})
router.beforeEach(auth)

export { router }; 
