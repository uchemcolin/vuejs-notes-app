<script setup>
    import { reactive, ref } from 'vue';
    import { RouterLink, useRouter } from 'vue-router';
    import { useAuthStore } from '@/stores/auth';
    import { useToast } from 'vue-toastification';
    import config from "@/my_javascript_files/config";

    const apiUrl = config.apiBaseUrl;

    const toast = useToast(); // Get the toast instance

    const authStore = useAuthStore();
    const router = useRouter();

    let form = reactive({
        email: "",
        password: "",
    });

    let loggingInUser = ref(false);

    // Validation state
    let errors = reactive({
        email: "",
        password: "",
    });

    const validateEmail = (email) =>
        /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);

    const validateForm = () => {
        let isValid = true;

        // Validate email
        if (form.email.trim() === "") {
            errors.email = "Email is required.";
            isValid = false;
        } else if (!validateEmail(form.email)) {
            errors.email = "Please enter a valid email address.";
            isValid = false;
        } else {
            errors.email = "";
        }

        // Validate password
        if (form.password.trim() === "") {
            errors.password = "Password is required.";
            isValid = false;
        } else {
            errors.password = "";
        }

        return isValid;
    };

    const loginUser = async () => {
        if (validateForm()) {
            //const url = '/api/login';
            const url = `${apiUrl}/login`;
            //const url = "https://simple-notes-app-backend.uchemcolin.xyz/api/login";
            //const url = "http://simple-notes-app-backend.uchemcolin.xyz/api/login";
            const data = form;

            loggingInUser.value = true;

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data),
            })
            .then((response) => {
                if (!response.ok) {
                    //alert("Network response was not okay. Please try again.");
                }

                loggingInUser.value = false;
                return response.json();
            })
            .then((data) => {
                loggingInUser.value = false;

                if (data.type === "success") {
                    const token = data.token;

                    // Store the token with a 1-day expiration
                    localStorage.setItem('authToken', token);
                    localStorage.setItem('tokenExpiry', Date.now() + 24 * 60 * 60 * 1000);

                    authStore.setIsLoggedIn(true);
                    //authStore.setToken(token); // For NavBar.vue
                    //authStore.setTokenExpiry(Date.now() + 24 * 60 * 60 * 1000); // For NavBar.vue

                    //authStore.setToken(null);
                    //authStore.setTokenExpiry(null);

                    // Decided to use location.href
                    // because push was not getting
                    // the localStorage token from
                    // the browser. It only gets it
                    // after it has refreshed. It 
                    // is meant to be a quick fixed
                    router.push('/view-notes');
                    //location.href = "/view-notes";

                } else {

                    toast.error(data.message, {
                        timeout: 5000, // Custom timeout for this toast
                    });

                    //console.log(data.message);
                }
            })
            .catch((error) => {
                console.error('Error:', error);
                loggingInUser.value = false;

                toast.error("Error logging you in. Please try again.", {
                    timeout: 5000, // Custom timeout for this toast
                });

                //console.log(error);
            });
        }
    };
</script>

<template>
    <main class="page auth-page">
        <h1>Login</h1>
        <form class="auth-form" @submit.prevent="loginUser">
            <div class="form-group">
                <input
                    type="email"
                    class="form-input"
                    :class="{ 'is-invalid': errors.email }"
                    placeholder="Email"
                    v-model="form.email"
                    
                />
                <small v-if="errors.email" class="error-message">{{ errors.email }}</small>
            </div>
            <div class="form-group">
                <input
                    type="password"
                    class="form-input"
                    :class="{ 'is-invalid': errors.password }"
                    placeholder="Password"
                    v-model="form.password"
                    
                />
                <small v-if="errors.password" class="error-message">{{ errors.password }}</small>
            </div>
            <button type="submit" class="btn btn-primary" :disabled="loggingInUser">
                <span v-if="!loggingInUser">Login</span>
                <span v-else>Logging in... <font-awesome-icon icon="spinner" class="fa-spin" size="lg" /></span>
            </button>
            <p>
                Don't have an account? <RouterLink to="/register" class="link">Register</RouterLink>
            </p>
            <p>
                <small>
                    Forgot password? <RouterLink to="/forgot-password" class="link">Reset it</RouterLink>
                </small>
            </p>
        </form>
    </main>
</template>

<style scoped>
    /* Styles for validation */
    .form-group {
        margin-bottom: 1rem;
    }

    .is-invalid {
        border-color: #e74c3c;
        box-shadow: 0 0 5px rgba(231, 76, 60, 0.5);
    }

    .error-message {
        color: #e74c3c;
        font-size: 0.85rem;
        margin-top: 0.25rem;
        text-align: left;
    }

    /* Existing styles */
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
        margin-bottom: 0.5rem;
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
