<script setup>
    import { reactive } from 'vue';
    import { RouterLink } from 'vue-router';
    import { useRouter } from 'vue-router';
    import { ref } from "vue";
    import { useRoute } from "vue-router";
    import { useToast } from 'vue-toastification';
    import config from "@/my_javascript_files/config";

    const apiUrl = config.apiBaseUrl;

    const toast = useToast(); // Get the toast instance

    const router = useRouter();

    const route = useRoute();

    const token = route.params.id;

    let form = reactive(
        {
            token: token,
            email: "",
            password: "",
            password_confirmation: ""
        }
    );

    // Validation state
    let errors = reactive({
        email: "",
        password: "",
        new_password: ""
    });
    
    const validateField = (field) => {
        let isValid = true;

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
        }

        if (field === "password_confirmation") {
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
        }

        return isValid;
    };

    // To validate the reset password form
    const validateResetPasswordForm = () => {
        let isValid = true;

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

    let submittingResetPassword = ref(false);
    
    // login user
    let submitResetPassword = async () => {

        if (validateResetPasswordForm()) {
            //const url = '/api/reset_password';
            const url = `${apiUrl}/reset_password`;
            const data = form;

            submittingResetPassword.value = true;

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

                    //alert("Network response was not okay. Pleaae try again.");
                }

                submittingResetPassword.value = false;

                return response.json();
            })
            .then(data => {
                //console.log('Success:', data);

                submittingResetPassword.value = false;

                if(data["type"] == "success") {

                    toast.success(data["message"], {
                        timeout: 3000, // Custom timeout for this toast
                    });

                    // redirect to the login route
                    router.push("/login");

                } else {

                    toast.error(data["message"], {
                        timeout: 5000, // Custom timeout for this toast
                    });
                }

            })
            .catch(error => {
                console.error('Error:', error);

                submittingResetPassword.value = false;

                toast.error("Error resetting your password. Please try again", {
                    timeout: 5000, // Custom timeout for this toast
                });
            });
        } else {

            submittingResetPassword.value = false;
        }
    }
</script>

<template>

    <main class="page auth-page">
        <h1>Reset Password</h1>
        <form class="auth-form" @submit.prevent="submitResetPassword">
            <input type="hidden" placeholder="Token" v-model="form.token" required>
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
                    @input="validateField('new_password')"
                >
                <small v-if="errors.password_confirmation" class="error-message">{{ errors.password_confirmation }}</small>
            </div>
            <button type="submit" class="btn btn-primary" :disabled="submittingResetPassword">
                <span v-if="!submittingResetPassword">Reset Password</span>
                <span v-else>Resetting Password... <font-awesome-icon icon="spinner" class="fa-spin" size="lg" /></span>
            </button>
            <p>Remember your password? <RouterLink to="/login" class="link">Login</RouterLink></p>
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