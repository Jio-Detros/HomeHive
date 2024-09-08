<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile-edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        // Update profile fields
        $user->uname = $request->input('username');
        $user->fname = $request->input('first_name');
        $user->mname = $request->input('middle_name');
        $user->lname = $request->input('last_name');
        $user->bdate = $request->input('birthdate');
        $user->email = $request->input('email');
        $user->contact_no = $request->input('contact_no');
        $user->street = $request->input('street');
        $user->house_blk_no = $request->input('block_no');
        $user->house_lot_no = $request->input('lot_no');

        // Check if password needs to be updated
        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
        }

        // Mark email as unverified if it has changed
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // Save changes
        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
