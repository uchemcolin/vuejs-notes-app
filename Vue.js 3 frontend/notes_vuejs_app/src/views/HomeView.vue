<script setup>
    import { RouterLink } from 'vue-router';
</script>

<template>

    <header class="hero page">
        <h1>Welcome to NotesApp</h1>
        <p>Organize your thoughts, ideas, and tasks in one place.</p>
        <RouterLink to="/add-note" class="btn btn-primary">Add Your First Note</RouterLink>
    </header>
</template>