<script setup>
    import { RouterLink } from 'vue-router';
    import { useRouter } from 'vue-router';
    import { reactive, ref } from "vue";
    import { useToast } from 'vue-toastification';
    import { useAuthStore } from '@/stores/auth';
    import config from "@/my_javascript_files/config";

    const apiUrl = config.apiBaseUrl;

    const authStore = useAuthStore();

    const toast = useToast(); // Get the toast instance

    const router = useRouter();

    let form = reactive(
        {
            firstname: "",
            lastname: "",
            email: "",
            password: "",
            password_confirmation: ""
        }
    );

    let creatingUserAccount = ref(false);

    // Validation state
    let errors = reactive({
        firstname: "",
        lastname: "",
        email: "",
        password: "",
        password_confirmation: ""
    });

    const validateEmail = (email) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);

    const validateField = (field) => {
        let isValid = true;

        // Validate individual fields based on which one is being updated
        if (field === "firstname") {
            if (form.firstname.trim() === "") {
                errors.firstname = "First Name is required.";
                isValid = false;
            } else if (form.firstname.length < 3 || form.firstname.length > 30) {
                errors.firstname = "Please the first name should be between 3 and 30 characters in length.";
                isValid = false;
            } else {
                errors.firstname = "";
            }
        }

        if (field === "lastname") {
            if (form.lastname.trim() === "") {
                errors.lastname = "Last Name is required.";
                isValid = false;
            } else if (form.lastname.length < 3 || form.lastname.length > 30) {
                errors.lastname = "Please the last name should be between 3 and 30 characters in length.";
                isValid = false;
            } else {
                errors.lastname = "";
            }
        }

        if (field === "email") {
            if (form.email.trim() === "") {
                errors.email = "Email is required.";
                isValid = false;
            } else if (form.email.length > 255) {
                errors.email = "Please the email should not be more than 255 characters in length.";
                isValid = false;
            } else if (!validateEmail(form.email)) {
                errors.email = "Please enter a valid email address.";
                isValid = false;
            } else {
                errors.email = "";
            }
        }

        if (field === "password") {
            if (form.password.trim() === "") {
                errors.password = "Password is required.";
                isValid = false;
            } else if (form.password.length < 6) {
                errors.password = "Password must be at least 6 characters.";
                isValid = false;
            } else {
                errors.password = "";
            }
        }

        if (field === "password_confirmation") {
            if (form.password_confirmation.trim() === "") {
                errors.password_confirmation = "Password confirmation is required.";
                isValid = false;
            } else if (form.password_confirmation !== form.password) {
                errors.password_confirmation = "Password confirmation must match the password.";
                isValid = false;
            } else {
                errors.password_confirmation = "";
            }
        }

        return isValid;
    };

    const validateForm = () => {
        let isValid = true;

        // Validate firstname
        if (form.firstname.trim() === "") {
            errors.firstname = "First Name is required.";
            isValid = false;
        } else if (form.firstname.length < 3 || form.firstname.length > 30) {
            errors.firstname = "Please the first name should be between 3 and 30 characters in length.";
            isValid = false;
        } else {
            errors.firstname = "";
        }

        // Validate lastname
        if (form.lastname.trim() === "") {
            errors.lastname = "Last Name is required.";
            isValid = false;
        } else if (form.lastname.length < 3 || form.lastname.length > 30) {
            errors.lastname = "Please the last name should be between 3 and 30 characters in length.";
            isValid = false;
        } else {
            errors.lastname = "";
        }

        // Validate email
        if (form.email.trim() === "") {
            errors.email = "Email is required.";
            isValid = false;
        } else if (form.email.length > 255) {
            errors.email = "Please the email should not be more than 255 characters in length.";
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
        } else if (form.password.length < 6) {
            errors.password = "Password must be at least 6 characters.";
            isValid = false;
        } else {
            errors.password = "";
        }

        // Validate password_confirmation
        if (form.password_confirmation.trim() === "") {
            errors.password_confirmation = "Password confirmation is required.";
            isValid = false;
        } else if (form.password_confirmation !== form.password) {
            errors.password_confirmation = "Password confirmation must match the password.";
            isValid = false;
        } else {
            errors.password_confirmation = "";
        }

        return isValid;
    };
    
    // register user
    let registerUser = async () => {

        if (validateForm()) {
            //const url = '/api/register';
            const url = `${apiUrl}/register`;
            const data = form;

            creatingUserAccount.value = true;

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => {
                if (!response.ok) {

                    creatingUserAccount.value = false;

                }

                return response.json();
            })
            .then(data => {
                //console.log('Success:', data);

                creatingUserAccount.value = false;

                if(data["message"]["type"] == "success") {
                    let user = data["user"];
                    let token = data["message"]["token"];

                    authStore.setIsLoggedIn(true);

                    // Store the token in local storage with a 1-day expiration
                    localStorage.setItem('authToken', token);
                    localStorage.setItem('tokenExpiry', Date.now() + 24 * 60 * 60 * 1000); // 1 day in milliseconds

                    // Redirect to the '/view-notes' route
                    router.push('/view-notes');
                } else {

                    toast.error(data["message"]["text"], {
                        timeout: 5000, // Custom timeout for this toast
                    });
                }                
            })
            .catch(error => {
                console.error('Error:', error);

                creatingUserAccount.value = false;

                /*toast.error("There was an error in creating your account. Please try again", {
                    timeout: 5000, // Custom timeout for this toast
                });*/

                toast.error(error, {
                    timeout: 5000, // Custom timeout for this toast
                });
            });
        }
    }
