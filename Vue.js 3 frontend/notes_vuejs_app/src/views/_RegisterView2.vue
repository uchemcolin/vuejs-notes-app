<script setup>
    import { RouterLink } from 'vue-router';
    import { useRouter } from 'vue-router';
    import { reactive, ref } from "vue";

    const router = useRouter();

    let form = reactive(
        {
            firstname: "",
            lastname: "",
            email: "",
            password: ""
        }
    );

    let creatingUserAccount = ref(false);
    
    // register user
    let registerUser = async () => {

        if(form.firstname.trim() !== "" && form.lastname.trim() !== "" && form.email.trim() !== "" && form.password.trim() !== "" && form.password_confirmation.trim() !== "") {
            const url = '/api/register';
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
                    //throw new Error('Network response was not ok');

                    creatingUserAccount.value = false;

                    //alert("Error creating your account. Please try again");

                    alert("Network response was not okay. Please try again.");
                }

                return response.json();
            })
            .then(data => {
                console.log('Success:', data);

                /*"user" => $user,
                "message" => [
                    "type" => "success",
                    "text" => "Your account has been created and you are now logged in",
                    //"token" => $token->plainTextToken
                    "token" => $token
                ]*/

                creatingUserAccount.value = false;

                if(data["message"]["type"] == "success") {
                    //let token = data["user"]["token"];
                    let user = data["user"];
                    let token = user.token;

                    // Store the token in local storage with a 1-day expiration
                    localStorage.setItem('authToken', token);
                    localStorage.setItem('tokenExpiry', Date.now() + 24 * 60 * 60 * 1000); // 1 day in milliseconds

                    //authStore.setUser(user);

                    // Redirect to the '/view-notes' route
                    router.push('/view-notes');
                } else {
                    alert(data["message"]["text"]);

                    //alert("There was an error in creating your account. Please try again");
                }                
            })
            .catch(error => {
                console.error('Error:', error);

                creatingUserAccount.value = false;

                //alert(data["message"]["text"]);
                
                alert("There was an error in creating your account. Please try again");
            });
        } else {

            creatingUserAccount.value = false;

            alert("Please fill all fields");
        }
    }
</script>

<template>

    <main class="page auth-page">
        <h1>Register</h1>
        <form class="auth-form" @click.prevent="registerUser">
            <input type="text" class="form-input" placeholder="First Name" v-model="form.firstname" required>
            <input type="text" class="form-input" placeholder="Last Name" v-model="form.lastname" required>
            <input type="email" class="form-input" placeholder="Email" v-model="form.email" required>
            <input type="password" class="form-input" placeholder="Password" v-model="form.password" required>
            <input type="password" class="form-input" placeholder="Confirm Password" v-model="form.password_confirmation" required>
            <button type="submit" class="btn btn-primary" :disabled="creatingUserAccount">
                <span v-if="!creatingUserAccount">Register</span>
                <span v-else>Creating Account... <font-awesome-icon icon="spinner" class="fa-spin" size="lg" /></span>
                <!--<font-awesome-icon icon="spinner" class="fa-spin" size="lg" :style="{ color: 'white' }" />-->
            </button>
            <p>Already have an account? <RouterLink to="/login" class="link">Login</RouterLink></p>
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