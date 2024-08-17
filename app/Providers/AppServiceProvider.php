<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Stripe\StripeClient;

class AppServiceProvider extends ServiceProvider
{
    private const TEST_ENVIRONMENT = 'testing';
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if($this->app->environment() === self::TEST_ENVIRONMENT) {
            return;
        }

        $this->app->bind(StripeClient::class, function() {
            return new StripeClient(env('STRIPE_SECRET_KEY'));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
