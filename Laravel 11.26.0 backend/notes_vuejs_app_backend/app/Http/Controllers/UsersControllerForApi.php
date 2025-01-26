<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendResetPasswordLinkEmail;

class UsersControllerForApi extends Controller
{
    //1|RinRcN050UcR6ClWq8VfdAt93GbNlwLWydYnYydr916df52d
    // Create New User
    public function register(Request $request) {

        $formFields = $request->validate([
            "firstname" => ["required", "min:3", "max:30"],
            "lastname" => ["required", "min:3", "max:30"],
            "email" => ["required", "max:255", "email", Rule::unique("users", "email")],
            //"password" => ["required", "confirmed", "min:6"]
            "password" => "required|confirmed|min:6|max:30"
        ]);
        
        $formFields["firstname"] = ucfirst($formFields["firstname"]);
        $formFields["lastname"] = ucfirst($formFields["lastname"]);

        // Hash Password
        $formFields["password"] = bcrypt($formFields["password"]);

        try {
            // Create User
            $user = User::create($formFields);

            // create token to be used by app for authentication
            // for routes that require you to be logged in
            // before you can visit them
            $token =  $user->createToken('TheNotesVueJsWebApp')->plainTextToken;

            $response = [
                "user" => $user,
                "message" => [
                    "type" => "success",
                    "text" => "Your account has been created and you are now logged in",
                    //"token" => $token->plainTextToken
                    "token" => $token
                ]
            ];

            return response(
                $response,
                201 // 201 status code meaning everything went smooth and there was no error
            );
        } catch (\Exception $e) {

            //var_dump($e->getMessage());

            // Return an error response with 500 status code (Internal Server Error)
            return response()->json([
                'message' => [
                    "type" => "error",
                    "text" => $e->getMessage(),
                ],
            ], 500);
        }
    }

    // Show profile form
    public function profile() {

        $user_details = User::find(auth()->id());

        $response = [
            "user" => $user_details
        ];

        return response(
            $response,
            200 // 200 status code (similar to 201 but not exactly the same, this one is just for retrieving something, no update of info been done) meaning everything went smooth and there was no error
        );
    }

