<script setup>
    import { onMounted } from 'vue';
    import { ref } from 'vue';
    //import { useRouter } from 'vue-router';
    import { useRoute } from "vue-router";
    import loadingGif from "@/assets/images/Loading_icon_cropped.gif";

    const router = useRoute();

    const noteId = router.params.id;

    let note = ref({});

    let tryingToLoadNote = ref(true);

    // In your main App.vue or a global mixin:
    onMounted(() => {
        const token = localStorage.getItem('authToken');
        const tokenExpiry = localStorage.getItem('tokenExpiry');

        console.log(token);

        if (token && tokenExpiry > Date.now()) {

            tryingToLoadNote.value = true;

            const url = `/api/notes/view_note/${noteId}`;
            //const data = form;

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                //body: JSON.stringify(data)
            })
            .then(response => {
                if (!response.ok) {
                    //throw new Error('Network response was not ok');

                    alert("Error getting the note. Please try again");
                }

                return response.json();
            })
            .then(data => {

                note.value = data["note"];

                tryingToLoadNote.value = false;

                console.log("Note: " + note.value.length);
            })
            .catch(error => {
                console.error('Error:', error);
                console.log(error);
            });
        }/* else {
            // Token is expired or not found, redirect to login
            router.push('/login');
        }*/
    });
</script>

<template>

    <main class="page view-notes">
        <div v-if="tryingToLoadNote == false">
            <div class="notes-list" v-if="note.length > 0">
                <div class="note-card">
                    <h3>{{ note.title }}</h3>
                    <p>{{ note.content }}</p>
                </div>
            </div>
            <div class="notes-list" v-else>
                <div class="note-card">
                    <h3>The note does not exist</h3>
                </div>
            </div>
            <!--<div class="notes-list">
                <div class="note-card"  v-for="(note, index) in notes.slice(0, limit || notes.length)" :key="note.index">
                    <h3>{{ note.title }}</h3>
                    <p>{{ note.content }}</p>
                </div>
            </div>-->
        </div>
        <div class="notes-list" v-else>
            <img :src="loadingGif" />
        </div>
    </main>

    <main class="page view-note">
        <div class="note-container">
            <div v-if="tryingToLoadNote == false">
            <div class="notes-list" v-if="note.length > 0">
                <h1 class="note-title">{{ note.title }}</h1>
                <p class="note-date">Created on: December 10, 2024</p>
                <p class="note-content">
                    {{ note.content }}
                </p>
                <div class="note-actions">
                    <a href="edit-note.html" class="btn btn-secondary">Edit Note</a>
                    <button class="btn btn-danger">Delete Note</button>
                </div>
                
                <div class="note-card">
                    <h3>{{ note.title }}</h3>
                    <p>{{ note.content }}</p>
                </div>
            </div>
            <div class="notes-list" v-else>
                <div class="note-card">
                    <h3>The note does not exist</h3>
                </div>
            </div>
            <!--<div class="notes-list">
                <div class="note-card"  v-for="(note, index) in notes.slice(0, limit || notes.length)" :key="note.index">
                    <h3>{{ note.title }}</h3>
                    <p>{{ note.content }}</p>
                </div>
            </div>-->
        </div>
        <div class="notes-list" v-else>
            <img :src="loadingGif" />
        </div>
            <h1 class="note-title">Note Title</h1>
            <p class="note-date">Created on: December 10, 2024</p>
            <p class="note-content">
                This is where the full content of the note will be displayed. It can be multiple paragraphs, and it should be styled beautifully to make it easy to read.
            </p>
            <div class="note-actions">
                <a href="edit-note.html" class="btn btn-secondary">Edit Note</a>
                <button class="btn btn-danger">Delete Note</button>
            </div>
        </div>
    </main>

    <main class="page view-note">
        <div class="note-container" v-if="tryingToLoadNote == false && note.length > 0">
            <h1 class="note-title">Note Title</h1>
            <p class="note-date">Created on: December 10, 2024</p>
            <p class="note-content">
                This is where the full content of the note will be displayed. It can be multiple paragraphs, and it should be styled beautifully to make it easy to read.
            </p>
            <div class="note-actions">
                <a href="edit-note.html" class="btn btn-secondary">Edit Note</a>
                <button class="btn btn-danger">Delete Note</button>
            </div>
        </div>
        <div class="note-container" v-else-if="tryingToLoadNote == false && note.length == 0">
            <!--<h1 class="note-title">Note Title</h1>
            <p class="note-date">Created on: December 10, 2024</p>-->
            <p class="note-content">
                Note does not exist
            </p>
            <!--<div class="note-actions">
                <a href="edit-note.html" class="btn btn-secondary">Edit Note</a>
                <button class="btn btn-danger">Delete Note</button>
            </div>-->
        </div>
        <div class="note-container" v-else>
            <p class="note-content">
                <img :src="loadingGif" />
            </p>
        </div>
    </main>
</template>