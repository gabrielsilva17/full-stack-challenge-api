<?php

namespace App\Providers;

use App\Repositories\ProfileRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register repository bindings.
     */
    public function register(): void
    {
        $this->app->singleton(UserRepository::class, function($app){
            return new UserRepository($app->make(\App\Models\User::class));
        });
        $this->app->singleton(ProfileRepository::class, function($app){
            return new ProfileRepository($app->make(\App\Models\Profile::class));
        });
    }
}
