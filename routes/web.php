<?php

use App\Http\Controllers\Admin\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ElectionController;
use App\Http\Controllers\Admin\PositionController;
use App\Http\Controllers\Admin\CandidateController;
use App\Http\Controllers\Admin\VoterController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\OrganizationController;
use App\Http\Controllers\Admin\RegistrationFieldController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\ResultController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\VoterAuthController;
use App\Http\Controllers\BallotController;

// Public / Auth

Route::get('/', fn () => redirect()->route('login'));

Route::get('e/{election:slug}/not-ready', [VoterAuthController::class, 'notReady'])
    ->name('voter.not-ready');

// Voter email verification
Route::get('e/{election:slug}/email-verify/{token}', [VoterAuthController::class, 'verifyEmailLink'])
    ->name('voter.email.verify');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');

    Route::post('/login', [LoginController::class, 'store'])
        ->middleware('throttle:login')
        ->name('login.submit');

    // Organisation registration
    Route::get('/register', [OrganizationController::class, 'register'])->name('register');

    Route::post('/register', [OrganizationController::class, 'storeRegister'])
        ->middleware('throttle:register')
        ->name('register.submit');

    // Password reset
    Route::get('/forgot-password', [ForgotPasswordController::class, 'create'])
        ->name('password.request');

    Route::post('/forgot-password', [ForgotPasswordController::class, 'store'])
        ->middleware('throttle:password-reset')
        ->name('password.email');

    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'create'])
        ->middleware('token.check')
        ->name('password.reset');

    Route::post('/reset-password', [ResetPasswordController::class, 'store'])
        ->name('password.update');
});

// Voter Portal
Route::prefix('e/{election:slug}')->name('voter.')->group(function () {

    Route::middleware('portal.ready')->group(function () {

        // Unauthenticated voter pages
        Route::middleware('voter.guest')->group(function () {

            Route::get('/', [VoterAuthController::class, 'showLogin'])
                ->name('login');

            Route::post('/', [VoterAuthController::class, 'login'])
                ->middleware(['authorize.login', 'throttle:voter-login'])
                ->name('login.submit');

            Route::get('/register', [VoterAuthController::class, 'showRegister'])
                ->name('register');

            Route::post('/register', [VoterAuthController::class, 'register'])
                ->middleware('throttle:register')
                ->name('register.submit');

            // Email verification OTP
            Route::get('/verify-email', [VoterAuthController::class, 'showVerifyEmail'])
                ->name('verify-email');

            Route::post('/verify-email', [VoterAuthController::class, 'verifyEmailOtp'])
                ->middleware('throttle:otp')
                ->name('verify-email.submit');

            // 2FA
            Route::get('/2fa', [VoterAuthController::class, 'show2fa'])
                ->name('2fa');

            Route::post('/2fa', [VoterAuthController::class, 'verify2fa'])
                ->middleware('throttle:otp')
                ->name('2fa.submit');

            // Resend OTP
            Route::post('/resend-otp', [VoterAuthController::class, 'resendOtp'])
                ->middleware('throttle:resend-otp')
                ->name('resend-otp');

            // Forgot password
            Route::get('/forgot-password', [VoterAuthController::class, 'forgetPassword'])
                ->name('password.request');

            Route::post('/forgot-password', [VoterAuthController::class, 'resetPassword'])
                ->middleware('throttle:password-reset')
                ->name('password.email');

            Route::get('/reset/password/{token}', [VoterAuthController::class, 'resetView'])
                ->middleware('token.check')
                ->name('password.reset');

            Route::post('/reset/password', [VoterAuthController::class, 'resetStore'])
                ->name('password.update');
        });

        // Authenticated voter pages
        Route::middleware('voter.auth')->group(function () {

            Route::get('/ballot', [BallotController::class, 'ballot'])
                ->name('ballot');

            Route::post('/cast', [BallotController::class, 'cast'])
                ->middleware('throttle:vote')
                ->name('cast');

            Route::get('/confirmation', [BallotController::class, 'confirmation'])
                ->name('confirmation');

            Route::post('/logout', [VoterAuthController::class, 'logout'])
                ->name('logout');

            // Route::get('/profile', [VoterAuthController::class, 'showProfile'])->name('profile.show');

            Route::get('/profile/password/view', [VoterAuthController::class, 'editPassword'])
                ->name('password.form');

            Route::put('/profile/change/password/now', [VoterAuthController::class, 'updatePassword'])
                ->name('password.change');
        });
    });
});

