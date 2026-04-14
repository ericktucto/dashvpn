import { createWebHistory, createRouter } from 'vue-router'

import auth from './middlewares/auth'
import LoginView from '@/pages/auth/LoginView.vue'
import ServerView from '@/modules/server/pages/ServerView.vue'
import PeerIndexView from '@/modules/peers/pages/PeerIndexView.vue'

const routes = [
    { path: '/', component: LoginView, name: 'auth@login' },
    { path: '/peers', component: PeerIndexView, name: 'peers@index' },
    { path: '/server', component: ServerView, name: 'server@index' },
]

const router = createRouter({
    history: createWebHistory(),
    routes,
})
router.beforeEach(auth)

export { router }; 
