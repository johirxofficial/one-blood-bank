<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\AdController;
use App\Http\Controllers\Admin\BlogController as AdminBlogController;
use App\Http\Controllers\Admin\BloodRequestController as AdminBloodRequestController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DonorController as AdminDonorController;
use App\Http\Controllers\Admin\LocationImportController;
use App\Http\Controllers\Admin\MailLogController;
use App\Http\Controllers\Admin\MessageController;
use App\Http\Controllers\Admin\PageController as AdminPageController;
use App\Http\Controllers\Admin\RatingController as AdminRatingController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\SiteDonationController as AdminSiteDonationController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\PhoneVerificationController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\BloodRequestController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\DonorApplicationController;
use App\Http\Controllers\DonorCardController;
use App\Http\Controllers\DonorDirectoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InstallController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PhoneRevealController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RatingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public site
|--------------------------------------------------------------------------
*/
Route::get('/install', [InstallController::class, 'show'])->name('install');
Route::post('/install', [InstallController::class, 'store'])->name('install.store');

Route::get('/', HomeController::class)->name('home');
Route::get('/pages/{slug}', [PageController::class, 'show'])->name('pages.show');
Route::get('/blood-requests', [BloodRequestController::class, 'index'])->name('requests.index');
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');
Route::get('/donate', [DonationController::class, 'index'])->name('donate.index');
Route::post('/donate', [DonationController::class, 'store'])->middleware('throttle:10,1')->name('donate.store');

Route::get('/api/locations/districts/{division}', [LocationController::class, 'districts'])->name('locations.districts');
Route::get('/api/locations/upazilas/{district}', [LocationController::class, 'upazilas'])->name('locations.upazilas');
Route::get('/api/locations/unions/{upazila}', [LocationController::class, 'unions'])->name('locations.unions');

