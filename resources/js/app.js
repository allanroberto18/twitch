import Vue from 'vue';
import router from './router';
import App from './components/AppComponent';
import VueScrollTo from 'vue-scrollto';

Vue.use(VueScrollTo);
Vue.use(router);

const app = new Vue({
   router, render: h => h(App)
}).$mount('#app');
