<script setup>
    import { reactive } from 'vue';
    import { RouterLink } from 'vue-router';
    import { ref } from "vue";
    import { useToast } from 'vue-toastification';
    import config from "@/my_javascript_files/config";

    const apiUrl = config.apiBaseUrl;

    const toast = useToast(); // Get the toast instance

    let form = reactive(
        {
            email: ""
        }
    );

    // Validation state
    let errors = reactive({
        email: ""
    });

    const validateEmail = (email) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);

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

        return isValid;
    };

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

        return isValid;
    };

    let submittingForgotPassword = ref(false);
    
    // submit forgot password form
    let submitForgotPassword = async () => {

        if (validateForm()) {
            //const url = '/api/forgot_password';
            const url = `${apiUrl}/forgot_password`;
            const data = form;

            submittingForgotPassword.value = true;

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

                submittingForgotPassword.value = false;

                return response.json();
            })
            .then(data => {
                //console.log('Success:', data);

                submittingForgotPassword.value = false;

                if(data["type"] == "success") {

                    toast.success(data["message"], {
                        timeout: 3000, // Custom timeout for this toast
                    });

                } else {

                    toast.error(data["message"], {
                        timeout: 5000, // Custom timeout for this toast
                    });
                }

            })
            .catch(error => {
                console.error('Error:', error);

                submittingForgotPassword.value = false;

                toast.error("Error sending reset link to your email. Please try again", {
                    timeout: 5000, // Custom timeout for this toast
                });
            });
        }
    }
</script>

<template>

    <main class="page auth-page">
        <h1>Forgot Password</h1>
        <form class="auth-form" @submit.prevent="submitForgotPassword">
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
            <button type="submit" class="btn btn-primary" :disabled="submittingForgotPassword">
                <span v-if="!submittingForgotPassword">Send Reset Link</span>
                <span v-else>Sending Reset Link... <font-awesome-icon icon="spinner" class="fa-spin" size="lg" /></span>
                <!--<font-awesome-icon icon="spinner" class="fa-spin" size="lg" :style="{ color: 'white' }" />-->
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