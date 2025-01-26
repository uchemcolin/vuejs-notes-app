<script setup>
import { onMounted, ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import loadingGif from "@/assets/images/Loading_icon_cropped.gif";

const router = useRouter();

let notes = ref([]);
let tryingToLoadNotes = ref(true);

const currentPage = ref(1);
const notesPerPage = ref(1); // Adjust based on how many notes per page you want

// Calculate total pages
const totalPages = computed(() => Math.ceil(notes.value.length / notesPerPage.value));

// Compute notes for the current page
const paginatedNotes = computed(() => {
  const start = (currentPage.value - 1) * notesPerPage.value;
  const end = start + notesPerPage.value;
  return notes.value.slice(start, end);
});

// Fetch notes on mount
onMounted(() => {
  const token = localStorage.getItem('authToken');
  const tokenExpiry = localStorage.getItem('tokenExpiry');

  if (token && tokenExpiry > Date.now()) {
    tryingToLoadNotes.value = true;

    fetch('/api/notes', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${token}`,
      },
    })
      .then(response => {
        if (!response.ok) throw new Error('Failed to fetch notes');
        return response.json();
      })
      .then(data => {
        notes.value = data.notes || [];
        tryingToLoadNotes.value = false;
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Error loading notes. Please try again.');
      });
  } else {
    router.push('/login');
  }
});

// Navigation methods
const nextPage = () => {
  if (currentPage.value < totalPages.value) currentPage.value++;
};

const prevPage = () => {
  if (currentPage.value > 1) currentPage.value--;
};

const goToViewNote = (noteToViewId) => {
  router.push('/view-note/' + noteToViewId);
};
</script>

<template>
  <main class="page view-notes">
    <h1>Your Notes</h1>
    <div v-if="!tryingToLoadNotes">
      <div class="notes-list" v-if="paginatedNotes.length > 0">
        <div
          class="note-card"
          v-for="note in paginatedNotes"
          :key="note.id"
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

      <!-- Pagination Controls -->
      <div class="pagination-controls">
        <button @click="prevPage" :disabled="currentPage === 1">Previous</button>
        <span>Page {{ currentPage }} of {{ totalPages }}</span>
        <button @click="nextPage" :disabled="currentPage === totalPages">Next</button>
      </div>
    </div>
    <div class="loading" v-else>
      <img :src="loadingGif" alt="Loading..." />
    </div>
  </main>
</template>

<style scoped>
    /* Add some basic styling for pagination */
    /*.pagination-controls {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-top: 1rem;
    }

    .pagination-controls button {
        margin: 0 0.5rem;
    }*/

    .pagination-controls {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-top: 1rem;
        gap: 0.5rem;
    }

    .pagination-controls button {
        padding: 0.5rem 1rem;
        font-size: 1rem;
        font-weight: bold;
        color: #fff;
        background-color: #007bff; /* Primary button color */
        border: none;
        border-radius: 0.25rem;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .pagination-controls button:disabled {
        background-color: #d6d6d6; /* Disabled button color */
        cursor: not-allowed;
    }

    .pagination-controls button:hover:not(:disabled) {
        background-color: #0056b3; /* Hover color */
    }

    .pagination-controls span {
        font-size: 1rem;
        font-weight: bold;
        color: #333;
    }
</style>
