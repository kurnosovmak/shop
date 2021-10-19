import VueRouter from 'vue-router'
import NavBarComponent from '../components/NavBarComponent'


const routes = [
    {
        path: '/',
        name: 'Home',
        components: {
            default: () => import(/* webpackChunkName: "ProductsView" */ '../views/ProductsView.vue')
        },
        meta: {
            requiresAuth: false,
            requiresVisitor: false,
        },

    },
    {
        path: '/product/:id',
        name: 'Product',
        components: {
            default: () => import(/* webpackChunkName: "ProductView" */ '../views/ProductView.vue')
        },
        meta: {
            requiresAuth: false,
            requiresVisitor: false,
        },
    },
    {
        path: '/basket',
        name: 'Basket',
        components: {
            default: () => import(/* webpackChunkName: "BasketView" */ '../views/BasketView.vue')
        },
        meta: {
            requiresAuth: true,
            requiresVisitor: false,
        },
    },


    {
        path: '/login',
        name: 'Login',
        components: {
            default: () => import(/* webpackChunkName: "LoginView" */ '../views/LoginView.vue')
        },
        meta: {
            requiresAuth: false,
            requiresVisitor: true,
        },
    },


    {
        path: '/register',
        name: 'Register',
        components: {
            default: () => import(/* webpackChunkName: "RegisterView" */ '../views/RegisterView.vue')
        },
        meta: {
            requiresAuth: false,
            requiresVisitor: true,
        },
    },

]

const router = new VueRouter({
    mode: 'history',
    base: process.env.BASE_URL,
    routes
})

router.beforeEach((to, from, next) => {
    const loggedIn = localStorage.getItem('token')

    if (to.matched.some((record) => record.meta.requiresAuth) && !loggedIn) {
        next('/')
    } else if (
        to.matched.some((record) => record.meta.requiresVisitor) &&
        loggedIn
    ) {
        next('/')
    } else {
        next()
    }
})

export default router
