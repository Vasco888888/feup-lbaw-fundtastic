<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Media;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\Comment;
use App\Models\Donation;
use App\Models\Report;
use App\Models\UnbanAppeal;
use App\Models\Campaign;

class UserController extends Controller
{
    public function __construct()
    {
        // Only require authentication for editing/updating profile
        $this->middleware('auth')->only(['edit', 'update']);
    }

    public function search(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        if ($q !== '') {
            $users = User::where('name', 'like', "%{$q}%")
                ->orWhere('email', 'like', "%{$q}%")
                ->orderBy('name')
                ->limit(100)
                ->get();
        } else {
            $users = User::orderBy('name')
                ->limit(100)
                ->get();
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'users' => $users->map(function ($u) {
                    return [
                        'id' => $u->user_id,
                        'name' => $u->name,
                        'email' => $u->email,
                        'profile_image' => $u->profile_image,
                    ];
                })->values(),
            ]);
        }

        return view('users.search', [
            'users' => $users,
            'q' => $q,
        ]);
    }

    /**
     * Display the user's profile.
     */
    public function show($id)
    {
        $user = User::findOrFail($id);

        // Load recent donations and campaigns for the profile page.
        // Exclude anonymous donations from being displayed on the profile
        $donations = $user->donations()
            ->where('is_anonymous', false)
            ->with('campaign')
            ->orderBy('date', 'desc')
            ->take(10)
            ->get();
        $campaigns = $user->campaigns()->withCount('donations')->get();

        // Compute totals
        // Sum only valid, non-anonymous donations so invalidated donations (e.g., after admin deletion) don't count
        // and anonymous contributions don't appear in the total
        $totalContributions = $user->donations()
            ->where('is_anonymous', false)
            ->where(function($q){
                $q->where('is_valid', true)->orWhereNull('is_valid');
            })->sum('amount');

        return view('pages.profile', [
            'user' => $user,
            'donations' => $donations,
            'campaigns' => $campaigns,
            'totalContributions' => $totalContributions,
        ]);
    }

    /**
     * Show edit form for the authenticated user.
     */
    public function edit()
    {
        $user = Auth::user();

        // Ensure the authenticated user may edit this profile
        Gate::authorize('update', $user);

        return view('pages.profile_edit', [
            'user' => $user,
        ]);
    }

    /**
     * Update the authenticated user's profile.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();

        // Ensure the authenticated user may update this profile
        Gate::authorize('update', $user);

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => [
                    'required',
                    'email',
                    'max:255',
                    Rule::unique('user', 'email')->ignore($user->user_id, 'user_id'),
                ],
                'bio' => 'nullable|string|max:1000',
            ]);

            $user->fill($validated);
            $user->save();

        return redirect()->route('users.show', $user->user_id)->with('success', 'Profile updated.');
    }

    /**
     * Upload a profile picture for the authenticated user.
     */
    public function uploadProfilePicture(Request $request): RedirectResponse
    {
        $user = Auth::user();

        // Ensure the authenticated user may update this profile
        Gate::authorize('update', $user);

        $validated = $request->validate([
            'profile_picture' => 'required|file|max:5120|mimes:jpg,jpeg,png,gif',
        ]);

        $file = $request->file('profile_picture');

        // Store under the public disk so files are web-accessible via storage symlink
        $path = $file->store("profile_pictures/{$user->user_id}", 'public');

        // Use the public storage URL path so views can reference the file directly
        $publicPath = '/storage/' . $path;

        // Check for duplicate content
        try {
            $newContents = Storage::disk('public')->get($path);
            $newHash = hash('sha256', $newContents);

            // Check existing profile media for this user
            if ($user->profile_media_id) {
                $existingMedia = Media::find($user->profile_media_id);
                if ($existingMedia) {
                    $existingPath = $existingMedia->file_path;
                    $rel = null;
                    if (str_starts_with($existingPath, '/storage/')) {
                        $rel = ltrim(substr($existingPath, strlen('/storage/')), '/');
                    } elseif (str_starts_with($existingPath, 'storage/')) {
                        $rel = ltrim(substr($existingPath, strlen('storage/')), '/');
                    }

                    if ($rel && Storage::disk('public')->exists($rel)) {
                        try {
                            $existingContents = Storage::disk('public')->get($rel);
                            $existingHash = hash('sha256', $existingContents);
                            if ($existingHash === $newHash) {
                                // Duplicate found: remove the newly uploaded file
                                Storage::disk('public')->delete($path);
                                return redirect()->route('profile.edit')
                                    ->with('error', 'This image is already your profile picture.');
                            }
                        } catch (\Exception $e) {
                            // ignore read errors
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            // If we cannot read the file, clean up and abort
            Storage::disk('public')->delete($path);
            return redirect()->route('profile.edit')
                ->with('error', 'Failed to process uploaded file.');
        }

        // Delete old profile picture if it exists
        if ($user->profile_media_id) {
            $oldMedia = Media::find($user->profile_media_id);
            if ($oldMedia) {
                // Delete old file from storage
                $oldPath = $oldMedia->file_path;
                if (str_starts_with($oldPath, '/storage/')) {
                    $rel = ltrim(substr($oldPath, strlen('/storage/')), '/');
                    Storage::disk('public')->delete($rel);
                } elseif (str_starts_with($oldPath, 'storage/')) {
                    $rel = ltrim(substr($oldPath, strlen('storage/')), '/');
                    Storage::disk('public')->delete($rel);
                }
                // Delete old media record
                $oldMedia->delete();
            }
        }

        // Create new media record
        $media = Media::create([
            'file_path' => $publicPath,
            'media_type' => 'image',
            'uploaded_at' => now(),
            'user_id' => $user->user_id,
            'campaign_id' => null,
        ]);

        // Update user's profile_media_id
        $user->profile_media_id = $media->media_id;
        // Clear external profile image (from OAuth) when uploading a new picture
        $user->external_profile_image = null;
        $user->save();

        return redirect()->route('profile.edit')
            ->with('success', 'Profile picture uploaded successfully!');
    }

    /**
     * Remove the profile picture for the authenticated user.
     */
    public function removeProfilePicture(): RedirectResponse
    {
        $user = Auth::user();

        // Ensure the authenticated user may update this profile
        Gate::authorize('update', $user);

        if ($user->profile_media_id) {
            $media = Media::find($user->profile_media_id);
            if ($media) {
                // Delete file from storage
                $filePath = $media->file_path;
                if (str_starts_with($filePath, '/storage/')) {
                    $rel = ltrim(substr($filePath, strlen('/storage/')), '/');
                    Storage::disk('public')->delete($rel);
                } elseif (str_starts_with($filePath, 'storage/')) {
                    $rel = ltrim(substr($filePath, strlen('storage/')), '/');
                    Storage::disk('public')->delete($rel);
                }
                // Delete media record
                $media->delete();
            }

            // Clear user's profile_media_id
            $user->profile_media_id = null;
            $user->save();
        }

        return redirect()->route('profile.edit')
            ->with('success', 'Profile picture removed successfully.');
    }

    /**
     * Delete the authenticated user's account.
     * This anonymizes all content created by the user (same behavior as admin delete).
     */
    public function destroy(): RedirectResponse
    {
        $user = Auth::user();

        // Ensure the authenticated user may delete their own account
        Gate::authorize('delete', $user);

        DB::transaction(function () use ($user) {
            // Anonymize comments
            Comment::where('user_id', $user->user_id)->update(['user_id' => null]);

            // Anonymize donations made by the user
            Donation::where('donator_id', $user->user_id)->update(['donator_id' => null, 'is_anonymous' => true]);

            // Delete reports and appeals referencing this user so they do not
            // appear in the administrator dashboard after the user is removed.
            Report::where('user_id', $user->user_id)->delete();
            UnbanAppeal::where('user_id', $user->user_id)->delete();

            // Remove follow records for the user (clean up pivot table)
            DB::table('user_follows_campaign')->where('user_id', $user->user_id)->delete();

            // For campaigns created by this user: mark donations invalid and dissociate them,
            // and anonymize the campaign creator.
            $campaigns = Campaign::where('creator_id', $user->user_id)->get();
            foreach ($campaigns as $campaign) {
                // Mark donations invalid and dissociate from campaign (same behaviour as admin deleting a campaign)
                $campaign->donations()->update([ 'is_valid' => false, 'campaign_id' => null ]);

                // Delete notifications related to this campaign's updates to avoid
                // foreign key violations (notification_campaign_update.update_id
                // references campaign_update.update_id without ON DELETE CASCADE).
                try {
                    $updateIds = $campaign->updates()->pluck('update_id')->toArray();
                    if (!empty($updateIds)) {
                        $notifIds = DB::table('notification_campaign_update')->whereIn('update_id', $updateIds)->pluck('notification_id')->toArray();
                        // Remove linking rows first
                        DB::table('notification_campaign_update')->whereIn('update_id', $updateIds)->delete();
                        // Then delete the actual notifications
                        if (!empty($notifIds)) {
                            DB::table('notification')->whereIn('notification_id', $notifIds)->delete();
                        }
                    }
                } catch (\Throwable $e) {
                    // Ignore notification cleanup errors to avoid blocking user deletion;
                    // DB will still prevent campaign deletion if dangling references remain.
                }

                // Now delete the campaign record entirely so it's removed from listings
                $campaign->delete();
            }

            // Finally delete the user row to remove credentials and profile.
            $user->delete();
        });

        // Log out the user after deletion
        Auth::logout();

        return redirect()->route('campaigns.index')->with('success', 'Your account has been deleted successfully.');
    }
    }

