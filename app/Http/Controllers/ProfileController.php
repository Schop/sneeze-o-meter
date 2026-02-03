<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $data = $request->validated();
        
        // Handle checkbox values (false when unchecked)
        $data['use_precise_location'] = $request->has('use_precise_location');
        $data['show_in_leaderboard'] = $request->has('show_in_leaderboard');
        
        // Parse custom locations if provided
        if (isset($data['custom_locations']) && !empty($data['custom_locations'])) {
            $locations = array_filter(array_map('trim', explode(',', $data['custom_locations'])));
            $data['custom_locations'] = !empty($locations) ? $locations : null;
        } else {
            $data['custom_locations'] = null;
        }
        
        $request->user()->fill($data);

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('success', __('messages.profile.saved'));
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
