import Vue from 'vue'
import Router from 'vue-router'

Vue.use(Router)

export const constantRouterMap = [
    
    {
        path: '/',
        component: () => import('@/views/problem'),
    },
    {
        path: '/Login',
        component: () => import('@/views/login'),
    },
    {
        path: '/Problem',
        component: () => import('@/views/problem'),
    },
    {
        path: '/ProblemInfo/:id',
        component: () => import('@/views/problemInfo'),
    },
    {
        path: '/User',
        component: () => import('@/views/user'),
    },
    {
        path: '/Ranking',
        component: () => import('@/views/ranking'),
    },
    {
        path: '/UserHome/:id',
        component: () => import('@/views/userHome'),
    }
]

const router = new Router({
    routes: constantRouterMap
})

export default router