// Admin Panel
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {

    Route::post('/logout', [LoginController::class, 'destroy'])
        ->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'show'])
        ->name('profile.show');

    Route::get('/profile/edit', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::put('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::get('/profile/password', [ProfileController::class, 'editPassword'])
        ->name('profile.password.edit');

    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])
        ->name('profile.password.update');

    // Organisation
    Route::get('/organization', [OrganizationController::class, 'show'])
        ->name('organization.show');

    Route::get('/organization/edit', [OrganizationController::class, 'edit'])
        ->name('organization.edit');

    Route::put('/organization', [OrganizationController::class, 'update'])
        ->name('organization.update');

    // Users
    Route::resource('users', UserController::class)->except(['show']);

    // Roles
    Route::resource('roles', RoleController::class)->except(['show']);

    // Elections
    Route::resource('elections', ElectionController::class);

    // Election Settings
    Route::get('/elections/{election}/settings', [ElectionController::class, 'settings'])
        ->name('elections.settings');

    Route::put('/elections/{election}/settings', [ElectionController::class, 'updateSettings'])
        ->name('elections.settings.update');

    // Election Resources
    Route::prefix('elections/{election}')->name('elections.')->group(function () {

        Route::resource('positions', PositionController::class)->except(['show', 'index']);

        Route::resource('candidates', CandidateController::class)->except(['show', 'index']);

        Route::resource('voters', VoterController::class);

        // Registration Form Builder
        Route::get('/registration-form', [RegistrationFieldController::class, 'show'])
            ->name('registration.show');

        Route::post('/registration-form', [RegistrationFieldController::class, 'store'])
            ->name('registration.store');

        Route::put('/registration-form', [RegistrationFieldController::class, 'update'])
            ->name('registration.update');

        Route::delete('/registration-form', [RegistrationFieldController::class, 'destroy'])
            ->name('registration.destroy');

        // Results
        Route::get('/results', [ResultController::class, 'show'])
            ->name('results');

        Route::get('/voters/assign/view', [VoterController::class, 'assign'])
            ->name('voters.assign.view');

        Route::post('/voters/assign/store', [VoterController::class, 'storeAssign'])
            ->name('voters.assign.store');

        // Voters import / export
        Route::post('/voters/import', [VoterController::class, 'import'])
            ->name('voters.import');

        // Route::get('/voters/export/download', [VoterController::class, 'export'])->name('voters.export.download');

        Route::get('/voters/import/template', [VoterController::class, 'downloadTemplate'])
            ->name('voters.import.template');

        Route::get('/voters/import-logs/log', [VoterController::class, 'importLogs'])
            ->name('voters.import-logs.log');

        Route::patch('/voters/{voter}/approve', [VoterController::class, 'approve'])
            ->name('voters.approve');

        Route::patch('/voters/{voter}/reject', [VoterController::class, 'reject'])
            ->name('voters.reject');

        // File preview
        Route::get('/voters/{voter}/file/{field}', [VoterController::class, 'previewFile'])
            ->where('field', '[a-zA-Z0-9_]+')
            ->name('voters.file-preview');

        // Vote reset
        // Route::post('/votes/reset', [AdminController::class, 'reset'])->name('votes.reset');
        // Route::post('/voters/{voterId}/votes/reset', [AdminController::class, 'resetForVoter'])->name('votes.reset-voter');
        // Route::post('/votes/restore/{batchId}', [AdminController::class, 'restore'])->name('votes.restore');
    });

    // Audit Logs
    Route::get('/audit-logs', [AuditLogController::class, 'index'])
        ->name('audit-logs');
});