<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotesControllerForApi;
use App\Http\Controllers\UsersControllerForApi;

/*Route::get('/user', function() {

    // Log User Out
    Route::post("/logout", [UsersControllerForApi::class, "logout"]);

    // Show User profile form
    Route::get("/users/profile", [UsersControllerForApi::class, "profile"]);

    // Show User's notes
    //Route::get("/users/notes", [NoteControllerForApi::class, "manage_notes"]);

    // Show User's notes
    Route::post("/notes", [NotesControllerForApi::class, "manage_notes"]);

    // Update user's name
    Route::post("/users/update_profile_name", [UsersControllerForApi::class, "update_profile_name"]);

    // Update user's email
    Route::post("/users/update_profile_email", [UsersControllerForApi::class, "update_profile_email"]);

    // Update user's password
    Route::post("/users/update_profile_password", [UsersControllerForApi::class, "update_profile_password"]);

    // Upload Note form page
    Route::post("/notes/add_note", [NotesControllerForApi::class, "store"]);

    // Delete Note
    Route::delete("/notes/{note}", [NotesControllerForApi::class, "destroy"]);

    // View Note form page
    Route::get("/notes/view_note/{note}", [NotesControllerForApi::class, "view_note"]);
})->middleware('auth:sanctum');*/

Route::post("/register", [UsersControllerForApi::class, "register"]);

// Authenticate the user during login
//Route::post("/users/authenticate", [UserControllerForApi::class, "authenticate"]);
Route::post("/login", [UsersControllerForApi::class, "login"]);

Route::post('/forgot_password', [UsersControllerForApi::class, 'submitForgetPasswordForm']);
Route::post('/reset_password', [UsersControllerForApi::class, 'submitResetPasswordForm']);

// this will be a cron job that will run
// 12am everyday to delete expired reset password requests
// in the database
Route::get('/delete_expired_reset_password_tokens', [UsersControllerForApi::class, 'deleteExpiredResetPasswordTokens']);

//Protected routes/routes that need authentication
Route::group(["middleware" => ["auth:sanctum"]], function() {
    // Log User Out
    Route::post("/logout", [UsersControllerForApi::class, "logout"]);

    // Show User profile form
    Route::get("/users/profile", [UsersControllerForApi::class, "profile"]);

    // Show User's notes
    //Route::get("/users/notes", [NoteControllerForApi::class, "manage_notes"]);

    // Show User's notes
    Route::post("/notes", [NotesControllerForApi::class, "manage_notes"]);

    // Update user's name
    Route::post("/users/update_profile_name", [UsersControllerForApi::class, "update_profile_name"]);

    // Update user's email
    Route::post("/users/update_profile_email", [UsersControllerForApi::class, "update_profile_email"]);

    // Update user's password
    Route::post("/users/update_profile_password", [UsersControllerForApi::class, "update_profile_password"]);

    // Upload Note form page
    Route::post("/notes/add_note", [NotesControllerForApi::class, "store"]);

    // Update a Note form page
    Route::post("/notes/update_note/{note}", [NotesControllerForApi::class, "update"]);

    // Delete Note
    Route::delete("/notes/{note}", [NotesControllerForApi::class, "destroy"]);

    // View Note form page
    Route::get("/notes/view_note/{note}", [NotesControllerForApi::class, "view_note"]);
});

