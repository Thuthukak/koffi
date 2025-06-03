<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Illuminate\Http\JsonResponse;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
       /**
     * Get the user's profile information
     */
    public function show(): JsonResponse
    {
        $user = Auth::user();
        
        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'profile_picture' => $user->profile_picture ? Storage::url($user->profile_picture) : null,
            'must_verify_email' => $user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail,
            'email_verified' => $user->hasVerifiedEmail(),
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ]);
    }

    public function edit ()
    {
        
    }

    /**
     * Update the user's profile information
     */
    public function update(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
        ]);

        $emailChanged = $user->email !== $validated['email'];

        $user->fill($validated);

        if ($emailChanged) {
            $user->email_verified_at = null;
        }

        $user->save();

        return response()->json([
            'message' => 'Profile updated successfully.',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'profile_picture' => $user->profile_picture ? Storage::url($user->profile_picture) : null,
                'must_verify_email' => $user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail,
                'email_verified' => $user->hasVerifiedEmail(),
            ]
        ]);
    }

    /**
     * Update the user's password
     */
    public function updatePassword(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json([
            'message' => 'Password updated successfully.',
        ]);
    }

    /**
     * Upload or update profile picture
     */
    public function uploadPicture(Request $request): JsonResponse
    {
        $request->validate([
            'profile_picture' => [
                'required',
                'image',
                'mimes:jpeg,jpg,png,gif',
                'max:2048', // 2MB max
            ],
        ]);

        $user = Auth::user();

        // Delete old profile picture if exists
        if ($user->profile_picture) {
            Storage::disk('public')->delete($user->profile_picture);
        }

        // Process and store the new image
        $image = $request->file('profile_picture');
        $filename = $this->generateUniqueFilename($image->getClientOriginalExtension());
        $path = 'profile-pictures/' . $filename;

        // Resize and optimize the image
        $processedImage = Image::make($image)
            ->fit(400, 400, function ($constraint) {
                $constraint->upsize();
            })
            ->encode('jpg', 85);

        // Store the processed image
        Storage::disk('public')->put($path, $processedImage->stream());

        // Update user record
        $user->profile_picture = $path;
        $user->save();

        return response()->json([
            'message' => 'Profile picture uploaded successfully.',
            'profile_picture' => Storage::url($path),
        ]);
    }

    /**
     * Remove profile picture
     */
    public function removePicture(): JsonResponse
    {
        $user = Auth::user();

        if ($user->profile_picture) {
            // Delete the file from storage
            Storage::disk('public')->delete($user->profile_picture);
            
            // Update user record
            $user->profile_picture = null;
            $user->save();
        }

        return response()->json([
            'message' => 'Profile picture removed successfully.',
        ]);
    }

    /**
     * Delete the user's account
     */
    public function destroy(Request $request): JsonResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = Auth::user();

        // Delete profile picture if exists
        if ($user->profile_picture) {
            Storage::disk('public')->delete($user->profile_picture);
        }

        // Log the user out
        Auth::logout();

        // Delete the user
        $user->delete();

        return response()->json([
            'message' => 'Account deleted successfully.',
        ]);
    }

    /**
     * Generate a unique filename for the uploaded image
     */
    private function generateUniqueFilename(string $extension): string
    {
        return Auth::id() . '_' . time() . '_' . Str::random(10) . '.' . $extension;
    }
}
