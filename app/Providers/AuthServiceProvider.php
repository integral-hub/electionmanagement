<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        \App\Models\User::class => \App\Policies\UserPolicy::class,
        \App\Models\Role::class => \App\Policies\RolePolicy::class,
        \App\Models\Election::class => \App\Policies\ElectionPolicy::class,
        \App\Models\Voter::class => \App\Policies\VoterPolicy::class,
        \App\Models\Candidate::class => \App\Policies\CandidatePolicy::class,
        \App\Models\Position::class => \App\Policies\PositionPolicy::class,
        \App\Models\Organization::class => \App\Policies\OrganizationPolicy::class,
        \App\Models\VotersImportLog::class => \App\Policies\VotersImportLogPolicy::class,
        \App\Models\ElectionSetting::class => \App\Policies\ElectionSettingsPolicy::class,
        \App\Models\ActivityLog::class => \App\Policies\ActivityLogPolicy::class,
        \App\Models\Vote::class => \App\Policies\VotePolicy::class,
    ];
    public function boot(): void
    {
         $this->registerPolicies();

        ResetPassword::createUrlUsing(function ($notifiable, string $token) {

            if ($notifiable instanceof \App\Models\Voter) {
                    $election = request()->route('election');

                return route('voter.password.reset', [
                    'election' => $election->slug,
                    'token' => $token,
                    'email' => $notifiable->getEmailForPasswordReset(),
                ]);
            }
            
            return route('password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ]);
        });
    
    }
}