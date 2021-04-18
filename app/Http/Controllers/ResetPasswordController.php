<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Notifications\PasswordResetNotification;
use App\Models\User;
use App\Models\ResetPassword;


class ResetPasswordController extends Controller
{

    /**
     * get user email and send password reset link to the user
     *
     * @param Request $request
     * @return void
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
        ]);

        /**
         * get the user from the data base
         */
        $user = User::where('email', $request->email)->first();

        /**
         * if the user is not found, an error is returned
         */
        if (!$user){
            return response()->json([
                'errors' => (Object) ['error' => ['We did not find a user with this email']]
            ], 422);
        }

        /**
         * if the user is found, we create or update the user in the password reset table
         */
        $passwordReset = ResetPassword::updateOrCreate(
            ['email' => $user->email],
            [
                'email' => $user->email,
                'token' => uniqid()
             ]
        );

        /**
         * if the creation or update is successful we send a password reset
         * notification with the link to the user
         */

        if($user && $passwordReset)
            $user->notify(
                new PasswordResetNotification($passwordReset->token, $user)
            );
        return response()->json('We have e-mailed your password reset link!');
    }


    /**
     * here we receive the users email and password to do the reset
     *
     * @param Request $request
     * @return void
     */
    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
            'c_password' => 'required|same:password',
        ]);

        /**
         * we retrieve the email the email and token
         */
        $passwordReset = ResetPassword::where([
            ['token', $request->token],
            ['email', $request->email]
        ])->first();

        /**
         * we check if the email and token are preset, if not we return an error
         */
        if (!$passwordReset){
            return response()->json([
                'errors' => (Object) ['error' => ['This password reset token is invalid.']]
            ], 422);
        }

        /**
         * we get the user with the email from the database
         */
        $user = User::where('email', $passwordReset->email)->first();

        /**
         * we return an error if theres is no user with that email
         */
        if (!$user){
            return response()->json([
                'errors' => (Object) ['error' => ['We cant find a user with that e-mail address.']]
            ], 422);
        }

        /**
         * we save the new password and delete the password reset data
         */
        $user->password = Hash::make($request->password);
        $user->save();
        $passwordReset->delete();
        return response()->json('Password reset successfully');

    }


        /**
         * updating a password
         *
         * @param Request $request
         * @return void
         */
    public function updatePassword(Request $request)
             {
                $request->validate([
                        'current_password' => 'required|string',
                        'new_password' => 'required|string|min:6',
                        'confirm_password' => 'required|string|same:new_password',
                            ]);

                        $current_password = $request->current_password;
                        $new_password  = $request->new_password;

        if(!Hash::check($current_password, $request->user()->password)){
            return response()->json([
                'errors' => (Object) ['error' => ['The old password did not match.']]
            ], 422);
         }
         else{
         $request->user()->fill(['password' => Hash::make($new_password)])->save();
        return response('Password reset successfully');

     }
    }
}
