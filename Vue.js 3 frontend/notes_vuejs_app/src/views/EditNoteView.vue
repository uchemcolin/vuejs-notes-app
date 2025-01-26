<script setup>
    import { onMounted } from 'vue';
    import { ref } from 'vue';
    import { useRouter } from "vue-router";
    import { useRoute } from "vue-router";
    import { reactive } from 'vue';
    import { useToast } from 'vue-toastification';
    import loadingGif from "@/assets/images/Loading_icon_cropped.gif";
    import config from "@/my_javascript_files/config";

    const apiUrl = config.apiBaseUrl;

    const toast = useToast(); // Get the toast instance

    const router = useRouter();

    const route = useRoute();

    const noteId = route.params.id;

    let note = ref({});

    let tryingToLoadNote = ref(true);

    let updatingNote = ref(false);

    let showLoadingErrorMessage = ref(false);

    let form = reactive(
        {
            title: "",
            content: ""
        }
    );

    let reloadPage = () => {
        location.reload();
    }

    // In your main App.vue or a global mixin:
    onMounted(() => {
        const token = localStorage.getItem('authToken');
        const tokenExpiry = localStorage.getItem('tokenExpiry');

        //console.log(token);

        if (token && tokenExpiry > Date.now()) {

            tryingToLoadNote.value = true;

            //const url = `/api/notes/view_note/${noteId}`;
            const url = `${apiUrl}/notes/view_note/${noteId}`;

            fetch(url, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
            })
            .then(response => {
                if (!response.ok) {

                    showLoadingErrorMessage.value = true;
                }

                return response.json();
            })
            .then(data => {

                note.value = data["note"];

                tryingToLoadNote.value = false;

                form.title = note.value.title;
                form.content = note.value.content;

                //console.log("Note: " + note.value.id);

                //console.log("tryingToLoad success: " + tryingToLoadNote.value);
            })
            .catch(error => {
                //console.error('Error:', error);
                //console.log(error);

                showLoadingErrorMessage.value = true;

            });
        }

        //console.log("tryingToLoad end: " + tryingToLoadNote.value);
    });

    // Validation state
    let errors = reactive({
        title: "",
        content: ""
    });
    
    const validateField = (field) => {
        let isValid = true;

        // Validate individual fields based on which one is being updated
        if (field === "title") {
            if (form.title.trim() === "") {
                errors.title = "Title is required.";
                isValid = false;
            } else if (form.title.length < 1 || form.title.length > 100) {
                errors.title = "Please the title must be between 1 to 100 characters.";
                isValid = false;
            } else {
                errors.title = "";
            }
        }

        if (field === "content") {
            // Validate content
            if (form.content.trim() === "") {
                errors.content = "Content is required.";
                isValid = false;
            } else if (form.content.length < 1 || form.content.length > 5000) {
                errors.content = "Please the content must be between 1 to 5000 characters.";
                isValid = false;
            } else {
                errors.content = "";
            }
        }

        return isValid;
    };

    const validateForm = () => {
        let isValid = true;

        // Validate title
        if (form.title.trim() === "") {
            errors.title = "Title is required.";
            isValid = false;
        } else if (form.title.length < 1 || form.title.length > 100) {
            errors.title = "Please the title must be between 1 to 100 characters.";
            isValid = false;
        } else {
            errors.title = "";
        }

        // Validate content
        if (form.content.trim() === "") {
            errors.content = "Content is required.";
            isValid = false;
        } else if (form.content.length < 1 || form.content.length > 5000) {
            errors.content = "Please the content must be between 1 to 5000 characters.";
            isValid = false;
        } else {
            errors.content = "";
        }

        return isValid;
    };

    // update new note to the database
    let updateNote = async () => {

        if(validateForm()) {
            const token = localStorage.getItem('authToken');
            const tokenExpiry = localStorage.getItem('tokenExpiry');

            //console.log(token);

            if (token && tokenExpiry > Date.now()) {
                const url = `/api/notes/update_note/${noteId}`;
                const data = form;

                updatingNote.value = true;

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${token}`
                    },
                    body: JSON.stringify(data)
                })
                .then(response => {
                    if (!response.ok) {

                        updatingNote.value = false;
                    }

                    return response.json();
                })
                .then(data => {
                    //console.log('Success:', data);

                    let message = data["message"];
                    let text = data["text"];

                    if(message.type == "success") {

                        updatingNote.value = false;

                        toast.success(message.text, {
                            timeout: 3000, // Custom timeout for this toast
                        });

                        // Redirect to the '/view-notes' route
                        router.push('/view-note/' + noteId);
                    } else {

                        updatingNote.value = false;

                        toast.error("There was an error in updating the note. Please try again.", {
                            timeout: 5000, // Custom timeout for this toast
                        });
                    }
                    
                })
                .catch(error => {
                    console.error('Error:', error);

                    updatingNote.value = false;

                    toast.error("An error has occured. Please try again.", {
                        timeout: 5000, // Custom timeout for this toast
                    });
                });
            }
        }
    }
</script>

<template>

    <main class="page add-note" v-if="tryingToLoadNote == false && note !== null">
        <h1>Edit Note</h1>
        <form class="note-form" @submit.prevent="updateNote">
            <div class="form-group">
                <input 
                    type="text" 
                    class="form-input" 
                    :class="{ 'is-invalid': errors.title }" 
                    placeholder="Note Title" 
                    v-model="form.title" 
                    @input="validateField('title')"
                >
                <small v-if="errors.title" class="error-message">{{ errors.title }}</small>
            </div>
            <div class="form-group">
                <textarea 
                    class="form-input" 
                    :class="{ 'is-invalid': errors.content }" 
                    placeholder="Note Content" 
                    v-model="form.content" 
                    rows="5" 
                    @input="validateField('content')"
                ></textarea>
                <small v-if="errors.content" class="error-message">{{ errors.content }}</small>
            </div>
            <button type="submit" class="btn btn-primary" :disabled="updatingNote">
                <span v-if="!updatingNote">Update Note</span>
                <span v-else>Updating... <font-awesome-icon icon="spinner" class="fa-spin" size="lg" /></span>
            </button>
        </form>
    </main>
    <main class="page add-note" v-else-if="tryingToLoadNote == false && note === null">
        <div class="note-container">
            <p class="note-content">
                Note does not exist
            </p>
        </div>
    </main>
    <main class="page add-note" v-else>
        <div v-if="showLoadingErrorMessage == false">
            <div>
                <img :src="loadingGif" />
            </div>
        </div>
        <div v-else="showLoadingErrorMessage == true" class="error-section">
            <div class="error-message">
              <strong>Error loading the note. Please try again.</strong>
            </div>
            <div>
              <!--<a href="javascript:void(0)" @click="reloadPage" class="reload-link">
                Reload
              </a>-->
              <RouterLink :to="router.currentRoute.value.fullPath" class="reload-link">Reload</RouterLink>
            </div>
          </div>
    </main>
</template>

<style scoped>
    /* Styles for validation */
    .form-group {
        margin-bottom: 1rem;
    }

    .is-invalid {
        border-color: #e74c3c;
        box-shadow: 0 0 5px rgba(231, 76, 60, 0.5);
    }

    .error-message {
        color: #e74c3c;
        font-size: 0.85rem;
        margin-top: 0.25rem;
        text-align: left;
    }

    .note-form {
        margin: 0 auto;
        background: #fff;
        padding: 2rem;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    }
</style>