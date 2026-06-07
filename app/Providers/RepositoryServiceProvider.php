<?php

namespace App\Providers;

use App\Models\ElectionSetting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use App\Repositories\ElectionSettingRepository;
use App\Repositories\Interfaces\ElectionSettingRepositoryInterface;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Binds interfaces to their implementations.
     * @var array<string, string>
     */
    public $bindings = [
        ElectionSettingRepositoryInterface::class => ElectionSettingRepository::class
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->app->when(ElectionSettingRepository::class)
            ->needs(Model::class)
            ->give(ElectionSetting::class);

    }
}
