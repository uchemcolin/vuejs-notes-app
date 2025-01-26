<script setup>
import { onMounted, ref, computed, watch } from 'vue';
import { useRouter } from 'vue-router';
import loadingGif from "@/assets/images/Loading_icon_cropped.gif";

const router = useRouter();

let notes = ref([]);
let tryingToLoadNotes = ref(true);
let searchQuery = ref("");  // The search query input

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

// Compute visible pages with ellipses
const visiblePages = computed(() => {
  const pages = [];
  const maxVisible = 5; // Maximum number of page buttons to display
  const start = Math.max(1, currentPage.value - 2);
  const end = Math.min(totalPages.value, currentPage.value + 2);

  for (let i = start; i <= end; i++) {
    pages.push(i);
  }

  if (start > 1) pages.unshift('...');
  if (end < totalPages.value) pages.push('...');
  return pages;
});

// Fetch notes on mount or when search query changes
const fetchNotes = (search = "") => {
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
      body: JSON.stringify({ search }) // Send the search query in the request body
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
};

// Fetch notes when mounted and when search query changes
onMounted(() => {
  fetchNotes(); // Initial fetch with no search term
});

// Watch for changes in the search query to refetch notes
watch(searchQuery, (newSearch) => {
  currentPage.value = 1; // Reset to the first page when search query changes
  fetchNotes(newSearch);  // Fetch notes with the new search term
});

// Navigation methods
const goToPage = (page) => {
  if (page === '...') return; // Do nothing for ellipses
  currentPage.value = page;
};

const goToViewNote = (noteToViewId) => {
  router.push('/view-note/' + noteToViewId);
};

const prevPage = () => {
  if (currentPage.value > 1) currentPage.value--;
};

const nextPage = () => {
  if (currentPage.value < totalPages.value) currentPage.value++;
};
</script>

<template>
  <main class="page view-notes">
    <h1>Your Notes</h1>

    <!-- Search Bar -->
    <div class="search-bar" v-if="paginatedNotes.length > 0">
      <input
        v-model="searchQuery"
        type="text"
        placeholder="Search Notes"
        class="search-input"
      />
    </div>

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
          <h3 v-if="!searchQuery">You currently have no notes</h3>
          <h3 v-else>Notes matching "{{  searchQuery }}" not found.</h3>
        </div>
      </div>

      <!-- Pagination Controls -->
      <div class="pagination-controls" v-if="paginatedNotes.length > 0">
        <button @click="prevPage" :disabled="currentPage === 1">Previous</button>
        
        <button
          v-for="page in visiblePages"
          :key="page"
          :disabled="page === '...' || page === currentPage"
          :class="{ active: page === currentPage }"
          @click="goToPage(page)"
        >
          {{ page }}
        </button>

        <button @click="nextPage" :disabled="currentPage === totalPages">Next</button>
      </div>
    </div>
    <div class="loading" v-else>
      <br/>
      <img :src="loadingGif" alt="Loading..." />
    </div>
  </main>
</template>

<style>
/* Basic search bar styling */
.search-bar {
  display: flex;
  justify-content: center;
  margin-top: 1.5rem;
}

.search-input {
  width: 100%;
  max-width: 500px; /* Adjust for responsiveness */
  padding: 0.75rem;
  font-size: 1rem;
  border: 1px solid #ccc;
  border-radius: 5px;
  background-color: #f8f8f8;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.search-input:focus {
  border-color: #007bff;
  outline: none;
  background-color: white;
}

/* Pagination Styles */
.pagination-controls {
  display: flex;
  flex-wrap: wrap; /* Ensure buttons wrap on smaller screens */
  justify-content: center;
  gap: 0.5rem;
  margin-top: 1rem;
}

.pagination-controls button {
  padding: 0.5rem 1rem;
  border: 1px solid #ccc;
  background-color: white;
  color: black;
  border-radius: 5px;
  cursor: pointer;
  transition: background-color 0.3s, color 0.3s;
}

.pagination-controls button.active {
  background-color: #007bff;
  color: white;
  cursor: default;
}

.pagination-controls button:disabled {
  cursor: not-allowed;
  opacity: 0.6;
}

.pagination-controls button:hover:not(:disabled):not(.active) {
  background-color: #007bff;
  color: white;
}

/* Responsive Adjustments */
@media (max-width: 600px) {
  .search-input {
    max-width: 90%; /* Ensure it takes more space on smaller screens */
  }
  
  .pagination-controls button {
    padding: 0.25rem 0.75rem;
    font-size: 0.875rem;
  }
}
</style>