</script>

<template>

    <main class="page auth-page">
        <h1>Register</h1>
        <form class="auth-form" @submit.prevent="registerUser">
            <div class="form-group">
                <input 
                    type="text" 
                    class="form-input" 
                    :class="{ 'is-invalid': errors.firstname }"
                    placeholder="First Name" 
                    v-model="form.firstname" 
                    @input="validateField('firstname')"
                >
                <small v-if="errors.firstname" class="error-message">{{ errors.firstname }}</small>
            </div>
            <div class="form-group">
                <input 
                    type="text" 
                    class="form-input" 
                    :class="{ 'is-invalid': errors.lastname }"
                    placeholder="Last Name" 
                    v-model="form.lastname" 
                    @input="validateField('lastname')"
                    
                >
                <small v-if="errors.lastname" class="error-message">{{ errors.lastname }}</small>
            </div>

            <div class="form-group">
                <input 
                    type="email" 
                    class="form-input" 
                    :class="{ 'is-invalid': errors.email }" 
                    placeholder="Email" 
                    v-model="form.email" 
                    @input="validateField('email')"

                >
                <small v-if="errors.email" class="error-message">{{ errors.email }}</small>
            </div>
            <div class="form-group">
                <input 
                    type="password" 
                    class="form-input" 
                    :class="{ 'is-invalid': errors.password }" 
                    placeholder="Password" 
                    v-model="form.password"
                    @input="validateField('password')"
                >
                <small v-if="errors.password" class="error-message">{{ errors.password }}</small>
            </div>
            <div class="form-group">
                <input 
                    type="password" 
                    class="form-input" 
                    :class="{ 'is-invalid': errors.password_confirmation }" 
                    placeholder="Confirm Password" 
                    v-model="form.password_confirmation" 
                    @input="validateField('password_confirmation')"
                >
                <small v-if="errors.password_confirmation" class="error-message">{{ errors.password_confirmation }}</small>
            </div>
            <button type="submit" class="btn btn-primary" :disabled="creatingUserAccount">
                <span v-if="!creatingUserAccount">Register</span>
                <span v-else>Creating Account... <font-awesome-icon icon="spinner" class="fa-spin" size="lg" /></span>
            </button>
            <p>Already have an account? <RouterLink to="/login" class="link">Login</RouterLink></p>
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