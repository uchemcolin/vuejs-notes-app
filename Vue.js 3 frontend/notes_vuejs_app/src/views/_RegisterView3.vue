<script setup>
import { reactive, ref } from 'vue';
import { RouterLink, useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';

const authStore = useAuthStore();
const router = useRouter();

let form = reactive({
    name: "",
    email: "",
    password: "",
    confirmPassword: "",
});

let registeringUser = ref(false);

// Validation state
let errors = reactive({
    name: "",
    email: "",
    password: "",
    confirmPassword: "",
});

const validateEmail = (email) =>
    /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);

const validateForm = () => {
    let isValid = true;

    // Validate name
    if (form.name.trim() === "") {
        errors.name = "Name is required.";
        isValid = false;
    } else {
        errors.name = "";
    }

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
    } else if (form.password.length < 8) {
        errors.password = "Password must be at least 8 characters.";
        isValid = false;
    } else {
        errors.password = "";
    }

    // Validate confirm password
    if (form.confirmPassword.trim() === "") {
        errors.confirmPassword = "Please confirm your password.";
        isValid = false;
    } else if (form.confirmPassword !== form.password) {
        errors.confirmPassword = "Passwords do not match.";
        isValid = false;
    } else {
        errors.confirmPassword = "";
    }

    return isValid;
};

const registerUser = async () => {
    if (validateForm()) {
        const url = '/api/register';
        const data = form;

        registeringUser.value = true;

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data),
        })
            .then((response) => {
                if (!response.ok) {
                    alert("Network response was not okay. Please try again.");
                }

                registeringUser.value = false;
                return response.json();
            })
            .then((data) => {
                registeringUser.value = false;

                if (data.type === "success") {
                    alert("Registration successful! Please login.");
                    router.push('/login');
                } else {
                    alert(data.message);
                }
            })
            .catch((error) => {
                console.error('Error:', error);
                registeringUser.value = false;
                alert("Error registering you. Please try again.");
            });
    }
};
</script>

<template>
<main class="page auth-page">
    <h1>Register</h1>
    <form class="auth-form" @submit.prevent="registerUser">
        <div class="form-group">
            <input
                type="text"
                class="form-input"
                :class="{ 'is-invalid': errors.name }"
                placeholder="Name"
                v-model="form.name"
            />
            <small v-if="errors.name" class="error-message">{{ errors.name }}</small>
        </div>
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
        <div class="form-group">
            <input
                type="password"
                class="form-input"
                :class="{ 'is-invalid': errors.confirmPassword }"
                placeholder="Confirm Password"
                v-model="form.confirmPassword"
            />
            <small v-if="errors.confirmPassword" class="error-message">{{ errors.confirmPassword }}</small>
        </div>
        <button type="submit" class="btn btn-primary" :disabled="registeringUser">
            <span v-if="!registeringUser">Register</span>
            <span v-else>Registering... <font-awesome-icon icon="spinner" class="fa-spin" size="lg" /></span>
        </button>
        <p>
            Already have an account? <RouterLink to="/login" class="link">Login</RouterLink>
        </p>
    </form>
</main>
</template>

<style scoped>
/* Reuse styles from LoginView */
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
