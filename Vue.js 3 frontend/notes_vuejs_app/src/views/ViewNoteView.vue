<script setup>
  import { onMounted } from 'vue';
  import { ref } from 'vue';
  import { useRoute, useRouter } from 'vue-router';
  import { RouterLink } from 'vue-router';
  import { useToast } from 'vue-toastification';
  import loadingGif from '@/assets/images/Loading_icon_cropped.gif';
  import dayjs from 'dayjs'; // Import dayjs
  import config from "@/my_javascript_files/config";

  const apiUrl = config.apiBaseUrl;

  const router = useRouter();
  const route = useRoute();
  const toast = useToast(); // Get the toast instance

  const noteId = route.params.id;
  let note = ref({});
  let tryingToLoadNote = ref(true);
  let showLoadingErrorMessage = ref(false);

  let reloadPage = () => {
    location.reload();
  }

  // On mounted, fetch note details
  onMounted(() => {

      const token = localStorage.getItem('authToken');
      const tokenExpiry = localStorage.getItem('tokenExpiry');

      if (token && tokenExpiry > Date.now()) {
          tryingToLoadNote.value = true;
          //const url = `/api/notes/view_note/${noteId}`;
          const url = `${apiUrl}/notes/view_note/${noteId}`;

          fetch(url, {
          method: 'GET',
          headers: {
              'Content-Type': 'application/json',
              'Authorization': `Bearer ${token}`,
          },
          })
          .then((response) => {
              if (!response.ok) {
                  //alert('Error getting the note. Please try again');

                  showLoadingErrorMessage.value = true;
              }
              return response.json();
          })
          .then((data) => {
              note.value = data['note'];

              // Format the created_at date here using dayjs
              if (note.value && note.value.created_at) {
                  note.value.formattedCreatedAt = dayjs(note.value.created_at).format('MMMM D, YYYY h:mm A');
              }

              // Format the created_at date here using dayjs
              if (note.value && note.value.updated_at) {
                  note.value.formattedUpdatedAt = dayjs(note.value.updated_at).format('MMMM D, YYYY h:mm A');
              }

              tryingToLoadNote.value = false;
          })
          .catch((error) => {
              console.error('Error:', error);

              showLoadingErrorMessage.value = true;
          });
      }
  });

  // Delete note function remains unchanged
  let deleteNote = async () => {
    let result = confirm('Are you sure you want to delete this item?');

    if (result) {
      const token = localStorage.getItem('authToken');
      const tokenExpiry = localStorage.getItem('tokenExpiry');

      if (token && tokenExpiry > Date.now()) {
        //const url = `/api/notes/${noteId}`;
        const url = `${apiUrl}/notes/${noteId}`;

        fetch(url, {
          method: 'DELETE',
          headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`,
          },
        })
          .then((response) => {
            if (!response.ok) {
              alert('Error in deleting the note. Please try again');
            }
            return response.json();
          })
          .then((data) => {
            let message = data['message'];
            if (message.type === 'success') {
              toast.success(message.text, {
                timeout: 3000, // Custom timeout for this toast
              });
              router.push('/view-notes');
            } else {
              alert('There was an error in deleting the note. Please try again.');
            }
          })
          .catch((error) => {
            console.error('Error:', error);
            alert('An error has occurred. Please try again.');
          });
      }
    }
  };
</script>

<template>
    <main class="page view-note">
        <div class="note-container" v-if="!tryingToLoadNote && note !== null">
            <h1 class="note-title align-left">{{ note.title }}</h1>
            <p class="note-content align-left">{{ note.content }}</p>
            <p class="note-date">Last Updated: {{ note.formattedUpdatedAt || 'Date not available' }}</p>
            <p class="note-date">Created on: {{ note.formattedCreatedAt || 'Date not available' }}</p>
            <div class="note-actions">
                <RouterLink :to="`/edit-note/${note.id}`" class="btn btn-secondary">Edit Note</RouterLink>
                <button class="btn btn-danger" @click="deleteNote">Delete Note</button>
            </div>
        </div>
        <div class="note-container" v-else-if="!tryingToLoadNote && note === null">
            <p class="note-content">Note does not exist</p>
        </div>
        <div class="note-container" v-else>
            
            <div class="note-content" v-if="showLoadingErrorMessage == false">
                <img :src="loadingGif" />
            </div>
            <div class="note-content" v-else="showLoadingErrorMessage == true">
                <div class="error-section">
                    <div class="error-message">
                        <strong>Error loading the note. Please try again.</strong>
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
                        <RouterLink :to="router.currentRoute.value.fullPath" class="reload-link">Reload</RouterLink>
                    </div>
                </div>
            </div>
        </div>
    </main>
</template>

<style scoped>
  /* Some paragraphs I want left aligned */
  .align-left {
    text-align: left;
  }
</style>
  