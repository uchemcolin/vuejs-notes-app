<script setup>
    import { reactive } from 'vue';
    import { RouterLink } from 'vue-router';
    import { useRouter } from 'vue-router';
    import { ref } from "vue";
    import { defineStore } from 'pinia';
    import { useAuthStore } from '@/stores/auth';

    const authStore = useAuthStore();

    const router = useRouter();

    let form = reactive(
        {
            email: "",
            password: ""
        }
    );

    let loggingInUser = ref(false);
    
    // login user
    let loginUser = async () => {

        if(form.email.trim() !== "" && form.password.trim() !== "") {
            const url = '/api/login';
            const data = form;

            loggingInUser.value = true;

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => {
                if (!response.ok) {
                    //throw new Error('Network response was not ok');

                    //alert("Error logging you in. Please try again");

                    alert("Network response was not okay. Pleaae try again.");
                }

                loggingInUser.value = false;

                return response.json();
            })
            .then(data => {
                console.log('Success:', data);

                loggingInUser.value = false;

                if(data["type"] == "success") {
                    let token = data["token"];
                    let user = data["user"];

                    // Store the token in local storage with a 1-day expiration
                    localStorage.setItem('authToken', token);
                    localStorage.setItem('tokenExpiry', Date.now() + 24 * 60 * 60 * 1000); // 1 day in milliseconds

                    //authStore.setUser(user);

                    // Explicitly set login state
                    authStore.setIsLoggedIn(true);

                    // Redirect to the '/view-notes' route
                    router.push('/view-notes');
                } else {
                    alert(data["message"]);
                }

                
            })
            .catch(error => {
                console.error('Error:', error);

                loggingInUser.value = false;

                alert("Error logging you in. Please try again");
            });
        } else {

            loggingInUser.value = false;

            alert("Please fill all fields");
        }
    }
</script>

<template>

    <main class="page auth-page">
        <h1>Login</h1>
        <form class="auth-form" @submit.prevent="loginUser">
            <input type="email" class="form-input" placeholder="Email" v-model="form.email" required>
            <input type="password" class="form-input" placeholder="Password" v-model="form.password" required>
            <button type="submit" class="btn btn-primary" :disabled="loggingInUser">
                <span v-if="!loggingInUser">Login</span>
                <span v-else>Logging in... <font-awesome-icon icon="spinner" class="fa-spin" size="lg" /></span>
                <!--<font-awesome-icon icon="spinner" class="fa-spin" size="lg" :style="{ color: 'white' }" />-->
            </button>
            <p>Don't have an account? <RouterLink to="/register" class="link">Register</RouterLink></p>
            <p>
                <small>
                    Forgot password? <RouterLink to="/forgot_password" class="link">Reset it</RouterLink>
                </small>
            </p>
        </form>
    </main>
</template>

<style scoped>
    /* Authentication Pages */
    .auth-page {
        text-align: center;
        padding: 4rem 2rem;
    }

    .auth-page h1 {
        font-size: 2.5rem;
        color: #fff;
        margin-bottom: 2rem;
    }

    .auth-form {
        max-width: 400px;
        margin: 0 auto;
        background: #fff;
        padding: 2rem;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    }

    .form-input {
        width: 100%;
        padding: 1rem;
        margin-bottom: 1rem;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 1rem;
        box-sizing: border-box;
    }

    .form-input:focus {
        outline: none;
        border-color: #f3ec78;
        box-shadow: 0 0 5px rgba(243, 236, 120, 0.8);
    }

    .btn-primary {
        width: 100%;
        padding: 1rem;
        font-size: 1.2rem;
        background: #f3ec78;
        color: #333;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background 0.3s, transform 0.3s;
    }

    .btn-primary:hover {
        background: #af4261;
        color: #fff;
        transform: scale(1.05);
    }

    .link {
        color: #af4261;
        text-decoration: none;
        font-weight: bold;
    }

    .link:hover {
        text-decoration: underline;
    }
</style>