//import './assets/main.css'
import './assets/mystyle.css';

import router from "./router";

import { createApp } from 'vue';
import { createPinia } from 'pinia'; // Import createPinia
import App from './App.vue';

const pinia = createPinia(); // Create a Pinia instance

//app.use(pinia); // Install Pinia

createApp(App).use(router).use(pinia).mount('#app');

/*app.use(router);

const app = createApp(App);

app.use(router);
app.use(Toast);

app.mount("#app");*/

/*
// Import the functions you need from the SDKs you need
import { initializeApp } from "firebase/app";
// TODO: Add SDKs for Firebase products that you want to use
// https://firebase.google.com/docs/web/setup#available-libraries

// Your web app's Firebase configuration
const firebaseConfig = {
  apiKey: "AIzaSyCtl0eVjQws7KI2jLadI_ejTYWNu9dbHXY",
  authDomain: "vuejs-notes-app-f1a2a.firebaseapp.com",
  projectId: "vuejs-notes-app-f1a2a",
  storageBucket: "vuejs-notes-app-f1a2a.firebasestorage.app",
  messagingSenderId: "963965914863",
  appId: "1:963965914863:web:934cd1f7ab870a4a1c551a"
};

// Initialize Firebase
const app = initializeApp(firebaseConfig);
*/
