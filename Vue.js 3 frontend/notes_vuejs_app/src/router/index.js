import { createRouter, createWebHistory } from "vue-router";
import HomeView from "@/views/HomeView.vue";
import AddNoteView from "@/views/AddNoteView.vue";
import ViewNotesView from "@/views/ViewNotesView.vue";
import ViewNoteView from "@/views/ViewNoteView.vue";
import EditNoteView from "@/views/EditNoteView.vue";
import ViewProfileView from "@/views/ViewProfileView.vue";
import EditProfileView from "@/views/EditProfileView.vue";
import AboutView from "@/views/AboutView.vue";
import RegisterView from "@/views/RegisterView.vue";
import LoginView from "@/views/LoginView.vue";
import ForgotPasswordView from "@/views/ForgotPasswordView.vue";
import ResetPasswordView from "@/views/ResetPasswordView.vue";
import NotFoundView from "@/views/NotFoundView.vue";
import { useAuthStore } from '@/stores/auth';


const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
      {
        path: "/",
        name: "home",
        component: HomeView,
      },
      {
        path: "/add-note",
        name: "add-note",
        component: AddNoteView,
        meta: { requiresAuth: true }
      },
      {
        path: "/view-notes",
        name: "view-notes",
        component: ViewNotesView,
        meta: { requiresAuth: true }
      },
      {
        path: '/view-note/:id',
        name: "view-note",
        component: ViewNoteView,
        meta: { requiresAuth: true }
      },
      {
        path: '/edit-note/:id',
        name: "edit-note",
        component: EditNoteView,
        meta: { requiresAuth: true }
      },
      {
        path: '/view-profile',
        name: "view-profile",
        component: ViewProfileView,
        meta: { requiresAuth: true }
      },
      {
        path: '/edit-profile',
        name: "edit-profile",
        component: EditProfileView,
        meta: { requiresAuth: true }
      },
      {
        path: "/about",
        name: "about",
        component: AboutView,
        //meta: { requiresAuth: true }
      },
      {
        path: "/register",
        name: "register",
        component: RegisterView,
        meta: { guest: true }
      },
      {
        path: "/login",
        name: "login",
        component: LoginView,
        meta: { guest: true }
      },
      {
        path: "/forgot-password",
        name: "forgot-password",
        component: ForgotPasswordView,
        meta: { guest: true }
      },
      {
        path: '/reset-passowrd/:token',
        name: "reset-password",
        component: ResetPasswordView,
        meta: { guest: true }
      },
      {
        path: "/:catchAll(.*)",
        name: "not-found",
        component: NotFoundView
      }
    ]
});
  
let hasValidatedToken = false; // Flag to track token validation

router.beforeEach((to, from, next) => {
    const isLoggedIn = localStorage.getItem('authToken') !== null;

    const authStore = useAuthStore();

    //if (to.matched.some(record => record.meta.requiresAuth) && !isLoggedIn) {
    if (to.matched.some(record => record.meta.requiresAuth) && isLoggedIn === false) {
    next({ name: 'login' });
  //} else if (to.matched.some(record => record.meta.guest) && isLoggedIn === true) {
    } else if (to.matched.some(record => record.meta.requiresAuth) && isLoggedIn === true) {

    const token = localStorage.getItem('authToken');
    const tokenExpiry = localStorage.getItem('tokenExpiry');

    //if (!hasValidatedToken) {

      if (token && tokenExpiry > Date.now()) {

        // Token already validated, proceed
        next();

        //next({ name: 'view-notes' });

        /*const url = '/api/users/profile';

        fetch(url, {
          method: 'GET',
          headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`
          }
        })
        .then(response => {
          if (!response.ok) {
            next({ name: 'login' });
          } else {
            return response.json();
          }
        })
        .then(data => {
          const userDetails = data["user"];
          authStore.setUser(userDetails); // Set user in store
          hasValidatedToken = true; // Mark token as validated
          next({ name: 'view-notes' });
        })
        .catch(error => {
          authStore.logout();
          next({ name: 'login' });
        });*/
      } else {
        //authStore.logout();
        next({ name: 'login' });
      }
    /*} else {
      // Token already validated, proceed
      next();
    }*/
  } else {
    next();
  }
});

export default router;