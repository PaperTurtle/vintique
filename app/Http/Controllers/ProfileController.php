<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use App\Services\ImageService;
use Illuminate\Http\{RedirectResponse, Request};
use Illuminate\Support\Facades\{Auth, Redirect, Storage};
use Illuminate\View\View;

/**
 * ProfileController is responsible for managing user profile information.
 * This includes displaying the edit form, updating profile data, and deleting the user's account.
 */
class ProfileController extends Controller
{
    protected ImageService $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * Display the user's profile.
     * This method returns the view for the user's profile with the user data.
     *
     * @param  int  $userID the ID of the user whose profile is being displayed
     * @return View returns the view for the user's profile with the user data
     */
    public function show(int $userID): View
    {
        $user = User::findOrFail($userID);

        return view('profile.show', ['user' => $user]);
    }

    /**
     * Display the user's profile form for editing.
     * This method returns the view for the user to edit their profile information.
     *
     * @return View returns the view for editing the user's profile with the user data
     */
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

    /**
     * Update the user's profile information.
     * This method updates the user's profile with the validated data from the request.
     * If the email is changed, it resets the email verification status.
     * It returns a redirect response to the profile edit page with a status message.
     *
     * @param  ProfileUpdateRequest $request the request instance containing validated profile data
     * @param  int                  $userID  the ID of the user whose profile is being updated
     * @return RedirectResponse     returns a redirect response to the profile edit page with a status message
     */
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

        return Redirect::route('profile.show', $userID)->with('status', 'Profile updated successfully.');
    }

    /**
     * Delete the user's account.
     * This method deletes the authenticated user's account after validating the provided password.
     * It logs out the user, invalidates the session, and redirects to the home page.
     *
     * @param  Request          $request the current request instance
     * @param  int              $userID  the ID of the user whose account is being deleted
     * @return RedirectResponse returns a redirect response to the home page after account deletion
     */
    public function destroy(Request $request, int $userID): RedirectResponse
    {
        $user = User::findOrFail($userID);

        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password:web'],
        ]);

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/')->with('status', 'Account deleted successfully.');
    }
}
