<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AppealController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\CollaborationRequestController;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\ContactController;
use Illuminate\Support\Facades\Auth;

// Home
Route::get('/', [CampaignController::class, 'landing'])->name('landing');

Route::view('/about', 'pages.about')->name('about');
Route::view('/help', 'pages.help')->name('help');
Route::get('/contacts', [ContactController::class, 'show'])->name('contacts');

// Campaigns
Route::get('/campaigns', [CampaignController::class, 'index'])->name('campaigns.index');
Route::get('/api/campaigns/search', [CampaignController::class, 'search'])->name('api.campaigns.search');
// Constrain `{campaign}` to numeric IDs so routes like `/campaigns/create` are not
// captured by the parameterized route (prevents "create" being treated as an id).
Route::get('/campaigns/{campaign}', [CampaignController::class, 'show'])
    ->whereNumber('campaign')
    ->name('campaigns.show');

// Campaign management (auth required)
Route::middleware('auth')->group(function () {
    Route::get('/campaigns/create', [CampaignController::class, 'create'])->name('campaigns.create');
    Route::post('/campaigns', [CampaignController::class, 'store'])->name('campaigns.store');
    Route::post('/campaigns/{campaign}/updates', [CampaignController::class, 'storeUpdate'])->name('campaigns.updates.store');
    Route::get('/campaigns/{campaign}/edit', [CampaignController::class, 'edit'])->name('campaigns.edit');
    Route::patch('/campaigns/{campaign}', [CampaignController::class, 'update'])->name('campaigns.update');
    Route::delete('/campaigns/{campaign}', [CampaignController::class, 'destroy'])->name('campaigns.destroy');
    Route::post('/api/campaigns/{campaign}/donate', [DonationController::class, 'store'])->name('api.campaigns.donate');
    Route::post('/api/campaigns/{campaign}/follow', [FollowController::class, 'store'])->name('api.campaigns.follow');
    Route::delete('/api/campaigns/{campaign}/unfollow', [FollowController::class, 'destroy'])->name('api.campaigns.unfollow');
    Route::post('/api/campaigns/{campaign}/comments', [CommentController::class, 'store'])->name('api.campaigns.comments.store');

    // Campaign media upload (creator only)
    Route::post('/campaigns/{campaign}/media', [CampaignController::class, 'storeMedia'])->name('campaigns.media.store');
    Route::patch('/campaigns/{campaign}/cover/{media}', [CampaignController::class, 'setCover'])->name('campaigns.cover.set');

    // Collaboration requests
    Route::post('/campaigns/{campaign}/collaboration-request', [CollaborationRequestController::class, 'store'])->name('collaboration.request');
    Route::patch('/collaboration-requests/{request}/accept', [CollaborationRequestController::class, 'accept'])->name('collaboration.accept');
    Route::patch('/collaboration-requests/{request}/reject', [CollaborationRequestController::class, 'reject'])->name('collaboration.reject');
    Route::delete('/collaboration-requests/{request}/cancel', [CollaborationRequestController::class, 'cancel'])->name('collaboration.cancel');
    Route::delete('/campaigns/{campaign}/collaborator', [CollaborationRequestController::class, 'removeCollaborator'])->name('collaboration.remove');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::patch('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
    Route::get('/api/notifications/unread-count', [NotificationController::class, 'getUnreadCount'])->name('api.notifications.unreadCount');
});

// Campaign media destroy (auth:web,admin)
Route::delete('/campaigns/{campaign}/media/{media}', [CampaignController::class, 'destroyMedia'])
    ->middleware('auth:web,admin')
    ->name('campaigns.media.destroy');

// Users (view profiles and search)
Route::get('/users/search', [UserController::class, 'search'])->name('users.search');
Route::get('/users/{id}', [UserController::class, 'show'])
    ->where('id', '[0-9]+')
    ->name('users.show');

// Profile (auth required for either user or admin)
Route::middleware('auth:admin,web')->get('/profile', function () {
    // If the admin guard is authenticated, redirect to the admin edit/profile area.
    if (Auth::guard('admin')->check()) {
        return redirect()->route('admin.profile.edit');
    }

    return redirect()->route('users.show', Auth::id());
})->name('profile');

// Profile edit (auth required for either user or admin)
Route::middleware('auth:admin,web')->group(function () {
    // Delegate to user or admin controller depending on the authenticated guard.
    Route::get('/profile/edit', function () {
        if (Auth::guard('admin')->check()) {
            return app(AdminController::class)->edit();
        }
        return app(UserController::class)->edit();
    })->name('profile.edit');

    Route::patch('/profile', function (Request $request) {
        if (Auth::guard('admin')->check()) {
            return app(AdminController::class)->update($request);
        }

        return app(UserController::class)->update($request);
    })->name('profile.update');

    // Profile picture upload/remove (user only)
    Route::post('/profile/picture', [UserController::class, 'uploadProfilePicture'])->name('profile.picture.upload');
    Route::delete('/profile/picture', [UserController::class, 'removeProfilePicture'])->name('profile.picture.remove');

    // User account deletion (self-deletion)
    Route::delete('/profile/account', [UserController::class, 'destroy'])->name('profile.destroy');

    // Add named routes for admin profile pages.
    Route::get('/admin/profile/edit', [AdminController::class, 'edit'])->name('admin.profile.edit');
    Route::patch('/admin/profile', [AdminController::class, 'update'])->name('admin.profile.update');
    Route::get('/admins/{id}', [AdminController::class, 'show'])->whereNumber('id')->name('admin.profile.show');
});

// Administrator dashboard (empty for now)
Route::middleware('auth:admin')->get('/admin', [AdminController::class, 'index'])->name('admin.index');

// Admin search users/admins
Route::middleware('auth:admin')->get('/admin/search', [AdminController::class, 'search'])->name('admin.search');

// Admin: create users (ajax)
Route::middleware('auth:admin')->post('/admin/users', [AdminController::class, 'storeUser'])->name('admin.users.store');

// Admin campaign management: suspend/activate and soft-delete
Route::middleware('auth:admin')->post('/admin/campaigns/{campaign}/suspend', [CampaignController::class, 'toggleSuspend'])->name('admin.campaigns.toggleSuspend');
Route::middleware('auth:admin')->delete('/admin/campaigns/{campaign}', [CampaignController::class, 'destroy'])->name('admin.campaigns.destroy');

// Admin can ban/unban regular users
Route::middleware('auth:admin')->post('/admin/users/{id}/ban', [AdminController::class, 'toggleUserBan'])->name('admin.users.toggleBan');
// Admin: delete (anonymize) a user
Route::middleware('auth:admin')->delete('/admin/users/{id}', [AdminController::class, 'destroyUser'])->name('admin.users.destroy');

// Toggle report status (open <-> resolved)
Route::middleware('auth:admin')->patch('/admin/reports/{report}/status', [AdminController::class, 'toggleReportStatus'])->name('admin.reports.toggle');

// Appeals: accept / reject
Route::middleware('auth:admin')->post('/admin/appeals/{appeal}/accept', [AdminController::class, 'acceptAppeal'])->name('admin.appeals.accept');
Route::middleware('auth:admin')->post('/admin/appeals/{appeal}/reject', [AdminController::class, 'rejectAppeal'])->name('admin.appeals.reject');

// View the reported comment by redirecting to its campaign page (anchor to comment)
Route::middleware('auth:admin')->get('/admin/reports/{report}/view', [AdminController::class, 'viewReportComment'])->name('admin.reports.view_comment');

// Admin can delete comments
Route::middleware('auth:admin')->delete('/admin/comments/{comment}', [AdminController::class, 'destroyComment'])->name('admin.comments.destroy');

// API (authentication required)

// Authentication
Route::controller(LoginController::class)->group(function () {
    Route::get('/login', 'showLoginForm')->name('login');
    Route::post('/login', 'authenticate');
});

Route::controller(LogoutController::class)->group(function () {
    Route::post('/logout', 'logout')->name('logout');
    Route::get('/logout', function() {
        return redirect()->route('login')->with('error', 'Please use the logout button from your profile page.');
    });
});

Route::controller(RegisterController::class)->group(function () {
    Route::get('/register', 'showRegistrationForm')->name('register');
    Route::post('/register', 'register');
});

// Password Reset
Route::controller(ForgotPasswordController::class)->group(function () {
    Route::get('/forgot-password', 'showLinkRequestForm')->name('password.request');
    Route::post('/forgot-password', 'sendResetLinkEmail')->name('password.email');
});

Route::controller(ResetPasswordController::class)->group(function () {
    Route::get('/reset-password/{token}', 'showResetForm')->name('password.reset');
    Route::post('/reset-password', 'reset')->name('password.update');
});

// OAuth sign-in / sign-up
Route::get('/auth/google', [SocialAuthController::class, 'redirect'])->name('google-auth');
Route::get('/auth/google/call-back', [SocialAuthController::class, 'callbackGoogle'])->name('google-call-back');

// Reports (auth required)
Route::middleware('auth')->post('/reports', [ReportController::class, 'store'])->name('reports.store');

// Appeals (auth required) - store unban appeals (no migration; table exists in DB snapshot)
Route::middleware('auth')->post('/appeals', [AppealController::class, 'store'])->name('appeals.store');

// Allow authenticated users to delete their own comments
Route::middleware('auth')->delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
