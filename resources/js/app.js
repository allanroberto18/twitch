import Vue from 'vue';
import router from './router';
import App from './components/AppComponent';
import VueScrollTo from 'vue-scrollto';

Vue.use(VueScrollTo);
//
// // You can also pass in the default options
// Vue.use(VueScrollTo, {
//     container: "body",
//     duration: 500,
//     easing: "ease",
//     offset: 0,
//     force: true,
//     cancelable: true,
//     onStart: false,
//     onDone: false,
//     onCancel: false,
//     x: false,
//     y: true
// })
Vue.use(router);

const app = new Vue({
   router, render: h => h(App)
}).$mount('#app');
