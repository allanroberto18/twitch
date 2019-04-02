import Vue from 'vue';
import Router from 'vue-router';
import HeaderTemplate from './templates/HeaderTemplate';
import LoginComponent from "./components/LoginComponent";
import HomeComponent from "./components/HomeComponent";

Vue.use(Router);

const routes = [
    {
        path: '/',
        components: {
            header: HeaderTemplate,
            default: LoginComponent
        }
    },
    {
        path: '/home',
        name: 'home',
        components: {
            header: HeaderTemplate,
            default: HomeComponent
        }
    }
];

export default new Router({
    routes
});

