//import './assets/main.css'
import './assets/mystyle.css';

import router from "./router";

import { createApp } from 'vue';
import { createPinia } from 'pinia'; // Import createPinia
import App from './App.vue';
import Toast, { POSITION } from 'vue-toastification';
import 'vue-toastification/dist/index.css';
import FontAwesomeIcon from "./fontawesome/fontawesome";

const pinia = createPinia(); // Create a Pinia instance

const app = createApp(App);

// Toastification options (optional)
const options = {
    position: POSITION.TOP_RIGHT, // Default position
    timeout: 5000, // Default timeout
};
  
app.use(Toast, options);

app.use(pinia); // Install Pinia
app.use(router);

// Register the Font Awesome Icon component globally
app.component('font-awesome-icon', FontAwesomeIcon);

app.mount('#app');