<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;

use App\Models\Campaign;
use App\Models\Category;
use App\Models\CampaignUpdate;
use App\Events\CampaignUpdatePublished;
use App\Models\Media;
use Illuminate\Support\Facades\Storage;

class CampaignController extends Controller
{
    /**
     * Exibe a landing page com campanhas populares.
     */
    public function landing()
    {
        $popularCampaigns = Campaign::with(['creator', 'category', 'coverMedia'])
            ->orderBy('popularity', 'desc')
            ->take(3)
            ->get();

        $totalActiveCampaigns = Campaign::where('status', 'active')->count();
        $totalRaised = Campaign::sum('current_amount');

        return view('landing', [
            'popularCampaigns' => $popularCampaigns,
            'totalActiveCampaigns' => $totalActiveCampaigns,
            'totalRaised' => $totalRaised
        ]);
    }
    /**
     * Display a listing of all campaigns.
     */
    public function index(Request $request): View
    {
        // Build query with relationships
        $query = Campaign::with(['creator', 'category', 'coverMedia']);

        $categoryId = $request->input('category');
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $search = $request->input('search');
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->whereRaw(
                    "(
                        setweight(to_tsvector('english', coalesce(title, '')), 'A') ||
                        setweight(to_tsvector('english', coalesce(description, '')), 'B')
                    ) @@ plainto_tsquery('english', ?)",
                    [$search]
                )
                ->orWhereHas('category', function($catQuery) use ($search) {
                    $catQuery->where('name', 'ILIKE', '%' . $search . '%');
                })
                ->orWhereHas('creator', function($userQuery) use ($search) {
                    $userQuery->where('name', 'ILIKE', '%' . $search . '%');
                });
            });
        }

        // Retrieve campaigns ordered by popularity
        $campaigns = $query->orderBy('popularity', 'desc')->get();

        $categories = Category::orderBy('category_id')->get();

        // Render the 'pages.campaigns' view with all campaigns and search term.
        return view('pages.campaigns', [
            'campaigns' => $campaigns,
            'search' => $search ?? '',
            'categories' => $categories,
            'selectedCategory' => $categoryId ?? ''
        ]);
    }

