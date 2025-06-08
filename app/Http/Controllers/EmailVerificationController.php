<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Notifications\CustomVerifyEmail;


class EmailVerificationController extends Controller
{
    public function sendVerificationEmail(Request $request) {

        $request->validate([
            "is_mobile" => 'required'
        ]);

        if($request->user()->hasVerifiedEmail()) {
            return response()->json([
                'status' => 200,
                'message' => 'Already Verified'
            ], 200);
        }

        // $request->user()->sendEmailVerificationNotification();
        $request->user()->notify(new CustomVerifyEmail($request->is_mobile));


        return response()->json([
            'status' => 200,
            'message' => 'Verification link sent!'
        ], 200);
    }
    //  FOR BOTH WEB AND MOBILE APP
    public function verify(Request $request, $id, $hash) {
        $redirect = $request->query('redirect_url', config('app.frontend_web_url') . '/email-verified-error');

        // Optional domain safety check
        if (!str_starts_with($redirect, config('app.frontend_web_url')) &&
            !str_starts_with($redirect, config('app.frontend_mobile_url'))) {
            abort(403, 'Invalid redirect URL.');
        }

        $user = User::find($id);
        if (!$user || !hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return redirect($redirect);
        }

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        return redirect($redirect);
    }

    // FOR ONLY WEB
    // public function verify($id, $hash) {
    //     $user = User::find($id);

    //     if (!$user) {
    //         return redirect(config('app.frontend_url') . '/email-verified-error');
    //     }

    //     if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
    //         return redirect(config('app.frontend_url') . '/email-verified-error');
    //     }

    //     if ($user->hasVerifiedEmail()) {
    //         return redirect(config('app.frontend_url') . '/email-verified');
    //     }

    //     $user->markEmailAsVerified();
    //     return redirect(config('app.frontend_url') . '/email-verified');
    // }

    //  LARAVEL DEFAULT VERIFY METHOD
    // public function verify(EmailVerificationRequest $request) {

    //     if ($request->user()->hasVerifiedEmail()) {
    //         return response()->json([
    //             'status' => 200,
    //             'message' => 'Already Verified'
    //         ], 200);
    //     }

    //     $request->fulfill();
    //     return response()->json([
    //         'status' => 200,
    //         'message' => 'Email verified!'
    //     ], 200);
    // }

    public function verifyStatus(Request $request) {
        return response()->json([
            'status' => 200,
            'verified' => $request->user()->hasVerifiedEmail()
        ], 200);
    }
}
