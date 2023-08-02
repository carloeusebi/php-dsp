import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { library } from '@fortawesome/fontawesome-svg-core';
import { faIcons } from './assets/font_awesome';
import { pinia } from './stores';
import { createApp } from 'vue';
import router from './routes';
import App from './App.vue';
import './style.css';

library.add(...faIcons);

createApp(App).use(pinia).use(router).component('font-awesome-icon', FontAwesomeIcon).mount('#app');
