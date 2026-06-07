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
};
use App\Services\Auth\LoginService;
use App\Services\{
    UserService,
    VoterService,
    ElectionService,
    PositionService,
    CandidateService,
    RegistrationFieldService,
};


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
