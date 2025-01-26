<script setup>
    import { RouterLink } from 'vue-router';
    import { onMounted } from 'vue';
    //import { ref } from 'vue';
    import { computed } from 'vue';
    import { useAuthStore } from '@/stores/auth'; // Assuming your store is in stores/auth.js

    const authStore = useAuthStore();

    const isLoggedIn = computed(() => authStore.isLoggedIn);

    const userDetails = computed(() => authStore.user);

    //let isLoggedIn = ref(false);

    //let userDetails = ref([]);

    // In your main App.vue or a global mixin:
    onMounted(() => {
        const token = localStorage.getItem('authToken');
        const tokenExpiry = localStorage.getItem('tokenExpiry');

        console.log(token);

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

                    alert("Error getting your profile. Please try again");
                }

                return response.json();
            })
            .then(data => {

                userDetails = data["user"];

                //console.log("Notes: " + notes.length);

                if(userDetails.length > 0) {
                    isLoggedIn = true;
                } else {
                    isLoggedIn = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                console.log(error);
            });
        }/* else {
            // Token is expired or not found, redirect to login
            router.push('/login');
        }*/
    });
</script>

<template>
    <nav class="navbar">
        <RouterLink to="/" class="logo">📝 NotesApp</RouterLink>
        <ul class="nav-links">
            <li><RouterLink to="/" class="nav-link">Home</RouterLink></li>
            <li><RouterLink to="/add-note" class="nav-link">Add Note</RouterLink></li>
            <li v-if="isLoggedIn"><RouterLink to="/view-notes" class="nav-link">View Notes</RouterLink></li>
            <li><RouterLink to="/about" class="nav-link">About</RouterLink></li>
            <li v-if="!isLoggedIn"><RouterLink to="/register" class="nav-link">Register</RouterLink></li>
            <li v-if="!isLoggedIn"><RouterLink to="/login" class="nav-link">Login</RouterLink></li>
        </ul>
    </nav>
</template>