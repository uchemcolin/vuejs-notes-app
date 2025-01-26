<script setup>
    import { onMounted } from 'vue';
    import { ref } from 'vue';
    import { useRouter } from "vue-router";
    import { useToast } from 'vue-toastification';
    import { reactive } from 'vue';
    import loadingGif from "@/assets/images/Loading_icon_cropped.gif";
    import config from "@/my_javascript_files/config";

    const apiUrl = config.apiBaseUrl;

    const router = useRouter();

    const toast = useToast(); // Get the toast instance

    let userDetails = ref({});

    let tryingToLoadUserDetails = ref(true);

    let showLoadingErrorMessage = ref(false);

    let updatingProfileName = ref(false);
    let updatingProfileEmail = ref(false);
    let updatingProfilePassword = ref(false);

    let editNameForm = reactive(
        {
            firstname: "",
            lastname: ""
        }
    );

    let editEmailForm = reactive(
        {
            email: ""
        }
    );

    let editPasswordForm = reactive(
        {
            old_password: "",
            password: "",
            password_confirmation: ""
        }
    );

    let reloadPage = () => {
        location.reload();
    }

    onMounted(() => {
        const token = localStorage.getItem('authToken');
        const tokenExpiry = localStorage.getItem('tokenExpiry');

        //console.log(token);

        if (token && tokenExpiry > Date.now()) {

            tryingToLoadUserDetails.value = true;

            //const url = `/api/users/profile`;
            const url = `${apiUrl}/users/profile`;

            fetch(url, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
            })
            .then(response => {
                if (!response.ok) {

                    showLoadingErrorMessage.value = true;
                }

                return response.json();
            })
            .then(data => {

                userDetails.value = data["user"];

                tryingToLoadUserDetails.value = false;

                editNameForm.firstname = userDetails.value.firstname;
                editNameForm.lastname = userDetails.value.lastname;

                editEmailForm.email = userDetails.value.email;

                //console.log("User Details: " + userDetails.value);

                //console.log("tryingToLoad success: " + tryingToLoadUserDetails.value);
            })
            .catch(error => {
                //console.error('Error:', error);
                //console.log(error);

                showLoadingErrorMessage.value = true;
            });
        }

        //console.log("tryingToLoad end: " + tryingToLoadUserDetails.value);
    });

    // Validation state
    let errors = reactive({
        firstname: "",
        lastname: "",
        email: "",
        old_password: "",
        password: "",
        new_password: ""
    });
    
    const validateField = (field) => {
        let isValid = true;

        // Validate individual fields based on which one is being updated
        if (field === "firstname") {
            if (editNameForm.firstname.trim() === "") {
                errors.firstname = "First Name is required.";
                isValid = false;
            } else if (editNameForm.firstname.length < 3 || editNameForm.firstname.length > 30) {
                errors.firstname = "Please the first name must be between 3 to 30 characters.";
                isValid = false;
            } else {
                errors.firstname = "";
            }
        }

        if (field === "lastname") {
            // Validate last name
            if (editNameForm.lastname.trim() === "") {
                errors.lastname = "Last Name is required.";
                isValid = false;
            } else if (editNameForm.lastname.length < 3 || editNameForm.lastname.length > 30) {
                errors.lastname = "Please the last name must be between 3 to 30 characters.";
                isValid = false;
            } else {
                errors.lastname = "";
            }
        }

        if (field === "email") {
            if (editEmailForm.email.trim() === "") {
                errors.email = "Email is required.";
                isValid = false;
            } else if (editEmailForm.email.length > 255) {
                errors.email = "Please the email should not be more than 255 characters in length.";
                isValid = false;
            } else if (!validateEmail(editEmailForm.email)) {
                errors.email = "Please enter a valid email address.";
                isValid = false;
            } else {
                errors.email = "";
            }
        }

        if (field === "old_password") {
            // Validate old password
            if (editPasswordForm.old_password.trim() === "") {
                errors.old_password = "Old Password is required.";
                isValid = false;
            } else {
                errors.old_password = "";
            }
        }

        if (field === "password") {
            // Validate password
            if (editPasswordForm.password.trim() === "") {
                errors.password = "Password is required.";
                isValid = false;
            } else if (editPasswordForm.password.length < 6) {
                errors.password = "Password must be at least 6 characters.";
                isValid = false;
            } else {
                errors.password = "";
            }
        }

        if (field === "password_confirmation") {
            // Validate password_confirmation
            if (editPasswordForm.password_confirmation.trim() === "") {
                errors.password_confirmation = "Password confirmation is required.";
                isValid = false;
            } else if (editPasswordForm.password_confirmation !== editPasswordForm.password) {
                errors.password_confirmation = "Password confirmation must match the password.";
                isValid = false;
            } else {
                errors.password_confirmation = "";
            }
        }

        return isValid;
    };

    // To validate the update name form
    const validateUpdateNameForm = () => {
        let isValid = true;

        // Validate first name
        if (editNameForm.firstname.trim() === "") {
            errors.firstname = "First Name is required.";
            isValid = false;
        } else if (editNameForm.firstname.length < 3 || editNameForm.firstname.length > 30) {
            errors.firstname = "Please the first name must be between 3 to 30 characters.";
            isValid = false;
        } else {
            errors.firstname = "";
        }

        // Validate last name
        if (editNameForm.lastname.trim() === "") {
            errors.lastname = "Last Name is required.";
            isValid = false;
        } else if (editNameForm.lastname.length < 3 || editNameForm.lastname.length > 30) {
            errors.lastname = "Please the last name must be between 3 to 30 characters.";
            isValid = false;
        } else {
            errors.lastname = "";
        }

        return isValid;
    };

    // To validate the update email form
    const validateUpdateEmailForm = () => {
        let isValid = true;

        // Validate email
        if (editEmailForm.email.trim() === "") {
            errors.email = "Email is required.";
            isValid = false;
        } else if (editEmailForm.email.length > 255) {
            errors.email = "Please the email should not be more than 255 characters in length.";
            isValid = false;
        } else if (!validateEmail(editEmailForm.email)) {
            errors.email = "Please enter a valid email address.";
            isValid = false;
        } else {
            errors.email = "";
        }

        return isValid;
    };

    // To validate the update password form
    const validateUpdatePasswordForm = () => {
        let isValid = true;

        // Validate old password
        if (editPasswordForm.old_password.trim() === "") {
            errors.old_password = "Old Password is required.";
            isValid = false;
        } else {
            errors.password = "";
        }

        // Validate password
        if (editPasswordForm.password.trim() === "") {
            errors.password = "Password is required.";
            isValid = false;
        } else if (editPasswordForm.password.length < 6) {
            errors.password = "Password must be at least 6 characters.";
            isValid = false;
        } else {
            errors.password = "";
        }

        // Validate password_confirmation
        if (editPasswordForm.password_confirmation.trim() === "") {
            errors.password_confirmation = "Password confirmation is required.";
            isValid = false;
        } else if (editPasswordForm.password_confirmation !== editPasswordForm.password) {
            errors.password_confirmation = "Password confirmation must match the password.";
            isValid = false;
        } else {
            errors.password_confirmation = "";
        }

        return isValid;
    };

    // update profile name to the database
    let updateProfileName = async () => {

        if (validateUpdateNameForm()) {
            const token = localStorage.getItem('authToken');
            const tokenExpiry = localStorage.getItem('tokenExpiry');

            //console.log(token);

            updatingProfileName.value = true;

            if (token && tokenExpiry > Date.now()) {
                const url = `/api/users/update_profile_name`;
                const data = editNameForm;

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${token}`
                    },
                    body: JSON.stringify(data)
                })
                .then(response => {
                    if (!response.ok) {
                        //throw new Error('Network response was not ok');

                        //alert("Error in updating your name. Please try again");

                        //alert("Network response was not okay. Please try again");
                    }

                    updatingProfileName.value = false;

                    return response.json();
                })
                .then(data => {
                    //console.log('Success:', data);

                    let message = data["message"];
                    let text = data["text"];

                    if(message.type == "success") {

                        toast.success(message.text, {
                            timeout: 3000, // Custom timeout for this toast
                        });

                        updatingProfileName.value = false;

                        // Redirect to the '/view-profile' route
                        router.push('/view-profile');
                    } else {

                        updatingProfileName.value = false;

                        toast.error("There was an error in updating your name. Please try again.", {
                            timeout: 5000, // Custom timeout for this toast
                        });
                    }
                    
                })
                .catch(error => {
                    console.error('Error:', error);

                    updatingProfileName.value = false;

                    toast.error("An error has occured. Please try again.", {
                        timeout: 5000, // Custom timeout for this toast
                    });
                });
            }
        }
    }

    // update profile email to the database
    let updateProfileEmail = async () => {

        if (validateUpdateEmailForm()) {
            const token = localStorage.getItem('authToken');
            const tokenExpiry = localStorage.getItem('tokenExpiry');

            //console.log(token);

            updatingProfileEmail.value = true;

            if (token && tokenExpiry > Date.now()) {
                const url = `/api/users/update_profile_email`;
                const data = editEmailForm;

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${token}`
                    },
                    body: JSON.stringify(data)
                })
                .then(response => {
                    if (!response.ok) {
                        //throw new Error('Network response was not ok');

                        //alert("Error in updating your email. Please try again");

                        //alert("Network response was not okay. Please try again");
                    }

                    updatingProfileEmail.value = false;

                    return response.json();
                })
                .then(data => {
                    //console.log('Success:', data);

                    let message = data["message"];
                    let text = data["text"];

                    if(message.type == "success") {

                        toast.success(message.text, {
                            timeout: 3000, // Custom timeout for this toast
                        });

                        updatingProfileEmail.value = false;

                        // Redirect to the '/view-profile' route
                        router.push('/view-profile');
                    } else {

                        updatingProfileEmail.value = false;

                        toast.error("There was an error in updating your email. Please try again.", {
                            timeout: 5000, // Custom timeout for this toast
                        });
                    }
                    
                })
                .catch(error => {
                    console.error('Error:', error);

                    updatingProfileEmail.value = false;

                    toast.error("An error has occured. Please try again.", {
                        timeout: 5000, // Custom timeout for this toast
                    });
                });
            }
        }
    }

    // update profile password to the database
    let updateProfilePassword = async () => {

        if (validateUpdatePasswordForm()) {
            const token = localStorage.getItem('authToken');
            const tokenExpiry = localStorage.getItem('tokenExpiry');

            //console.log(token);

            updatingProfilePassword.value = true;

            if (token && tokenExpiry > Date.now()) {
                const url = `/api/users/update_profile_password`;
                const data = editPasswordForm;

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${token}`
                    },
                    body: JSON.stringify(data)
                })
                .then(response => {
                    if (!response.ok) {
                        //throw new Error('Network response was not ok');

                        //alert("Error in updating your password. Please try again");

                        //alert("Network response was not okay. Please try again.");
                    }

                    updatingProfilePassword.value = false;

                    return response.json();
                })
                .then(data => {
                    //console.log('Success:', data);

                    let message = data["message"];
                    let text = data["text"];

                    if(message.type == "success") {

                        toast.success(message.text, {
                            timeout: 3000, // Custom timeout for this toast
                        });

                        updatingProfilePassword.value = false;

                        // Redirect to the '/view-profile' route
                        router.push('/view-profile');
                    } else {

                        toast.error("There was an error in updating your password. Please try again.", {
                            timeout: 5000, // Custom timeout for this toast
                        });
                    }
                    
                })
                .catch(error => {
                    console.error('Error:', error);

                    updatingProfilePassword.value = false;

                    toast.error("An error has occured. Please try again.", {
                        timeout: 5000, // Custom timeout for this toast
                    });
                });
            }
        }
    }
</script>

<template>

    <div class="edit-profile-container" v-if="tryingToLoadUserDetails == false && userDetails !== null">
        <h2>Edit Profile</h2>
        <form class="edit-profile-form" @submit.prevent="updateProfileName">
            <div class="form-group">
                <label for="name">First Name</label>
                <input 
                    type="text" 
                    id="firstname" 
                    name="firstname" 
                    class="form-input" 
                    :class="{ 'is-invalid': errors.firstname }" 
                    placeholder="Enter your first name" 
                    v-model="editNameForm.firstname" 
                    @input="validateField('firstname')"
                >
                <small v-if="errors.firstname" class="error-message">{{ errors.firstname }}</small>
            </div>
            <div class="form-group">
                <label for="name">Last Name</label>
                <input 
                    type="text" 
                    id="lastname" 
                    name="lastname" 
                    class="form-input" 
                    :class="{ 'is-invalid': errors.lastname }" 
                    placeholder="Enter your last name" 
                    v-model="editNameForm.lastname" 
                    @input="validateField('lastname')"
                >
                <small v-if="errors.lastname" class="error-message">{{ errors.lastname }}</small>
            </div>
            <button type="submit" class="btn-save-profile" :disabled="(updatingProfileName || updatingProfileEmail || updatingProfilePassword)">
                <span v-if="!updatingProfileName">Save Changes</span>
                <span v-else>Saving Changes... <font-awesome-icon icon="spinner" class="fa-spin" size="lg" /></span>
            </button>
        </form>
        <br/>
        <hr/>
        <br/>
        <form class="edit-profile-form" @submit.prevent="updateProfileEmail">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    class="form-input" 
                    :class="{ 'is-invalid': errors.email }" 
                    placeholder="Enter your email address" 
                    v-model="editEmailForm.email" 
                    @input="validateField('email')"

                >
                <small v-if="errors.email" class="error-message">{{ errors.email }}</small>
            </div>
            <button type="submit" class="btn-save-profile" :disabled="(updatingProfileName || updatingProfileEmail || updatingProfilePassword)">
                <span v-if="!updatingProfileEmail">Save Changes</span>
                <span v-else>Saving Changes... <font-awesome-icon icon="spinner" class="fa-spin" size="lg" /></span>
            </button>
        </form>
        <br/>
        <hr/>
        <br/>
        <form class="edit-profile-form" @submit.prevent="updateProfilePassword">
            <div class="form-group">
                <label for="old_password">Old Password</label>
                <input 
                    type="password" 
                    id="old-password" 
                    name="old_password" 
                    class="form-input" 
                    :class="{ 'is-invalid': errors.old_password }" 
                    placeholder="Enter your password" 
                    v-model="editPasswordForm.old_password" 
                    @input="validateField('old_password')"
                >
                <small v-if="errors.old_password" class="error-message">{{ errors.old_password }}</small>
            </div>
            <div class="form-group">
                <label for="password">New Password</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    class="form-input" 
                    :class="{ 'is-invalid': errors.password }" 
                    placeholder="Enter the new password" 
                    v-model="editPasswordForm.password" 
                    @input="validateField('password')"
                >
                <small v-if="errors.password" class="error-message">{{ errors.password }}</small>
            </div>
            <div class="form-group">
                <label for="password_confirmation">Confirm New Password</label>
                <input 
                    type="password" 
                    id="password_confirmation" 
                    name="password_confirmation" 
                    class="form-input" 
                    :class="{ 'is-invalid': errors.password_confirmation }" 
                    placeholder="Confirm the new password" 
                    v-model="editPasswordForm.password_confirmation" 
                    @input="validateField('password_confirmation')"
                >
                <small v-if="errors.password_confirmation" class="error-message">{{ errors.password_confirmation }}</small>
            </div>
            <button type="submit" class="btn-save-profile" :disabled="(updatingProfileName || updatingProfileEmail || updatingProfilePassword)">
                <span v-if="!updatingProfilePassword">Save Changes</span>
                <span v-else>Saving Changes... <font-awesome-icon icon="spinner" class="fa-spin" size="lg" /></span>
            </button>
        </form>
    </div>
    <div class="profile-container" v-else-if="tryingToLoadUserDetails == false && userDetails === null">
        <p class="profile-name">
            Your account does not exist
        </p>
    </div>
    <div class="profile-container" v-else>
        <div v-if="showLoadingErrorMessage == false">
            <img :src="loadingGif" />
        </div>
        <div v-else="showLoadingErrorMessage == true" class="error-section">
            <div class="error-message">
              <strong>Error loading your profile. Please try again.</strong>
            </div>
            <div>
              <!--<a href="javascript:void(0)" @click="reloadPage" class="reload-link">
                Reload
              </a>-->
              <RouterLink :to="router.currentRoute.value.fullPath" class="reload-link">Reload</RouterLink>
            </div>
          </div>
    </div>
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
</style>