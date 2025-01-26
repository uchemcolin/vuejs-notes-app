import { defineStore } from 'pinia';

export const useAuthStore = defineStore('auth', {
  state: () => ({
    isLoggedIn: false, 
    user: null 
  }),
  actions: {
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