/*
|--------------------------------------------------------------------------
| Guest authentication
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function (): void {
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store'])->middleware('throttle:6,1');

    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);

    Route::get('/forgot-password', [PasswordResetController::class, 'requestForm'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendLink'])->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'resetForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.store');
});

/*
|--------------------------------------------------------------------------
| Authenticated users
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::get('/verify-phone', [PhoneVerificationController::class, 'notice'])->name('verification.notice');
    Route::post('/verify-phone', [PhoneVerificationController::class, 'verify'])->name('verification.verify')->middleware('throttle:10,1');
    Route::post('/verify-phone/resend', [PhoneVerificationController::class, 'resend'])->name('verification.resend')->middleware('throttle:3,1');

    Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify.email');
    Route::post('/email/verification-notification', [EmailVerificationController::class, 'resend'])
        ->middleware('throttle:3,1')
        ->name('verification.send');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/dashboard/donations', [DashboardController::class, 'storeDonation'])->name('dashboard.donations.store');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    Route::get('/donor/apply', [DonorApplicationController::class, 'create'])->name('donor.apply');
    Route::post('/donor/apply', [DonorApplicationController::class, 'store'])->name('donor.apply.store');

    Route::get('/donor/card', [DonorCardController::class, 'preview'])->name('donor.card');
    Route::get('/donor/card/download', [DonorCardController::class, 'download'])->name('donor.card.download');

    Route::post('/blood-requests', [BloodRequestController::class, 'store'])->name('requests.store');
});

Route::middleware('phone.verified')->group(function (): void {
    Route::get('/donors', [DonorDirectoryController::class, 'index'])->name('donors.index');
    Route::get('/donors/{username}', [DonorDirectoryController::class, 'show'])->name('donors.show');
    Route::get('/@{username}', [DonorDirectoryController::class, 'show'])
        ->where('username', '[A-Za-z0-9_-]+')
        ->name('donors.show.username');
});

Route::middleware(['auth', 'phone.verified'])->group(function (): void {
    Route::post('/donors/{username}/reveal-phone', PhoneRevealController::class)
        ->middleware('throttle:20,1')
        ->name('donors.reveal');
    Route::post('/donors/{username}/ratings', [RatingController::class, 'store'])
        ->middleware('throttle:10,1')
        ->name('donors.rate');
});

/*
|--------------------------------------------------------------------------
| Admin portal
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'staff'])->prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/', AdminDashboardController::class)->name('dashboard');

    Route::get('/donors', [AdminDonorController::class, 'index'])->name('donors.index');
    Route::get('/donors/{donor}', [AdminDonorController::class, 'show'])->name('donors.show');
    Route::post('/donors/{donor}/approve', [AdminDonorController::class, 'approve'])->name('donors.approve');
    Route::post('/donors/{donor}/reject', [AdminDonorController::class, 'reject'])->name('donors.reject');
    Route::post('/donors/{donor}/suspend', [AdminDonorController::class, 'suspend'])->name('donors.suspend');
    Route::delete('/donors/{donor}', [AdminDonorController::class, 'destroy'])->name('donors.destroy');

    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');

    Route::get('/ratings', [AdminRatingController::class, 'index'])->name('ratings.index');
    Route::put('/ratings/{rating}', [AdminRatingController::class, 'update'])->name('ratings.update');
    Route::delete('/ratings/{rating}', [AdminRatingController::class, 'destroy'])->name('ratings.destroy');

    Route::get('/requests', [AdminBloodRequestController::class, 'index'])->name('requests.index');
    Route::put('/requests/{bloodRequest}', [AdminBloodRequestController::class, 'update'])->name('requests.update');
    Route::delete('/requests/{bloodRequest}', [AdminBloodRequestController::class, 'destroy'])->name('requests.destroy');

    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/create', [MessageController::class, 'create'])->name('messages.create');
    Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');

    Route::get('/ads', [AdController::class, 'index'])->name('ads.index');
    Route::get('/ads/create', [AdController::class, 'create'])->name('ads.create');
    Route::post('/ads', [AdController::class, 'store'])->name('ads.store');
    Route::get('/ads/{ad}/edit', [AdController::class, 'edit'])->name('ads.edit');
    Route::put('/ads/{ad}', [AdController::class, 'update'])->name('ads.update');
    Route::delete('/ads/{ad}', [AdController::class, 'destroy'])->name('ads.destroy');

    Route::get('/blog', [AdminBlogController::class, 'index'])->name('blog.index');
    Route::get('/blog/create', [AdminBlogController::class, 'create'])->name('blog.create');
    Route::post('/blog', [AdminBlogController::class, 'store'])->name('blog.store');
    Route::get('/blog/{post}/edit', [AdminBlogController::class, 'edit'])->name('blog.edit');
    Route::put('/blog/{post}', [AdminBlogController::class, 'update'])->name('blog.update');
    Route::delete('/blog/{post}', [AdminBlogController::class, 'destroy'])->name('blog.destroy');

    Route::get('/site-donations', [AdminSiteDonationController::class, 'index'])->name('site-donations.index');
    Route::put('/site-donations/{siteDonation}', [AdminSiteDonationController::class, 'update'])->name('site-donations.update');
    Route::delete('/site-donations/{siteDonation}', [AdminSiteDonationController::class, 'destroy'])->name('site-donations.destroy');

    Route::get('/pages', [AdminPageController::class, 'index'])->name('pages.index');
    Route::get('/pages/create', [AdminPageController::class, 'create'])->name('pages.create');
    Route::post('/pages', [AdminPageController::class, 'store'])->name('pages.store');
    Route::get('/pages/{page}/edit', [AdminPageController::class, 'edit'])->name('pages.edit');
    Route::put('/pages/{page}', [AdminPageController::class, 'update'])->name('pages.update');
    Route::delete('/pages/{page}', [AdminPageController::class, 'destroy'])->name('pages.destroy');

    Route::get('/logs/activity', [ActivityLogController::class, 'index'])->name('logs.activity');
    Route::get('/logs/mail', [MailLogController::class, 'index'])->name('logs.mail');

    Route::middleware('admin')->group(function (): void {
        Route::get('/settings', [SettingController::class, 'edit'])->name('settings.edit');
        Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');

        Route::get('/locations', [LocationImportController::class, 'index'])->name('locations.index');
        Route::post('/locations/import', [LocationImportController::class, 'import'])->name('locations.import');
        Route::get('/locations/sample', [LocationImportController::class, 'sample'])->name('locations.sample');

        Route::get('/staff', [StaffController::class, 'index'])->name('staff.index');
        Route::get('/staff/create', [StaffController::class, 'create'])->name('staff.create');
        Route::post('/staff', [StaffController::class, 'store'])->name('staff.store');
        Route::get('/staff/{staff}/edit', [StaffController::class, 'edit'])->name('staff.edit');
        Route::put('/staff/{staff}', [StaffController::class, 'update'])->name('staff.update');
        Route::delete('/staff/{staff}', [StaffController::class, 'destroy'])->name('staff.destroy');
    });
});
