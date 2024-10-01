<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use App\Services\ImageService;
use Illuminate\Http\{RedirectResponse, Request};
use Illuminate\Support\Facades\{Redirect, Storage};
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct(private ImageService $imageService) {}

    public function show(int $userID): View
    {
        $user = User::findOrFail($userID);

        return view('profile.show', ['user' => $user]);
    }

    public function edit(int $userID): View
    {
        $user = User::findOrFail($userID);
        $profilePictureUrl = '';

        if ($user->profile->profile_picture) {
            $profilePictureUrl = Storage::url($user->profile->profile_picture);
        }

        return view('profile.edit', [
            'user' => $user,
            'profilePictureUrl' => $profilePictureUrl,
        ]);
    }

    public function update(ProfileUpdateRequest $request, int $userID): RedirectResponse
    {
        $user = User::with('profile')->findOrFail($userID);
        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        if ($request->hasFile('profile_picture')) {
            $path = $this->imageService->processAndStoreProfilePicture(
                $request->file('profile_picture'),
                $user->profile->profile_picture ?? '',
            );

            $user->profile->profile_picture = $path;
        }

        $user->save();
        $user->profile->bio = $request->input('bio');
        $user->profile->save();

        return to_route('profile.show', $userID)->with('status', 'Profile updated successfully.');
    }

    public function destroy(Request $request, int $userID): RedirectResponse
    {
        $user = User::findOrFail($userID);

        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password:web'],
        ]);

        auth()->logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/')->with('status', 'Account deleted successfully.');
    }
}
