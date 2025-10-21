<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
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
        $manageableUsers = collect();

       return view('profile.edit', [
            'user' => $request->user(),
            'manageableUsers' => $request->user()->jenis_user === 'admin'
                ? User::orderBy('name')->get(['id_user', 'name', 'email'])
                : $manageableUsers,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        if ($request->user()->jenis_user !== 'admin') {
            unset($validated['name']);
        }

        $request->user()->fill($validated);

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    public function updateUserName(Request $request): RedirectResponse
    {
        abort_unless($request->user()->jenis_user === 'admin', 403);

        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id_user'],
            'name' => ['required', 'string', 'max:255'],
        ]);

        $target = User::findOrFail($validated['user_id']);
        $target->name = $validated['name'];
        $target->save();

        return Redirect::route('profile.edit')->with('status', 'user-name-updated');
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
