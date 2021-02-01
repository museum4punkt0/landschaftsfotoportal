<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\User;
use Illuminate\Http\Request;
use Redirect;

class ChangeEmailController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a form for changing the user's email address.
     *
     * @return \Illuminate\Http\Response
     */
    public function change()
    {
        $user = Auth::user();
        
        return view('auth.email', compact('user'));
    }

    /**
     * Store the new email address and send verification link.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'email' => 'required|email',
        ]);
        
        $user->email = $request->input('email');
        $user->email_verified_at = null;
        $user->save();
        
        // Fire the event for sending an verification email
        event(new \Illuminate\Auth\Events\Registered($user));

        return Redirect::to('email/verify')
            ->with('success', __('email.changed'));
    }
}