    // Update User's profile first and last names
    public function update_profile_name(Request $request) {

        $user = User::find(auth()->id());

        $formFields = $request->validate([
            "firstname" => ["required", "min:3", "max:30"],
            "lastname" => ["required", "min:3", "max:30"],
        ]);

        $formFields["firstname"] = ucfirst($formFields["firstname"]);
        $formFields["lastname"] = ucfirst($formFields["lastname"]);

        /** Have to use try and catch for api incase it did not 
         * update successfully, it will notify the users
         * say the name was not updated successfully
         * Used it for app for the app to be more accurate in
         * telling if it went smoothly as it is not running
         * on the web browser that can easily indicate an error
         */
        try {
            $user->update($formFields);

            $response = [
                "user" => $user,
                "message" => [
                    "type" => "success",
                    "text" => "Your name has been changed successfully!"
                ]
            ];
    
            return response(
                $response,
                201 // 201 status code meaning everything went smooth and there was no error
            );
        }
        
        catch (\Exception $e) { // I don't remember what exception it is specifically, but it catches an exception
        

            $response = [
                "user" => $user,
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
    }
    
    // Update User's profile Email
    public function update_profile_email(Request $request) {

        $user = User::find(auth()->id());

        $formFields = $request->validate([
            "email" => ["required", "max:255", "email", Rule::unique("users", "email")],
        ]);

        $formFields["email"] = strtolower($formFields["email"]);

        /** Have to use try and catch for api incase it did not 
         * update successfully, it will notify the users
         * say the name was not updated successfully
         * Used it for app for the app to be more accurate in
         * telling if it went smoothly as it is not running
         * on the web browser that can easily indicate an error
         */
        try {
            $user->update($formFields);

            $response = [
                "user" => $user,
                "message" => [
                    "type" => "success",
                    "text" => "Your email has been changed successfully!"
                ]
            ];
    
            return response(
                $response,
                201 // 201 status code meaning everything went smooth and there was no error
            );
        }
        
        catch (\Exception $e) { // I don't remember what exception it is specifically, but it catches an exception
        
            $response = [
                "user" => $user,
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
    }

    // Update User's profile Email
    public function update_profile_password(Request $request) {

        $user = User::find(auth()->id());

        $formFields = $request->validate([
            "old_password" => ["required", "min:6"],
            "password" => "required|confirmed|min:6"
        ]);
    
        // Hash Password
        $formFields["password"] = bcrypt($formFields["password"]);

        foreach($user as $u) {
            //If the old password is wrong or invalid
            //return error with the message
            if(!password_verify($formFields["old_password"], $user["password"])) {

                $response = [
                    "user" => $user,
                    "message" => [
                        "type" => "error",
                        "text" => "Wrong old password inputted."
                    ]
                ];
        
                return response(
                    $response,
                    500
                );
            }
        }

        /** Have to use try and catch for api incase it did not 
         * update successfully, it will notify the users
         * say the name was not updated successfully
         * Used it for app for the app to be more accurate in
         * telling if it went smoothly as it is not running
         * on the web browser that can easily indicate an error
         */
        try {
            $user->password = $formFields["password"];
            $user->save();

            $response = [
                "user" => $user,
                "message" => [
                    "type" => "success",
                    "text" => "Your password has been changed successfully!"
                ]
            ];
    
            return response(
                $response,
                201 // 201 status code meaning everything went smooth and there was no error
            );
        }
        
        catch (\Exception $e) { // I don't remember what exception it is specifically
        
            $response = [
                "user" => $user,
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
    }

    // Logout User
    public function logout(Request $request) {

        /** Have to use try and catch for api incase it did not 
         * update successfully, it will notify the users
         * say the name was not updated successfully
         * Used it for app for the app to be more accurate in
         * telling if it went smoothly as it is not running
         * on the web browser that can easily indicate an error
         */
        try {
            auth()->user()->tokens()->delete(); // delete the user's logged in tokens

            $response = [
                "message" => [
                    "type" => "success",
                    "text" => "You have logged out!"
                ]
            ];
    
            return response(
                $response,
                200
            );
        }
        
        catch (\Exception $e) { // I don't remember what exception it is specifically
        
            $response = [
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
    }

    // Login User
    public function login(Request $request) {
        
        $formFields = $request->validate([
            "email" => ["required", "email"],
            //"password" => ["required", "confirmed", "min:6"]
            "password" => "required|min:6"
        ]);

        // attempt to login the user using the email and password provided via the api
        if(Auth::attempt(['email' => $formFields["email"], 'password' => $formFields["password"]])){ 
            $user = Auth::user();

            $token =  $user->createToken('TheNotesVueJsWebApp')->plainTextToken; // create a login token for the user on the app on the device
   
            $response = [
                "user" => $user,
                "type" => "success",
                "message" => "You are now logged in!",
                "token" => $token
            ];

            return response(
                $response,
                201 // 201 status code meaning everything went smooth and there was no error
            );

        } else {
            return response([
                "type" => "error",
                "message" => "Bad credentials"
            ], 401);
        }
    }

    public function submitForgetPasswordForm(Request $request) {

        $request->validate([
            'email' => 'required|email|exists:users',
        ]);

        $token = Str::random(64);

        // Start transaction for all those queries
        DB::beginTransaction();

        DB::table('password_reset_tokens')->insert([
            'email' => $request->email, 
            'token' => $token, 
            'created_at' => Carbon::now()
        ]);

        // Send the user/admin a password
        // reset email
        
        $content = [
            'subject' => "Reset Password",
            //'body' => 'This is the email body of how to send email from laravel 10 with mailtrap.'
            'token' => $token
        ];

        try {

            // try to send the email with the password
            // reset link to the user
            Mail::to($request->email)->send(new SendResetPasswordLinkEmail($content));

            // if we reach here, it means all
            // commit the transaction to save everything
            // and to let it know it went well
            DB::commit();

            $response = [
                "type" => "success",
                "message" => "We have e-mailed your password reset link!"
            ];

            return response(
                $response,
                201 // 201 status code meaning everything went smooth and there was no error
            );

        } catch (\Exception $e) {
            // Rollback/undo the records creation in the database
            DB::rollBack();

            // if the email did not send
            // go back and notify the user

            return response([
                "type" => "error",
                "message" => "There was an error in sending the reset link to your email. Please try again!"
            ], 401);
        }

        /*Mail::send('email.forgetPassword', ['token' => $token], function($message) use($request){
            $message->to($request->email);
            $message->subject('Reset Password');
        });*/

        /*session()->flash("success", "We have e-mailed your password reset link!");
        //return back()->with('message', 'We have e-mailed your password reset link!');
        return back();*/
    }
  
    public function submitResetPasswordForm(Request $request) {

        $request->validate([
            'email' => 'required|email|exists:users',
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required'
        ]);

        // Start transaction for all those queries
        DB::beginTransaction();

        $updatePassword = DB::table('password_reset_tokens')
                            ->where([
                            'email' => $request->email, 
                            'token' => $request->token
                            ])
                            ->orderBy("created_at", "desc")
                            ->first();

        if(!$updatePassword) {

            // Rollback/undo the records creation in the database
            DB::rollBack();

            return response([
                "type" => "error",
                "message" => "Invalid token!"
            ], 401);

        } else {

            // token is to expire in 30 minutes

            //foreach($updatePassword as $a) {
                //$origin = date_create($a["created_at"]); // date and time when the reset password token was created/entered. It is in the "Y-m-d H:i:s" format too
                $origin = date_create($updatePassword->created_at); // date and time when the reset password token was created/entered. It is in the "Y-m-d H:i:s" format too
                $target = date_create(date("Y-m-d H:i:s")); // current date and time
                $interval = date_diff($origin, $target);
                $difference_in_minutes = $interval->format('%i'); // 30 returned for instance, the minutes

                //return var_dump($difference_in_minutes);

                if($difference_in_minutes > 30) {

                    return response([
                        "type" => "error",
                        "message" => "Token has expired! Please start again."
                    ], 401);
                }
            //}
        }

        $user = User::where('email', $request->email)
                    //->update(['password' => Hash::make($request->password)]);
                    ->update(['password' => bcrypt($request->password)]);

        if(!$user) {
            // Rollback/undo the records creation in the database
            DB::rollBack();

            return response([
                "type" => "error",
                "message" => "An error occured! Please try again."
            ], 401);
        }

        DB::table('password_reset_tokens')->where(['email'=> $request->email])->delete();

        // if we reach here, it means all
        // commit the transaction to save everything
        // in the database
        // and to let it know it went well
        DB::commit();

        $response = [
            "type" => "success",
            "message" => "Your password has been changed!"
        ];

        return response(
            $response,
            201 // 201 status code meaning everything went smooth and there was no error
        );
    }

    // this will be a cron job that will run
    // 12am everyday to delete expired reset password requests
    // in the database. The ones that have expired for up to
    // a day and more
    // tokens expired after 30mminutes from when they 
    // have been created, but the code runs once a day
    // so it will delete the tokens that have expired
    // if is it a day or more the token was created
    public function deleteExpiredResetPasswordTokens() {

        // Start transaction for all those queries
        //DB::beginTransaction();

        $allResetPasswordTokens = DB::table('password_resets')
                                    ->orderBy("created_at", "desc")
                                    ->get();

        if(count($allResetPasswordTokens) > 0) {
            foreach($allResetPasswordTokens as $allResetPasswordToken) {
                $origin = date_create($allResetPasswordToken->created_at); // date and time when the reset password token was created. It is in the "Y-m-d H:i:s" format too
                $target = date_create(date("Y-m-d H:i:s")); // current date and time
                $interval = date_diff($origin, $target);
                $difference_in_minutes = $interval->format('%i'); // 30 returned for instance, the minutes
                $difference_in_days = $interval->format('%a'); // the difference in days

                //return var_dump($difference_in_minutes);
                //var_dump($difference_in_minutes);

                if($difference_in_days > 0) {

                    //return var_dump($allResetPasswordToken);
                    // delete the token since it has expired
                    if(!(DB::table('password_reset_tokens')->where(['created_at' => $allResetPasswordToken->created_at])->delete())) {
                    //if(!$allResetPasswordToken->delete()) {
                        // do nothing if it did not delete
                        // it will definitely retry it the next time
                        // the code will run or will continue
                        // from where it stopped

                        echo "error <br/>";
                    }
                }
            }
        }

        /*// if we reach here, it means all
        // commit the transaction to save everything
        // in the database
        // and to let it know it went well
        DB::commit();*/
    }
}