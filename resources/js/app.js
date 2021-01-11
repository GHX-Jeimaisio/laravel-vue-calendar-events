require('./bootstrap');

import Vue from 'vue'

//Main pages
import App from './views/app.vue';
import FlashMessage from '@smartweb/vue-flash-message';
Vue.use(FlashMessage);
const app = new Vue({
    el: '#app',
    components: { App }
});
