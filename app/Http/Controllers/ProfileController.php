<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{

    public function show(): Response
    {
        return Inertia::render('Dashboard/AccountData');
    }
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function getKeys(): Response | RedirectResponse
    {
        if (auth()->user()->public_key_ecdh && auth()->user()->public_key_eddsa) {
            return Redirect('/dashboard');
        }
        return Inertia::render('Profile/RegisterKey');
    }

    public function finalize(Request $request): Response | RedirectResponse
    {
        $validated = $request->validate([
            'publicKeyEcdh' => 'required|string',
            'publicKeyEddsa' => 'required|string',
        ]);
        auth()->user()->update([
            'public_key_ecdh' => $validated['publicKeyEcdh'],
            'public_key_eddsa' => $validated['publicKeyEddsa']
            ]);
        return Redirect('/dashboard');
    }
}
