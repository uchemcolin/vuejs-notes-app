<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;

class NotesControllerForApi extends Controller
{
    // to get a particular note's details with the note's id
    public function view_note($id) {

        $note = Note::find($id);

        $user_id = auth()->id(); //if there is a logged in user

        if($note["user_id"] == $user_id) {
            $message = "is_user_note";
        } else {
            $message = "is_not_user_note";
        }

        $response = [
            //"user" => $user,
            "note" => $note,
            "message" => $message
        ];

        return response(
            $response,
            201 // 201 status code meaning everything went smooth and there was no error
        );
    }

    public function manage_notes(Request $request) {

        if(isset($request->search)) {
            
            // search for a note if a search is made
            $search = $request->search;
            $notes = Note::where('user_id', auth()->id())
                            ->where('title', 'like', '%'.$search.'%')
                            ->orWhere('content', 'LIKE', "%".$search."%")
                            //->orderBy('id', 'DESC')->get();
                            ->orderBy('updated_at', 'DESC')->get();
        } else {

            // return all notes if no search is made
            $user_id = auth()->id();
            $notes = Note::where("user_id", $user_id)
                            //->orderBy('id', 'DESC')->get();
                            ->orderBy('updated_at', 'DESC')->get();

            $search = "";
        }

        $response = [
            //"user" => $user,
            "message" => [
                "type" => "success"
            ],
            "notes" => $notes,
            "search" => $search
        ];

        return response(
            $response,
            201 // 201 status code meaning everything went smooth and there was no error
        );
    }

    //Store/create a new note in the note system and db
    public function store(Request $request) {

        $user_id = auth()->id();
        //$request->validate()

        $formFields = $request->validate([
            "title" => ["required", "min:1", "max:100"],
            "content" => ["required", "min:1", "max:5000"]
        ]);

        $noteDetails["title"] = $formFields["title"];

        $noteDetails["content"] = $formFields["content"];

        $noteDetails["user_id"] = auth()->id();

        //Note::create($noteDetails);
        
        $createdNote = Note::create($noteDetails);

        if(!$createdNote) {
            //If the note was not uploaded

            $response = [
                //"user" => [],
                "message" => [
                    "type" => "error",
                    "text" => "There was an error in saving the note. Please try again"
                ]
            ];

            return response(
                $response,
                404 // 404 status code because there was an error
            );

        } else {

            $response = [
                //"user" => $user,
                "message" => [
                    "type" => "success",
                    "text" => "Note saved successfully!"
                ]
            ];

            return response(
                $response,
                201 // 201 status code meaning everything went smooth and there was no error
            );
        }
    }

    // Update a new note in the note system and db
    public function update(Request $request, $noteId) {

        $user_id = auth()->id();
        //$request->validate()

        $formFields = $request->validate([
            "title" => ["required", "min:1", "max:100"],
            "content" => ["required", "min:1", "max:5000"]
        ]);

        $noteDetails["title"] = $formFields["title"];

        $noteDetails["content"] = $formFields["content"];

        $noteDetails["user_id"] = auth()->id();

        //Note::create($noteDetails);

        // find the note of the user
        $noteToUpdateDetails = Note::where("id", $noteId)
                                ->where("user_id", $user_id)
                                ->get();

        if(count($noteToUpdateDetails) > 0) {
            // get the note using find
            // so I can do ->update()
            $noteToUpdate = Note::find($noteId);

            /** Have to use try and catch for api incase it did not 
             * update successfully, it will notify the users
             * say the name was not updated successfully
             * Used it for app for the app to be more accurate in
             * telling if it went smoothly as it is not running
             * on the web browser that can easily indicate an error
             */
            try {
                $noteToUpdate->update($formFields);

                $response = [
                    //"user" => $user,
                    "message" => [
                        "type" => "success",
                        "text" => "The note has been updated successfully!"
                    ]
                ];
        
                return response(
                    $response,
                    201 // 201 status code meaning everything went smooth and there was no error
                );
            }
            
            catch (\Exception $e) { // I don't remember what exception it is specifically, but it catches an exception
            
                $response = [
                    //"user" => $user,
                    "message" => [
                        "type" => "error",
                        "text" => "An error occured, please try again."
                    ]
                ];
        
                return response(
                    $response,
                    500
                );
            
            }
        } else {

            $response = [
                //"user" => $user,
                "message" => [
                    "type" => "error",
                    "text" => "The note does not exist."
                ]
            ];
    
            return response(
                $response,
                500
            );

        }
    }

    // Delete Note
    public function destroy($note_id) {
        $note = Note::find($note_id);
        
        // Make sure logged in user is owner
        if($note->user_id != auth()->id()) {

            $response = [
                //"user" => [],
                "message" => [
                    "type" => "error",
                    "text" => "Unauthirized Action"
                ]
            ];

            return response(
                $response,
                404 // 404 status code because there was an error
            );
        } else {

            if($note->delete()) {

                $response = [
                    //"user" => $user,
                    "message" => [
                        "type" => "success",
                        "text" => "Note deleted successfully!"
                    ]
                ];
    
                return response(
                    $response,
                    201 // 201 status code meaning everything went smooth and there was no error
                );

            } else {

                $response = [
                    //"user" => [],
                    "message" => [
                        "type" => "error",
                        "text" => "Error deleting note. Please try again."
                    ]
                ];
    
                return response(
                    $response,
                    404 // 404 status code because there was an error
                );
            }
        }
    }
}
