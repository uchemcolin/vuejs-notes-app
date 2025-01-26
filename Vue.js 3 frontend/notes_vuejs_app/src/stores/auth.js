// src/stores/auth.js
import { defineStore } from 'pinia';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,         // Stores the logged-in user's data
        isLoggedIn: false,  // Indicates whether the user is logged in
        //token: null, // Stores the logged in user token
        //tokenExpiry: null, // Stores the logged in user token expiry
        token: localStorage.getItem('authToken') || null,
        tokenExpiry: localStorage.getItem('tokenExpiry') || null,
    }),

    actions: {
        /**
         * Set the user data after successful login
         * @param {Object|null} userData - User object or null if logging out
         */
        setUser(userData) {
            this.user = userData;
            this.isLoggedIn = !!userData; // True if userData exists, false otherwise
        },

        /**
         * Set the login state explicitly
         * @param {boolean} status - Login status
         */
        setIsLoggedIn(status) {
            this.isLoggedIn = status;
        },
        setToken(token) {
            this.token = token;
        },
        setTokenExpiry(tokenExpiry) {
            this.tokenExpiry = tokenExpiry;
        },
    },

    //getters: {
        /**
         * Returns the user's name or a default placeholder if no user is logged in
         */
        //userName: (state) => (state.user ? state.user.name : 'Guest'),
    //},
});