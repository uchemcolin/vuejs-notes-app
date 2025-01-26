<script setup>
    import { RouterLink } from 'vue-router';
</script>

<template>

    <main class="page not-found">
        <h1>404</h1>
        <p>Oops! The page you are looking for doesn't exist.</p>
        <RouterLink to="/" class="btn btn-primary">Back to Home</RouterLink>
    </main>
</template>