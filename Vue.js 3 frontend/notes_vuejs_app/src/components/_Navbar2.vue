<script setup>
    import { RouterLink } from 'vue-router';
    import { useRouter } from 'vue-router';
    import { computed } from 'vue';
    import { ref } from "vue";
    import { useAuthStore } from '@/stores/auth';
    import { useToast } from 'vue-toastification';
    import { text } from '@fortawesome/fontawesome-svg-core';
    import config from "@/my_javascript_files/config";
    import { onMounted } from 'vue';

    const apiUrl = config.apiBaseUrl;

    const toast = useToast(); // Get the toast instance

    const authStore = useAuthStore();

    const router = useRouter();

    const isLoggedIn = computed(() => authStore.isLoggedIn);

    //let isLoggedIn = ref(false);

    //console.log(isLoggedIn.value + " currently logged in");

    /*const token = null;
    const tokenExpiry = null;

    if(isLoggedIn == true) {*/

        //const token = localStorage.getItem('authToken');
        //const tokenExpiry = localStorage.getItem('tokenExpiry');
    //}

    //const token = computed(() => authStore.token);
    //const tokenExpiry = computed(() => authStore.tokenExpiry);

    //let token = null;
    //let tokenExpiry = null;

    //onMounted(() => {
        /*if (token.value && Number(tokenExpiry.value) > Date.now()) {
            authStore.setIsLoggedIn(true);
        } else {
            authStore.setIsLoggedIn(false);
        }*/

        /*token = authStore.token;
        tokenExpiry = authStore.tokenExpiry;

        //console.log(token);

        if (token && tokenExpiry > Date.now()) {
            authStore.setIsLoggedIn(true);
        } else {
            authStore.setIsLoggedIn(false);
        }

        console.log(token);
    });*/

    /*const token = computed(() => authStore.token);
    const tokenExpiry = computed(() => authStore.tokenExpiry);

    if (token && tokenExpiry > Date.now()) {
        authStore.setIsLoggedIn(true);
    } else {
        authStore.setIsLoggedIn(false);
    }

    console.log(token);*/

    //let loggingOut = ref(false);

    let token = authStore.token;
    let tokenExpiry = authStore.tokenExpiry;

    console.log("token: " + token);

    let loggingOut = ref(false);

    console.log(token);

    if (token && tokenExpiry > Date.now()) {
        authStore.setIsLoggedIn(true);
    } else {
        authStore.setIsLoggedIn(false);
    }

    /*let loggingOut = ref(false);

    let token = null;
    let tokenExpiry = null;

    onMounted(() => {
        token = authStore.token;
        tokenExpiry = authStore.tokenExpiry;

        console.log(token + " from onMounted");

        if (token && tokenExpiry > Date.now()) {
            authStore.setIsLoggedIn(true);
        } else {
            authStore.setIsLoggedIn(false);
        }
    });*/

    //const token = null;
    //const tokenExpiry = null;

    const logout = async () => {

        token = localStorage.getItem('authToken');
        tokenExpiry = localStorage.getItem('tokenExpiry');

        loggingOut.value = true;

        if (token && tokenExpiry > Date.now()) {
            //authStore.setIsLoggedIn(true);

            //const url = '/api/logout';
            const url = `${apiUrl}/logout`;

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
            })
            .then(response => {
                if (!response.ok) {

                    //alert("Error logging you out. Please try again");

                    loggingOut.value = false;
                }

                return response.json();
            })
            .then(data => {
                //console.log('Success:', data);

                // Clear user data from auth store
                //authStore.setUser(null);
                authStore.setIsLoggedIn(false);
                //authStore.setToken(null); // For NavBar.vue
                //authStore.setTokenExpiry(null); // For NavBar.vue

                // Clear any local storage tokens/data (if applicable)
                localStorage.removeItem('authToken');
                localStorage.removeItem('tokenExpiry');

                authStore.setToken(null);
                authStore.setTokenExpiry(null);

                loggingOut.value = false;

                toast.success("You have successfully logged out", {
                    timeout: 5000, // Custom timeout for this toast
                });

                // Redirect to login page after logout
                router.push('/login');
            })
            .catch(error => {
                console.error('Error:', error);

                //alert("There was an error in logging you out. Please try again.");

                loggingOut.value = false;

                toast.error("There was an error in logging you out. Please try again.", {
                    timeout: 5000, // Custom timeout for this toast
                });
            });
        } else {
            authStore.setIsLoggedIn(false);

            // Clear any local storage tokens/data (if applicable)
            localStorage.removeItem('authToken');
            localStorage.removeItem('tokenExpiry');

            authStore.setToken(null);
            authStore.setTokenExpiry(null);

            loggingOut.value = false;

            toast.success("You have successfully logged out", {
                timeout: 5000, // Custom timeout for this toast
            });

            // Redirect to login page after logout
            router.push('/login');
        }

        
    };
</script>

<template>
    <nav class="navbar">
        <RouterLink to="/" class="logo">📝 NotesApp</RouterLink>
        <ul class="nav-links">
            <li><RouterLink to="/" class="nav-link">Home</RouterLink></li>
            <li v-if="isLoggedIn"><RouterLink to="/add-note" class="nav-link">Add Note</RouterLink></li>
            <li v-if="isLoggedIn"><RouterLink to="/view-notes" class="nav-link">View Notes</RouterLink></li>
            <li><RouterLink to="/about" class="nav-link">About</RouterLink></li>
            <li v-if="isLoggedIn">
                <RouterLink to="/view-profile" class="nav-link">Profile</RouterLink>
            </li>
            <li v-if="isLoggedIn">
                <a href="" @click.prevent="logout" class="nav-link">
                    <span v-if="loggingOut">Logging out... <font-awesome-icon icon="spinner" class="fa-spin" size="lg" /></span>
                    <span v-else>Logout</span>
                </a>
            </li>
            <li v-if="!isLoggedIn"><RouterLink to="/register" class="nav-link">Register</RouterLink></li>
            <li v-if="!isLoggedIn">
                <RouterLink to="/login" class="nav-link">
                    Login
                </RouterLink>
            </li>
        </ul>
    </nav>
</template>