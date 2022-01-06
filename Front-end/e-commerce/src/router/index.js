import Vue from 'vue';
import Router from 'vue-router';
import Contact from '../components/Contact.vue';
import Login from '../components/Login.vue';
import Checkout from '../components/Checkout.vue';
import Cart from '../components/Cart.vue';
import Home from '../components/Home.vue';

Vue.use(Router);

export default new Router({
    mode: 'history',
    routes: [{
        path: '/',
        name: 'Home',
        component: Home
    }, {
        path: '/contact',
        name: 'Contact',
        component: Contact
    }, {
        path: '/login',
        name: 'Login',
        component: Login
    }, {
        path: '/checkout',
        name: 'Checkout',
        component: Checkout,
    }, {
        path: '/cart',
        name: 'Cart',
        component: Cart
    }]
});