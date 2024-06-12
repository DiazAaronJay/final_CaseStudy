import { createApp } from 'vue';
import App from './App.vue';
import router from './router';
import axiosInstance from './axiosInstance';
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';

const app = createApp(App);

app.config.globalProperties.$axios = axiosInstance;

app.use(router);
app.mount('#app');
