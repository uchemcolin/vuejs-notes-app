import { defineStore } from 'pinia';
import { useRouter } from 'vue-router';

const router = useRouter();

export const useAuthStore = defineStore('auth', {
  state: () => ({
    isLoggedIn: false, 
    user: null 
  }),
  actions: {
    async loginUser(credentials) {
      const url = '/api/login';
      const data = credentials;

      fetch(url, {
          method: 'POST',
          headers: {
              'Content-Type': 'application/json'
          },
          body: JSON.stringify(data)
      })
      .then(response => {
          if (!response.ok) {
              throw new Error("Error logging you in. Please try again");

              //alert("Error logging you in. Please try again");
          }

          return response.json();
      })
      .then(data => {
          console.log('Success:', data);

          let token = data["token"];
          let user = data["user"];

          // Store the token in local storage with a 1-day expiration
          localStorage.setItem('authToken', token);
          localStorage.setItem('tokenExpiry', Date.now() + 24 * 60 * 60 * 1000); // 1 day in milliseconds

          //authStore.setUser(user);
          //this.setUser(user);

          this.isLoggedIn = true; // Set isLoggedIn to true here
          this.user = data.user;

          // Redirect to the '/view-notes' route
          router.push('/view-notes');
      })
      .catch(error => {
          //console.error('Error:', error);

          throw new Error('Error: ' + error);
      });
    },
    setUser(user) {
      //this.isLoggedIn = !!user; // Assuming user object exists if logged in
      this.isLoggedIn = true;
      this.user = user;
    },
    logout() {
      this.isLoggedIn = false;
      this.user = null;
    }
  }
});