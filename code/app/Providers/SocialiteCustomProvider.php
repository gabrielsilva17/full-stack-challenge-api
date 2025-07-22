<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\Facades\Socialite;
use App\Extensions\Socialite\GovBrProvider;

class SocialiteCustomProvider extends ServiceProvider
{
    public function boot()
    {
        Socialite::extend('govbr', function($app) {
            $config = $app['config']['services.govbr'];
            return Socialite::buildProvider(GovBrProvider::class, $config);
        });
    }
}
