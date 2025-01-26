/*// Import the functions you need from the SDKs you need
import { initializeApp } from "firebase/app";
// TODO: Add SDKs for Firebase products that you want to use
// https://firebase.google.com/docs/web/setup#available-libraries

import { getFirestore } from "firebase/firestore";

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

export default app;*/

import { initializeApp } from "firebase/app";
import { getDatabase } from "firebase/database";

const firebaseConfig = {
  // Your Firebase configuration here
  apiKey: "AIzaSyCtl0eVjQws7KI2jLadI_ejTYWNu9dbHXY",
  authDomain: "vuejs-notes-app-f1a2a.firebaseapp.com",
  projectId: "vuejs-notes-app-f1a2a",
  storageBucket: "vuejs-notes-app-f1a2a.firebasestorage.app",
  messagingSenderId: "963965914863",
  appId: "1:963965914863:web:934cd1f7ab870a4a1c551a"
};

const app = initializeApp(firebaseConfig);
const database = getDatabase(app);

export { database };