import { createMemoryHistory, createRouter } from 'vue-router'

import auth from './middlewares/auth'
import LoginView from '@/pages/auth/LoginView.vue'
import DashboardView from '@/pages/DashboardView.vue'

const routes = [
    { path: '/', component: LoginView, name: 'auth@login' },
    { path: '/dashboard', component: DashboardView, name: 'dashboard' },
]

const router = createRouter({
    history: createMemoryHistory(),
    routes,
})
router.beforeEach(auth)

export { router }; 
