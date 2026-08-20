<?php

use App\Http\Controllers\Api\V1\AuditLogController;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Auth\PasswordController;
use App\Http\Controllers\Api\V1\Auth\VoterAuthController;
use App\Http\Controllers\Api\V1\BallotController;
use App\Http\Controllers\Api\V1\CandidateController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\ElectionController;
use App\Http\Controllers\Api\V1\OrganizationController;
use App\Http\Controllers\Api\V1\PositionController;
use App\Http\Controllers\Api\V1\ProfileController;
use App\Http\Controllers\Api\V1\RegistrationFieldController;
use App\Http\Controllers\Api\V1\ResultController;
use App\Http\Controllers\Api\V1\RoleController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\VoterController;
use App\Http\Middleware\Api\EnsureVoterTokenAuthenticated;
use App\Http\Middleware\Api\SetPermissionTeamApi;
use App\Http\Middleware\AuthRequestLogs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->name('api.v1.')->group(function () {

    // Admin auth (public)
    Route::post('/auth/register', [AuthController::class, 'register'])
        ->middleware('throttle:register')
        ->name('auth.register');

    Route::post('/auth/login', [AuthController::class, 'login'])
        ->middleware('throttle:login')
        ->name('auth.login');

    Route::post('/auth/forgot-password', [PasswordController::class, 'forgot'])
        ->middleware('throttle:password-reset')
        ->name('auth.forgot-password');

    Route::post('/auth/reset-password', [PasswordController::class, 'reset'])
        ->name('auth.reset-password');

    // Admin panel (authenticated staff/admin tokens)
    Route::middleware(['auth:sanctum', SetPermissionTeamApi::class, AuthRequestLogs::class])
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {

            Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
            Route::get('/me', [AuthController::class, 'me'])->name('me');

            // Dashboard
            Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

            // Profile
            Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
            Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
            Route::put('/profile/password', [PasswordController::class, 'update'])->name('profile.password.update');

            // Organisation
            Route::get('/organization', [OrganizationController::class, 'show'])->name('organization.show');
            Route::put('/organization', [OrganizationController::class, 'update'])->name('organization.update');

            // Users
            Route::apiResource('users', UserController::class)->except(['show']);

            // Roles
            Route::apiResource('roles', RoleController::class)->except(['show']);

            // Elections
            Route::apiResource('elections', ElectionController::class);

            // Election Settings
            Route::get('/elections/{election}/settings', [ElectionController::class, 'settings'])
                ->name('elections.settings');

            Route::put('/elections/{election}/settings', [ElectionController::class, 'updateSettings'])
                ->name('elections.settings.update');

            // Election Resources
            Route::prefix('elections/{election}')->name('elections.')->group(function () {

                Route::post('/positions', [PositionController::class, 'store'])->name('positions.store');
                Route::put('/positions/{position}', [PositionController::class, 'update'])->name('positions.update');
                Route::delete('/positions/{position}', [PositionController::class, 'destroy'])->name('positions.destroy');

                Route::post('/candidates', [CandidateController::class, 'store'])->name('candidates.store');
                Route::put('/candidates/{candidate}', [CandidateController::class, 'update'])->name('candidates.update');
                Route::delete('/candidates/{candidate}', [CandidateController::class, 'destroy'])->name('candidates.destroy');

                Route::get('/voters', [VoterController::class, 'index'])->name('voters.index');
                Route::post('/voters', [VoterController::class, 'store'])->name('voters.store');
                Route::get('/voters/assign', [VoterController::class, 'assignable'])->name('voters.assign');
                Route::post('/voters/assign', [VoterController::class, 'storeAssign'])->name('voters.assign.store');
                Route::post('/voters/import', [VoterController::class, 'import'])->name('voters.import');
                Route::get('/voters/import/template', [VoterController::class, 'downloadTemplate'])->name('voters.import.template');
                Route::get('/voters/import-logs', [VoterController::class, 'importLogs'])->name('voters.import-logs');

                Route::get('/voters/{voter}', [VoterController::class, 'show'])->name('voters.show');
                Route::put('/voters/{voter}', [VoterController::class, 'update'])->name('voters.update');
                Route::delete('/voters/{voter}', [VoterController::class, 'destroy'])->name('voters.destroy');
                Route::patch('/voters/{voter}/approve', [VoterController::class, 'approve'])->name('voters.approve');
                Route::patch('/voters/{voter}/reject', [VoterController::class, 'reject'])->name('voters.reject');

                Route::get('/voters/{voter}/file/{field}', [VoterController::class, 'previewFile'])
                    ->where('field', '[a-zA-Z0-9_]+')
                    ->name('voters.file-preview');

                // Registration Form Builder
                Route::get('/registration-form', [RegistrationFieldController::class, 'show'])->name('registration.show');
                Route::post('/registration-form', [RegistrationFieldController::class, 'store'])->name('registration.store');
                Route::put('/registration-form', [RegistrationFieldController::class, 'update'])->name('registration.update');
                Route::delete('/registration-form', [RegistrationFieldController::class, 'destroy'])->name('registration.destroy');

                // Results
                Route::get('/results', [ResultController::class, 'show'])->name('results');
            });

            // Audit Logs
            Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs');
        });

    // Voter portal (per-election, public)
    Route::prefix('elections/{election:slug}')->name('voter.')->group(function () {

        Route::get('/status', [VoterAuthController::class, 'status'])->name('status');

        Route::post('/auth/register', [VoterAuthController::class, 'register'])
            ->middleware('throttle:register')
            ->name('register');

        Route::post('/auth/login', [VoterAuthController::class, 'login'])
            ->middleware('throttle:voter-login')
            ->name('login');

        Route::post('/auth/2fa/verify', [VoterAuthController::class, 'verifyTwoFactor'])
            ->middleware('throttle:otp')
            ->name('2fa.verify');

        Route::post('/auth/resend-otp', [VoterAuthController::class, 'resendOtp'])
            ->middleware('throttle:resend-otp')
            ->name('resend-otp');

        Route::post('/auth/verify-email', [VoterAuthController::class, 'verifyEmail'])
            ->middleware('throttle:otp')
            ->name('verify-email');

        Route::get('/auth/verify-email/{token}', [VoterAuthController::class, 'verifyEmailLink'])
            ->name('verify-email.link');

        Route::post('/auth/forgot-password', [VoterAuthController::class, 'forgotPassword'])
            ->middleware('throttle:password-reset')
            ->name('forgot-password');

        Route::post('/auth/reset-password', [VoterAuthController::class, 'resetPassword'])
            ->name('reset-password');

        // Voter portal (authenticated voter tokens)
        Route::middleware(['auth:sanctum', EnsureVoterTokenAuthenticated::class])->group(function () {

            Route::get('/auth/me', [VoterAuthController::class, 'me'])->name('me');
            Route::post('/auth/logout', [VoterAuthController::class, 'logout'])->name('logout');
            Route::put('/auth/password', [VoterAuthController::class, 'updatePassword'])->name('password.update');

            Route::get('/ballot', [BallotController::class, 'ballot'])->name('ballot');

            Route::post('/cast', [BallotController::class, 'cast'])
                ->middleware('throttle:vote')
                ->name('cast');

            Route::get('/confirmation', [BallotController::class, 'confirmation'])->name('confirmation');
        });
    });
});