// search campaign
    public function search(Request $request)
    {
        $query = Campaign::with(['creator', 'category', 'coverMedia']);

        $categoryId = $request->input('category');
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $search = $request->input('search');
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->whereRaw(
                    "(
                        setweight(to_tsvector('english', coalesce(title, '')), 'A') ||
                        setweight(to_tsvector('english', coalesce(description, '')), 'B')
                    ) @@ plainto_tsquery('english', ?)",
                    [$search]
                )
                ->orWhereHas('category', function($catQuery) use ($search) {
                    $catQuery->where('name', 'ILIKE', '%' . $search . '%');
                })
                ->orWhereHas('creator', function($userQuery) use ($search) {
                    $userQuery->where('name', 'ILIKE', '%' . $search . '%');
                });
            });
        }

        $campaigns = $query->orderBy('popularity', 'desc')->get();

        $formattedCampaigns = $campaigns->map(function($campaign) {
            $progress = $campaign->goal_amount > 0
                ? min(100, round(($campaign->current_amount / $campaign->goal_amount) * 100, 1))
                : 0;

            return [
                'campaign_id' => $campaign->campaign_id,
                'title' => $campaign->title,
                'description' => $campaign->description,
                'goal_amount' => (float) $campaign->goal_amount,
                'current_amount' => (float) $campaign->current_amount,
                'status' => $campaign->status,
                'popularity' => $campaign->popularity ?? 0,
                'start_date' => $campaign->start_date?->format('Y-m-d'),
                'end_date' => $campaign->end_date?->format('Y-m-d'),
                'progress' => $progress,
                'cover_image' => $campaign->coverMedia?->file_path,
                'category' => [
                    'name' => $campaign->category?->name ?? 'Uncategorized',
                ],
                'creator' => [
                    'name' => $campaign->creator->name,
                    'user_id' => $campaign->creator->user_id ?? null,
                ],
            ];
        });

        return response()->json([
            'campaigns' => $formattedCampaigns,
            'search' => $search ?? '',
            'category' => $categoryId ?? ''
        ]);
    }

    /**
     * Display the details of a specific campaign.
     */
    public function show(Campaign $campaign): View
    {
        // Preload the campaign's related data.
        $campaign->load([
            'creator',
            'collaborators',
            'category',
            'donations.donator',
            'updates.author',
            'comments.user',
            'media',
            'coverMedia',
        ]);

        // If the campaign has an end date in the past, mark it as completed.
        // Persist this change so the rest of the application (views and
        // controllers) will treat the campaign as completed and prevent
        // further donations.
        if ($campaign->end_date !== null && $campaign->end_date->lt(now()) && $campaign->status !== 'completed') {
            $campaign->status = 'completed';
            $campaign->save();
        }

        $progress = $campaign->goal_amount > 0
            ? min(100, round(($campaign->current_amount / $campaign->goal_amount) * 100, 2))
            : 0;

        // Check if current user is following this campaign
        $isFollowing = Auth::check() 
            ? Auth::user()->followedCampaigns()->where('lbaw2532.user_follows_campaign.campaign_id', $campaign->campaign_id)->exists()
            : false;

        // Check if current user has a pending collaboration request
        $hasPendingRequest = Auth::check()
            ? $campaign->collaborationRequests()
                ->where('requester_id', Auth::id())
                ->where('status', 'pending')
                ->exists()
            : false;

        // Get pending collaboration requests if user is the campaign creator
        $pendingRequests = (Auth::check() && Auth::id() === $campaign->creator_id)
            ? $campaign->collaborationRequests()
                ->with('requester')
                ->where('status', 'pending')
                ->orderBy('created_at', 'desc')
                ->get()
            : collect();

        // Render the 'pages.campaign' view with the campaign details.
        return view('pages.campaign', [
            'campaign' => $campaign,
            'progress' => $progress,
            'recentDonations' => $campaign->donations->take(5),
            // Provide all updates sorted by date (newest first) so the view
            // can show the first update and make the rest scrollable.
            'recentUpdates' => $campaign->updates->sortByDesc('date')->values(),
            'recentComments' => $campaign->comments->take(5),
            'isFollowing' => $isFollowing,
            'hasPendingRequest' => $hasPendingRequest,
            'pendingRequests' => $pendingRequests,
        ]);
    }

    /**
     * Show the form for creating a new campaign.
     */
    public function create(): View
    {
        // Admins are not allowed to create campaigns.
        if (Auth::guard('admin')->check()) {
            return redirect()->route('campaigns.index')
                ->with('error', 'Administrators are not allowed to create campaigns.');
        }

        // Ensure the current user is authorized to create a campaign.
        Gate::authorize('create', Campaign::class);

        // Load categories for the create form and render the view.
        // Order by the numeric category index (category_id) rather than alphabetically.
        $categories = Category::orderBy('category_id')->get();
        return view('pages.campaign-create', [
            'categories' => $categories
        ]);
    }

    /**
     * Store a newly created campaign in storage.
     */
    public function store(Request $request)
    {
        // Admins are not allowed to create campaigns.
        if (Auth::guard('admin')->check()) {
            return redirect()->route('campaigns.index')
                ->with('error', 'Administrators are not allowed to create campaigns.');
        }

        // Ensure the current user is authorized to create a campaign.
        Gate::authorize('create', Campaign::class);

        // Validate the request data. Use a model-based check for category because
        // the table is schema-qualified (e.g. "lbaw2532.category"). The normal
        // 'exists:connection.table,column' format would interpret the prefix
        // as a DB connection name, which we don't have configured.
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            // require goal_amount to be numeric and strictly greater than zero
            'goal_amount' => ['required', 'numeric', 'gt:0'],
            'start_date' => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    $start_ts = strtotime($value);
                    $today_ts = strtotime(date('Y-m-d'));
                    if ($start_ts < $today_ts) {
                        $fail('The start date must be today or later.');
                    }
                }
            ],
            // end_date is optional, but when present must be a valid date and later than start_date
            'end_date' => [
                'nullable',
                'date',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value === null || $value === '') {
                        return;
                    }
                    $start = strtotime($request->input('start_date'));
                    $end = strtotime($value);
                    // Allow end_date to be equal to start_date when creating.
                    if ($end < $start) {
                        $fail('The end date must be the same as or later than the start date.');
                    }
                }
            ],
            'category_id' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!\App\Models\Category::where('category_id', $value)->exists()) {
                        $fail('The selected category is invalid.');
                    }
                }
            ],
        ]);

        // Create and populate a new campaign instance.
        $campaign = new Campaign();
        $campaign->title = $validated['title'];
        $campaign->description = $validated['description'];
        $campaign->goal_amount = $validated['goal_amount'];
        $campaign->start_date = $validated['start_date'] ?? null;
        $campaign->end_date = $validated['end_date'] ?? null;
        $campaign->category_id = $validated['category_id'] ?? null;
        $campaign->creator_id = Auth::id();
        $campaign->current_amount = 0;
        $campaign->status = 'active';
        $campaign->popularity = 0;

        // Persist the campaign.
        $campaign->save();

        // Redirect to the campaign details page.
        return redirect()->route('campaigns.show', $campaign->campaign_id)
            ->with('success', 'Campaign created successfully!');
    }

    /**
     * Show the form for editing the specified campaign.
     */
    public function edit(Campaign $campaign): View
    {
        // Ensure the current user is authorized to update this campaign.
        Gate::authorize('update', $campaign);

        // Load categories for the edit form
        $categories = Category::orderBy('category_id')->get();

        // Render the 'pages.campaign-edit' view with categories
        return view('pages.campaign-edit', [
            'campaign' => $campaign,
            'categories' => $categories,
        ]);
    }

    /**
     * Update the specified campaign in storage.
     */
    public function update(Request $request, Campaign $campaign)
    {
        // Ensure the current user is authorized to update this campaign.
        Gate::authorize('update', $campaign);

        // Validate the request data. For updates we DO NOT allow changing the start_date.
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'goal_amount' => ['required', 'numeric', 'gt:0'],
            'end_date' => [
                'nullable',
                'date',
                function ($attribute, $value, $fail) use ($campaign) {
                    if ($value === null || $value === '') {
                        return;
                    }
                    $start = $campaign->start_date ? strtotime($campaign->start_date) : null;
                    $end = strtotime($value);
                    // Allow end_date to be equal to start_date when editing.
                    if ($start !== null && $end < $start) {
                        $fail('The end date must be the same as or later than the start date.');
                    }
                }
            ],
            'category_id' => [
                'required',
                function ($attribute, $value, $fail) {
                    if ($value !== null && $value !== '' && !\App\Models\Category::where('category_id', $value)->exists()) {
                        $fail('The selected category is invalid.');
                    }
                }
            ],
            'status' => 'in:active,completed,cancelled',
        ]);

        // Only update allowed fields (do not change start_date or creator_id)
        $fields = ['title', 'description', 'goal_amount', 'end_date', 'category_id', 'status'];
        foreach ($fields as $f) {
            if (array_key_exists($f, $validated)) {
                $campaign->{$f} = $validated[$f];
            }
        }
        $campaign->save();

        // Redirect to the campaign details page.
        return redirect()->route('campaigns.show', $campaign->campaign_id)
            ->with('success', 'Campaign updated successfully!');
    }

    /**
     * Remove the specified campaign from storage.
     */
    public function destroy(Campaign $campaign)
    {
        // If an admin is performing the action: mark donations invalid,
        // dissociate them from the campaign (set campaign_id = NULL),
        // then delete the campaign row entirely so it's removed from listings.
        if (Auth::guard('admin')->check()) {
            DB::transaction(function () use ($campaign) {
                // Mark related donations invalid and dissociate from campaign.
                $campaign->donations()->update([ 'is_valid' => false, 'campaign_id' => null ]);

                // Now delete the campaign record entirely.
                $campaign->delete();
            });

            return redirect()->route('campaigns.index')
                ->with('success', 'Campaign deleted and donations marked invalid.');
        }

        // Ensure regular user is authorized to delete this campaign.
        Gate::authorize('delete', $campaign);

        // BR.101: Prevent deletion if campaign has donations for regular users.
        if ($campaign->donations()->count() > 0) {
            return redirect()->route('campaigns.show', $campaign->campaign_id)
                ->with('error', 'Cannot delete campaign with existing donations (BR.101).');
        }

        // Delete the campaign for the creator when no donations exist.
        $campaign->delete();

        return redirect()->route('campaigns.index')
            ->with('success', 'Campaign deleted successfully!');
    }

    /**
     * Toggle suspend/activate for a campaign (admin only).
     */
    public function toggleSuspend(Campaign $campaign)
    {
        if (! Auth::guard('admin')->check()) {
            abort(403);
        }

        $campaign->status = $campaign->status === 'suspended' ? 'active' : 'suspended';
        $campaign->save();

        return redirect()->route('campaigns.show', $campaign->campaign_id)
            ->with('success', 'Campaign status updated to ' . $campaign->status . '.');
    }

    /**
     * Store a new update for the specified campaign.
     */
    public function storeUpdate(Request $request, Campaign $campaign)
    {
        // Only authorized users (typically the creator) may post updates.
        Gate::authorize('postUpdate', $campaign);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:5000',
        ]);

        $update = CampaignUpdate::create([
            'title' => $validated['title'] ?? null,
            'content' => $validated['content'],
            'date' => now(),
            'campaign_id' => $campaign->campaign_id,
            'author_id' => Auth::id(),
        ]);

        // Dispatch notification event
        event(new CampaignUpdatePublished($update));

        return redirect()->route('campaigns.show', $campaign->campaign_id)
            ->with('success', 'Update posted successfully!');
    }

    /**
     * Store an uploaded media file for the specified campaign.
     */
    public function storeMedia(Request $request, Campaign $campaign)
    {
        // Only campaign creator may upload media
        Gate::authorize('uploadMedia', $campaign);

        $validated = $request->validate([
            'media' => 'required|file|max:10240|mimes:jpg,jpeg,png,gif,mp4,mov,webm,pdf',
        ]);

        $file = $request->file('media');

        // Determine media type
        $mime = $file->getMimeType();
        if (str_starts_with($mime, 'image/')) {
            $type = 'image';
        } elseif (str_starts_with($mime, 'video/')) {
            $type = 'video';
        } else {
            $type = 'file';
        }

        // Store under the public disk so files are web-accessible via storage symlink
        $path = $file->store("campaign_media/{$campaign->campaign_id}", 'public');

        // Use the public storage URL path so views can reference the file directly
        $publicPath = '/storage/' . $path;

        // Compute hash of the uploaded file to detect duplicates
        try {
            $newContents = Storage::disk('public')->get($path);
            $newHash = hash('sha256', $newContents);
        } catch (\Exception $e) {
            // If we cannot read the file for some reason, clean up and abort
            Storage::disk('public')->delete($path);
            return redirect()->route('campaigns.show', $campaign->campaign_id)
                ->with('error', 'Failed to process uploaded file.');
        }

        // Check existing media for this campaign for identical content
        $existing = Media::where('campaign_id', $campaign->campaign_id)->get();
        $uploadedName = $file->getClientOriginalName();
        foreach ($existing as $m) {
            $existingPath = $m->file_path;
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
                        // Duplicate found: remove the newly uploaded file and inform user
                        Storage::disk('public')->delete($path);
                        $existingName = basename($existingPath);
                        return redirect()->route('campaigns.show', $campaign->campaign_id)
                            ->with('error', "Duplicate media detected — uploaded file '{$uploadedName}' appears identical to existing file '{$existingName}'.");
                    }
                } catch (\Exception $e) {
                    // ignore read errors for existing files
                }
            }
        }

        // No duplicate found — create media record
        $createdMedia = Media::create([
            'file_path' => $publicPath,
            'media_type' => $type,
            'uploaded_at' => now(),
            'campaign_id' => $campaign->campaign_id,
        ]);

        // Auto-set cover if none exists and this is an image
        if ($type === 'image' && ! $campaign->cover_media_id) {
            $campaign->cover_media_id = $createdMedia->media_id;
            $campaign->save();
        }

        return redirect()->route('campaigns.show', $campaign->campaign_id)
            ->with('success', 'Media uploaded successfully!');
    }

    /**
     * Set a campaign cover image from existing media.
     */
    public function setCover(Campaign $campaign, Media $media)
    {
        Gate::authorize('uploadMedia', $campaign);

        if ($media->campaign_id !== $campaign->campaign_id) {
            abort(404);
        }

        if ($media->media_type !== 'image') {
            return redirect()->route('campaigns.show', $campaign->campaign_id)
                ->with('error', 'Only images can be used as a campaign cover.');
        }

        $campaign->cover_media_id = $media->media_id;
        $campaign->save();

        return redirect()->route('campaigns.show', $campaign->campaign_id)
            ->with('success', 'Cover image updated.');
    }

    /**
     * Remove a media item from a campaign (delete file and database record).
     */
    public function destroyMedia(Campaign $campaign, Media $media)
    {
        // Allow admins to delete media
        if (Auth::guard('admin')->check()) {
            // Admin is authorized
        } else {
            // Only the campaign creator may remove media
            Gate::authorize('deleteMedia', $campaign);
        }

        // Ensure the media belongs to the campaign
        if ($media->campaign_id !== $campaign->campaign_id) {
            abort(404);
        }

        // If this media is the current cover, clear it before deletion
        if ($campaign->cover_media_id === $media->media_id) {
            $campaign->cover_media_id = null;
            $campaign->save();
        }

        // Remove the file from storage if it exists. The DB stores a public path like '/storage/campaign_media/...'
        $filePath = $media->file_path;
        if (str_starts_with($filePath, '/storage/')) {
            $rel = ltrim(substr($filePath, strlen('/storage/')), '/');
            Storage::disk('public')->delete($rel);
        } elseif (str_starts_with($filePath, 'storage/')) {
            $rel = ltrim(substr($filePath, strlen('storage/')), '/');
            Storage::disk('public')->delete($rel);
        }

        $media->delete();

        return redirect()->route('campaigns.show', $campaign->campaign_id)
            ->with('success', 'Media removed successfully.');
    }
}
