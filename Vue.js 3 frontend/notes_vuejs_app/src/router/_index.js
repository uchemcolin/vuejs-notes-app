import { createRouter, createWebHistory } from "vue-router";
import HomeView from "@/views/HomeView.vue";
import AddNoteView from "@/views/AddNoteView.vue";
import ViewNotesView from "@/views/ViewNotesView.vue";
import AboutView from "@/views/AboutView.vue";
import RegisterView from "@/views/RegisterView.vue";
import LoginView from "@/views/LoginView.vue";
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
            name: "views-notes",
            component: ViewNotesView,
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
            path: "/:catchAll(.*)",
            name: "not-found",
            component: NotFoundView
        }
    ]
});
  
router.beforeEach((to, from, next) => {
    const isLoggedIn = localStorage.getItem('authToken') !== null; 

    const authStore = useAuthStore();
  
    if (to.matched.some(record => record.meta.requiresAuth) && !isLoggedIn) {
        next({ name: 'login' });
    } else if (to.matched.some(record => record.meta.guest) && isLoggedIn) {

        // check if token is still valid
        const token = localStorage.getItem('authToken');
        const tokenExpiry = localStorage.getItem('tokenExpiry');

        if (token && tokenExpiry > Date.now()) {

            const url = '/api/users/profile';
            //const data = form;

            fetch(url, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                //body: JSON.stringify(data)
            })
            .then(response => {
                if (!response.ok) {
                    //throw new Error('Network response was not ok');

                    //alert("Error getting your notes. Please try again");

                    next({ name: 'login' });
                }

                return response.json();
            })
            .then(data => {

                userDetails = data["user"];

                //console.log("Notes: " + notes.length);

                if(userDetails.length > 0) {

                    

                    authStore.setUser(user);
                    
                    next({ name: 'views-notes' });
                } else {
                    //alert("The issue");

                    authStore.logout();

                    next({ name: 'login' });
                }
            })
            .catch(error => {
                /*console.error('Error:', error);
                console.log(error);*/

                authStore.logout();

                next({ name: 'login' });
            });
        } else {
            // Token is expired or not found, redirect to login

            authStore.logout();
            
            next({ name: 'login' });
        }
    } else {
        next();

        //alert("The issue");
    }
});

export default router;