<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Interfaces\Auth\LoginInterface;
use App\Services\Interfaces\{
    UserInterface,
    VoterInterface,
    ElectionInterface,
    PositionInterface,
    CandidateInterface,
    RegistrationFieldInterface,
    RoleInterface,
    FileUploadInterface,
    PortalChecklistInterface,
};
use App\Services\Auth\LoginService;
use App\Services\{
    UserService,
    VoterService,
    ElectionService,
    PositionService,
    CandidateService,
    RegistrationFieldService,
    RoleService,
    FileUploadService,
    PortalChecklistService,
};
use App\Services\Auth\PasswordService;
use App\Services\Auth\VoterAuthService;
use App\Services\Auth\VoterEmailVerificationService;
use App\Services\Interfaces\Auth\VoterEmailVerificationInterface;
use App\Services\Interfaces\Auth\PasswordInterface;
use App\Services\Interfaces\Auth\VoterAuthInterface;

class InterfaceServiceProvider extends ServiceProvider
{

    /**
     * Binds interfaces to their implementations.
     * @var array<string, string>
     */
      public $bindings = [
        LoginInterface::class => LoginService::class,
        UserInterface::class => UserService::class,
        VoterInterface::class => VoterService::class,
        ElectionInterface::class => ElectionService::class,
        PositionInterface::class => PositionService::class,
        CandidateInterface::class => CandidateService::class,
        RegistrationFieldInterface::class => RegistrationFieldService::class,
        RoleInterface::class => RoleService::class,
        FileUploadInterface::class => FileUploadService::class,
        PasswordInterface::class => PasswordService::class,
        VoterAuthInterface::class => VoterAuthService::class,
        VoterEmailVerificationInterface::class => VoterEmailVerificationService::class,
        PortalChecklistInterface::class => PortalChecklistService::class,
    ];

    /**
     * Register services.
     */
    public function register(): void
    {

    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {

    }
}
