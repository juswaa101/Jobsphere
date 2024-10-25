<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Show profile page
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function viewProfile()
    {
        // Render profile page
        return view('page.profile.index');
    }

    /**
     * Update password
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePassword(Request $request)
    {
        // Validate password request
        $request->validate([
            'currentPassword' => ['required'],
            'newPassword' => ['required', 'different:currentPassword', 'max:8'],
            'confirmPassword' => ['same:newPassword']
        ]);

        // Check if the current password matches the authenticated user's password
        if (!Hash::check($request->currentPassword, Auth::user()->password)) {
            return response()->json([
                'errors' => [
                    'currentPassword' => ['The current password is incorrect.']
                ]
            ], 422);
        }

        try {
            // Update password for the current authenticated user
            Auth::user()->update([
                'password' => Hash::make($request->newPassword) // Hash the new password
            ]);

            // Log out the user to require re-authentication
            auth()->logout();

            return response()->json([
                'message' => 'Password updated!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update password, please try again!'
            ], 500);
        }
    }

    /**
     * Update the user's name and email.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        // Validate the request inputs using an array instead of the | separator
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . auth()->id()],
        ]);

        try {
            // Update the user's information using Auth::user()->update()
            Auth::user()->update([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
            ]);

            return response()->json(['message' => 'User information updated successfully.'], 200);
        } catch (\Exception $e) {
            // Return a JSON response with error message and status 500 for server error
            return response()->json(['message' => 'Failed to update user information.'], 500);
        }
    }

    /**
     * Update the user's profile image.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProfileImage(Request $request)
    {
        // Validate the image input using an array
        $request->validate([
            'profile_image' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:16784'], // Image file must be of valid type and under 2MB
        ]);

        try {
            $user = Auth::user();

            // If the user already has a profile image, delete it from storage
            if ($user->profile_image) {
                Storage::delete('public/' . $user->profile_image);
            }

            // Store the new profile image
            $path = $request->file('profile_image')->store('profile_images', 'public');

            // Update the user's profile image path in the database
            $user->update(['profile_image' => $path]);

            return response()->json(['message' => 'Profile image updated successfully.'], 200);
        } catch (\Exception $e) {
            // Return a JSON response with error message and status 500 for server error
            return response()->json(['message' => 'Failed to update profile image.'], 500);
        }
    }

    /**
     * Remove the user's profile image.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeProfileImage()
    {
        try {
            $user = Auth::user();

            // Check if the user has a profile image
            if ($user->profile_image) {
                // Delete the image from storage
                Storage::delete('public/' . $user->profile_image);

                // Remove the image path from the user's record
                $user->update(['profile_image' => null]);

                return response()->json(['message' => 'Profile image removed successfully.'], 200);
            }

            return response()->json(['message' => 'No profile image to remove.'], 400);
        } catch (\Exception $e) {
            // Return a JSON response with error message and status 500 for server error
            return response()->json(['message' => 'Failed to remove profile image.'], 500);
        }
    }
}
