<script setup>
    import { onMounted } from 'vue';
    import { ref } from 'vue';
    import { RouterLink, useRouter } from 'vue-router';
    import loadingGif from "@/assets/images/Loading_icon_cropped.gif";
    import config from "@/my_javascript_files/config";

    const apiUrl = config.apiBaseUrl;

    const router = useRouter();

    let userDetails = ref({});

    let tryingToLoadUserDetails = ref(true);

    let showLoadingErrorMessage = ref(false);

    let reloadPage = () => {
        location.reload();
    }

    // In your main App.vue or a global mixin:
    onMounted(() => {
        const token = localStorage.getItem('authToken');
        const tokenExpiry = localStorage.getItem('tokenExpiry');

        console.log(token);

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
                //body: JSON.stringify(data)
            })
            .then(response => {
                if (!response.ok) {
                    //throw new Error('Network response was not ok');

                    //alert("Error getting your account details. Please try again");

                    showLoadingErrorMessage.value = true;
                }

                return response.json();
            })
            .then(data => {

                userDetails.value = data["user"];

                console.log("userdetails: " + userDetails);

                tryingToLoadUserDetails.value = false;

                //console.log("Note: " + user.value.length);
                //console.log("User Details: " + userDetails.value.id);

                //console.log("tryingToLoad success: " + tryingToLoadUserDetails.value);
            })
            .catch(error => {
                //console.error('Error:', error);
                //console.log(error);

                //alert(error);

                showLoadingErrorMessage.value = true;
            });
        }/* else {
            // Token is expired or not found, redirect to login
            router.push('/login');
        }*/

        //console.log("tryingToLoad end: " + tryingToLoadUserDetails.value);
    });
</script>

<template>

    <div class="profile-container" v-if="tryingToLoadUserDetails == false && userDetails !== null">
        <!--<img src="path/to/profile-photo.jpg" alt="Profile Photo" class="profile-photo">-->
        <h3>Your Profile</h3>
        <p class="profile-name">{{ userDetails.firstname }} {{ userDetails.lastname }}</p>
        <p class="profile-email">{{ userDetails.email }}</p>
        <!--<p class="profile-bio">A short bio about John Doe. Passionate about coding, design, and coffee.</p>-->
        <RouterLink to="/edit-profile" class="btn-edit-profile">Edit Profile</RouterLink>
    </div>
    <div class="profile-container" v-else-if="tryingToLoadUserDetails == false && userDetails === null">
        <p class="profile-name">
            Your account does not exist
        </p>
        <!--<a href="#" class="btn-edit-profile">Login</a>-->
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
              <!--<RouterLink 
                :to="router.currentRoute.value.fullPath" 
                class="reload-link">
                Reload
              </RouterLink>-->
              <!--<a href="javascript:void(0)" @click="reloadPage" class="reload-link">
                Reload
              </a>-->
              <!--<RouterLink to="/view-profile" class="reload-link">Reload</RouterLink>-->
              <RouterLink :to="router.currentRoute.value.fullPath" class="reload-link">Reload</RouterLink>
            </div>
        </div>
    </div>
</template>