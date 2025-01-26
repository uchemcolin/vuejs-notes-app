<script setup>
    import { onMounted } from 'vue';
    import { ref } from 'vue';
    import { useRoute, useRouter } from "vue-router";
    import { RouterLink } from 'vue-router';
    import { useToast } from 'vue-toastification';
    import loadingGif from "@/assets/images/Loading_icon_cropped.gif";

    const router = useRouter();

    const route = useRoute();

    const toast = useToast(); // Get the toast instance

    const noteId = route.params.id;

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

                    alert("Error getting the note. Please try again");
                }

                return response.json();
            })
            .then(data => {

                note.value = data["note"];

                tryingToLoadNote.value = false;

                //console.log("Note: " + note.value.length);
                console.log("Note: " + note.value.id);

                console.log("tryingToLoad success: " + tryingToLoadNote.value);
            })
            .catch(error => {
                console.error('Error:', error);
                console.log(error);

                //alert(error);
            });
        }/* else {
            // Token is expired or not found, redirect to login
            router.push('/login');
        }*/

        console.log("tryingToLoad end: " + tryingToLoadNote.value);
    });

    // delete new note to the database
    let deleteNote = async () => {

        let result = confirm("Are you sure you want to delete this item?");

        if (result) {
            const token = localStorage.getItem('authToken');
            const tokenExpiry = localStorage.getItem('tokenExpiry');

            console.log(token);

            if (token && tokenExpiry > Date.now()) {
                const url = `/api/notes/${noteId}`;

                fetch(url, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${token}`
                    },
                    //body: JSON.stringify(data)
                })
                .then(response => {
                    if (!response.ok) {
                        //throw new Error('Network response was not ok');

                        alert("Error in deleting the note. Please try again");
                    }

                    return response.json();
                })
                .then(data => {
                    console.log('Success:', data);

                    let message = data["message"];
                    let text = data["text"];

                    if(message.type == "success") {

                        toast.success(message.text, {
                            timeout: 3000, // Custom timeout for this toast
                        });

                        // Redirect to the '/view-notes' route
                        router.push('/view-notes');
                    } else {
                        alert("There was an error in deleting the note. Please try again.")
                    }
                    
                })
                .catch(error => {
                    console.error('Error:', error);

                    alert("An error has occured. Please try again.")
                });
            }
        }/* else {
            console.log("Action canceled.");
        }*/

        
    }
</script>

<template>

    <main class="page view-note">
        <div class="note-container" v-if="tryingToLoadNote == false && note !== null">
            <h1 class="note-title">{{ note.title }}</h1>
            <p class="note-date">Created on: December 10, 2024</p>
            <p class="note-content">
                {{ note.content }}
            </p>
            <div class="note-actions">
                <RouterLink :to="`/edit-note/${note.id}`" class="btn btn-secondary">Edit Note</RouterLink>
                <button class="btn btn-danger" @click="deleteNote(note.id)">Delete Note</button>
            </div>
        </div>
        <div class="note-container" v-else-if="tryingToLoadNote == false && note === null">
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