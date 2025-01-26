<script setup>
    import { onMounted } from 'vue';
    import { ref } from 'vue';
    import { useRouter } from 'vue-router';
    import loadingGif from "@/assets/images/Loading_icon_cropped.gif";

    const router = useRouter();

    let notes = ref([]);

    let limit = 10;

    let tryingToLoadNotes = ref(true);

    // In your main App.vue or a global mixin:
    onMounted(() => {
        const token = localStorage.getItem('authToken');
        const tokenExpiry = localStorage.getItem('tokenExpiry');

        console.log(token);

        if (token && tokenExpiry > Date.now()) {

            tryingToLoadNotes.value = true;

            const url = '/api/notes';
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

                    alert("Error getting your notes. Please try again");
                }

                return response.json();
            })
            .then(data => {

                notes.value = data["notes"];

                tryingToLoadNotes.value = false;

                console.log("Notes: " + notes.value.length);
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

    let goToViewNote = (noteToViewId) => {
        // Redirect to the '/view-notes' route
        router.push('/view-note/' + noteToViewId);
    };
</script>

<template>

    <main class="page view-notes">
        <h1>Your Notes</h1>
        <div v-if="tryingToLoadNotes == false">
            <div class="notes-list" v-if="notes.length > 0">
                <div 
                    class="note-card"  
                    v-for="(note, index) in notes.slice(0, limit || notes.length)" :key="note.index" 
                    @click="goToViewNote(note.id)"
                >
                    <h3>{{ note.title }}</h3>
                    <p>{{ note.content }}</p>
                </div>
            </div>
            <div class="notes-list" v-else>
                <div class="note-card">
                    <h3>You currently have no notes</h3>
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
</template>