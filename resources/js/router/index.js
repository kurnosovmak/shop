import VueRouter from 'vue-router'
import NavBarComponent from '../components/NavBarComponent'


const routes = [
    {
        path: '/',
        name: 'Home',
        components: {
            default: () => import(/* webpackChunkName: "ProductsView" */ '../views/ProductsView.vue')
        }
    },
    {
        path: '/product/:id',
        name: 'Product',
        components: {
            default: () => import(/* webpackChunkName: "ProductView" */ '../views/ProductView.vue')
        }
    },
    {
        path: '/basket',
        name: 'Basket',
        components: {
            default: () => import(/* webpackChunkName: "BasketView" */ '../views/BasketView.vue')
        }
    },

]

const router = new VueRouter({
    mode: 'history',
    base: process.env.BASE_URL,
    routes
})

router.beforeEach((to, from, next) => {
    // const loggedIn = localStorage.getItem('user')

    // if (to.matched.some((record) => record.meta.requiresAuth) && !loggedIn) {
    //   next('/')
    // } else if (
    //   to.matched.some((record) => record.meta.requiresVisitor) &&
    //   loggedIn
    // ) {
    //   next('/')
    // } else {
    next()
    // }
})

export default router
