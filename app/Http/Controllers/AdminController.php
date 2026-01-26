<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Models\Admin;
use App\Models\User;
use App\Models\Report;
use App\Models\Comment;
use App\Models\UnbanAppeal;
use App\Models\Donation;
use App\Models\Campaign;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function __construct()
    {
        // Only authenticated admins should be able to edit/update their profile.
        $this->middleware('auth');
    }

    /**
     * Create a new user or admin (admin only). Expects AJAX POST.
     */
    public function storeUser(Request $request)
    {
        $admin = auth()->guard('admin')->user();
        if (! $admin) {
            abort(403);
        }

        // Use the User model's table name to avoid Laravel interpreting a dotted
        // string as a connection name (which triggers the "Database connection
        // [...] not configured" error). This ensures the unique rule targets
        // the correct table.
        $userTable = (new User())->getTable();
        $adminTable = (new Admin())->getTable();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique($userTable, 'email'),
                Rule::unique($adminTable, 'email'),
            ],
            'password' => 'required|string|min:8',
            'account_type' => 'required|in:user,admin',
        ]);

        if ($validated['account_type'] === 'admin') {
            // Create admin
            $newAdmin = new Admin();
            $newAdmin->name = $validated['name'];
            $newAdmin->email = $validated['email'];
            $newAdmin->password = Hash::make($validated['password']);
            $newAdmin->save();

            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $newAdmin->admin_id,
                    'name' => $newAdmin->name,
                    'email' => $newAdmin->email,
                    'type' => 'admin',
                ],
            ], 201);
        } else {
            // Create user
            $user = new User();
            $user->name = $validated['name'];
            $user->email = $validated['email'];
            $user->password = Hash::make($validated['password']);
            // default values for optional fields
            $user->banned = false;
            $user->save();

            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $user->user_id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'type' => 'user',
                ],
            ], 201);
        }
    }

    /**
     * Show the administrator dashboard / index page.
     */
    public function index()
    {
        $admin = auth()->guard('admin')->user();
        if (! $admin) {
            abort(403);
        }

        // Load recent reports and appeals for display
        // Show open reports first, then resolved and other statuses
        $reports = Report::orderByRaw("CASE WHEN status = 'open' THEN 0 ELSE 1 END ASC, date DESC")->limit(50)->get();
        // Show pending appeals first, then accepted/rejected (so processed items go to the bottom)
        $appeals = UnbanAppeal::orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END ASC, date DESC")->limit(50)->get();

        // Provide a list of users for the admin to browse. Limit a reasonable number
        // so the page remains responsive while still showing a useful sample.
        $allUsers = User::orderBy('name')->limit(200)->get();

        return view('admin.index', [
            'reports' => $reports,
            'appeals' => $appeals,
            'allUsers' => $allUsers,
        ]);
    }

    /**
     * Search users and administrators by name or email.
     * GET /admin/search?q=...
     */
    public function search(Request $request)
    {
        $admin = auth()->guard('admin')->user();
        if (! $admin) {
            abort(403);
        }

        $q = trim((string) $request->query('q', ''));

        $users = collect();
        $admins = collect();

        if ($q !== '') {
            $users = User::where('name', 'like', "%{$q}%")
                ->orWhere('email', 'like', "%{$q}%")
                ->orderBy('name')
                ->limit(50)
                ->get();

            $admins = Admin::where('name', 'like', "%{$q}%")
                ->orWhere('email', 'like', "%{$q}%")
                ->orderBy('name')
                ->limit(50)
                ->get();
        }

        // Keep the dashboard context so the same view shows reports/appeals.
        // Show open reports first, then resolved and other statuses
        $reports = Report::orderByRaw("CASE WHEN status = 'open' THEN 0 ELSE 1 END ASC, date DESC")->limit(50)->get();
        $appeals = UnbanAppeal::orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END ASC, date DESC")->limit(50)->get();

        // If the request expects JSON (AJAX), return a compact JSON payload
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'users' => $users->map(function ($u) {
                    return [
                        'id' => $u->user_id,
                        'name' => $u->name,
                        'email' => $u->email,
                        'banned' => (bool) ($u->banned ?? false),
                    ];
                })->values(),
                'admins' => $admins->map(function ($a) {
                    return [
                        'id' => $a->admin_id ?? $a->id,
                        'name' => $a->name,
                        'email' => $a->email,
                    ];
                })->values(),
            ]);
        }

        return view('admin.index', [
            'reports' => $reports,
            'appeals' => $appeals,
            'users' => $users,
            'admins' => $admins,
            'q' => $q,
        ]);
    }

    /**
     * Accept an unban appeal and unban the related user.
     */
    public function acceptAppeal($id)
    {
        $admin = auth()->guard('admin')->user();
        if (! $admin) {
            abort(403);
        }

        $appeal = UnbanAppeal::findOrFail($id);
        $appeal->status = 'accepted';
        $appeal->save();

        // Unban user if present
        try {
            if ($appeal->user_id) {
                $user = User::find($appeal->user_id);
                if ($user) {
                    $user->setBanned(false);
                }
            }
        } catch (\Throwable $e) {
            // ignore unban errors
        }

        return redirect()->route('admin.index')->with('success', 'Appeal accepted and user unbanned.');
    }

    /**
     * Reject an unban appeal.
     */
    public function rejectAppeal($id)
    {
        $admin = auth()->guard('admin')->user();
        if (! $admin) {
            abort(403);
        }

        $appeal = UnbanAppeal::findOrFail($id);
        $appeal->status = 'rejected';
        $appeal->save();

        return redirect()->route('admin.index')->with('success', 'Appeal rejected.');
    }

    /**
     * Toggle a report's status between 'open' and 'resolved'.
     */
    public function toggleReportStatus($id)
    {
        $admin = auth()->guard('admin')->user();
        if (! $admin) {
            abort(403);
        }

        $report = Report::findOrFail($id);

        $report->status = ($report->status === 'open') ? 'resolved' : 'open';
        $report->save();

        return redirect()->route('admin.index')->with('success', 'Report status updated.');
    }

    /**
     * Redirect to the campaign page containing the comment referenced by the report.
     */
    public function viewReportComment($id)
    {
        $admin = auth()->guard('admin')->user();
        if (! $admin) {
            abort(403);
        }

        $report = Report::findOrFail($id);

        // If the report references a comment, find the comment and redirect to its campaign.
        if (empty($report->comment_id)) {
            return redirect()->route('admin.index')->with('error', 'No comment linked to this report.');
        }

        $comment = Comment::find($report->comment_id);
        if (! $comment) {
            return redirect()->route('admin.index')->with('error', 'Comment not found.');
        }

        $campaignId = $comment->campaign_id;
        if (! $campaignId) {
            return redirect()->route('admin.index')->with('error', 'Associated campaign not found.');
        }

        // Redirect to the campaign page anchored to the comment element.
        return redirect()->to(route('campaigns.show', $campaignId) . '#comment-' . $comment->comment_id);
    }

    /**
     * Delete a comment as an admin.
     */
    public function destroyComment($id)
    {
        $admin = auth()->guard('admin')->user();
        if (! $admin) {
            abort(403);
        }

        $comment = Comment::findOrFail($id);
        $campaignId = $comment->campaign_id;

        // Delete the comment. Related reports/comments cascade as defined by DB.
        $comment->delete();

        if ($campaignId) {
            return redirect()->route('campaigns.show', $campaignId)->with('success', 'Comment deleted.');
        }

        return redirect()->route('admin.index')->with('success', 'Comment deleted.');
    }

    /**
     * Show edit form for the authenticated admin.
     */
    public function edit()
    {
        $admin = auth()->guard('admin')->user();
        if (! $admin) {
            abort(403);
        }

        return view('pages.profile_edit', [
            'user' => $admin,
        ]);
    }

    /**
     * Show a public admin profile page.
     */
    public function show($id)
    {
        $admin = Admin::findOrFail($id);

        // Reuse the regular `pages.profile` view. Provide empty donations/campaigns
        // and zero totals so the template can render consistently for admins.
        return view('pages.profile', [
            'user' => $admin,
            'donations' => collect(),
            'campaigns' => collect(),
            'totalContributions' => 0,
        ]);
    }

    /**
     * Update the authenticated admin's profile.
     */
    public function update(Request $request): RedirectResponse
    {
        $admin = auth()->guard('admin')->user();
        if (! $admin) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('admin', 'email')->ignore($admin->admin_id, 'admin_id'),
            ],
            'bio' => 'nullable|string|max:1000',
            'profile_image' => 'nullable|url|max:1000',
        ]);

        $admin->fill($validated);
        $admin->save();

        return redirect()->route('profile')->with('success', 'Profile updated.');
    }

    /**
     * Toggle the banned status of a user (admin only).
     */
    public function toggleUserBan($id)
    {
        $admin = auth()->guard('admin')->user();
        if (! $admin) {
            abort(403);
        }

        // Find by the model primary key (User::$primaryKey is 'user_id').
        $user = User::find($id);
        if (! $user) {
            abort(404);
        }

        $new = ! ((bool) $user->banned);
        $user->setBanned($new);

        return redirect()->back()->with('success', $user->banned ? 'User has been banned.' : 'User has been unbanned.');
    }

    /**
     * Anonymize and delete a user account (admin only).
     *
     * Shared data (comments, donations, reports, appeals) is preserved but
     * the `user_id` / `donator_id` fields are set to NULL. For campaigns
     * created by the user, donations are marked invalid and dissociated
     * from the campaign (same behaviour as admin deleting a campaign),
     * and the campaign's `creator_id` is set to NULL to anonymize it.
     */
    public function destroyUser($id)
    {
        $admin = auth()->guard('admin')->user();
        if (! $admin) {
            abort(403);
        }

        $user = User::find($id);
        if (! $user) {
            abort(404);
        }

        // Prevent accidentally deleting admin profiles (defensive check).
        if (isset($user->admin_id) || $user instanceof \App\Models\Admin) {
            return redirect()->back()->with('error', 'Cannot delete an administrator account.');
        }

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

        return redirect()->route('campaigns.index')->with('success', 'User account deleted and anonymized successfully.');
    }
}
