import Vue from 'vue'
import router from "./router";
import VueFlashMessage from 'vue-flash-message';
import VueRouter from "vue-router";
import NavBarComponent from "./components/NavBarComponent";

Vue.use(VueRouter);
require('vue-flash-message/dist/vue-flash-message.min.css');
Vue.use(VueFlashMessage);

Vue.component('nav-bar-component',NavBarComponent);

const app = new Vue({
    el: '#app',

    router,
});
