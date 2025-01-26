<script setup>
    import { reactive } from 'vue';
    import { useToast } from 'vue-toastification';
    import { useRouter } from 'vue-router';
    import { ref } from "vue";
    import config from "@/my_javascript_files/config";

    const apiUrl = config.apiBaseUrl;

    const toast = useToast(); // Get the toast instance

    const router = useRouter();

    let savingNote = ref(false);

    let form = reactive(
        {
            title: "",
            content: ""
        }
    );

    // Validation state
    let errors = reactive({
        title: "",
        content: "",
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

    // save new note to the database
    let addNote = async () => {

        if(validateForm()) {
            const token = localStorage.getItem('authToken');
            const tokenExpiry = localStorage.getItem('tokenExpiry');

            //console.log(token);

            if (token && tokenExpiry > Date.now()) {
                //const url = '/api/notes/add_note';
                const url = `${apiUrl}/notes/add_note`;
                const data = form;

                savingNote.value = true;

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

                        savingNote.value = false;
                    }

                    return response.json();
                })
                .then(data => {
                    //console.log('Success:', data);

                    let message = data["message"];
                    let text = data["text"];

                    if(message.type == "success") {

                        savingNote.value = false;

                        toast.success(message.text, {
                            timeout: 3000, // Custom timeout for this toast
                        });

                        // Redirect to the '/view-notes' route
                        router.push('/view-notes');
                    } else {

                        savingNote.value = false;

                        toast.error("There was an error in saving your note. Please try again.", {
                            timeout: 5000, // Custom timeout for this toast
                        });
                    }
                    
                })
                .catch(error => {
                    console.error('Error:', error);

                    savingNote.value = false;

                    toast.error("An error has occured. Please try again.", {
                        timeout: 5000, // Custom timeout for this toast
                    });
                });
            }
        }
    }
</script>

<template>

    <main class="page add-note">
        <h1>Add a New Note</h1>
        <form class="note-form" @submit.prevent="addNote">
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
            <button type="submit" class="btn btn-primary" :disabled="savingNote">
                <span v-if="!savingNote">Save Note</span>
                <span v-else>Saving... <font-awesome-icon icon="spinner" class="fa-spin" size="lg" /></span>
            </button>
        </form>
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