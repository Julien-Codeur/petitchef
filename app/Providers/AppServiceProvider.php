<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register policies
        $this->registerPolicies();
    }

    /**
     * Register the application's policies
     */
    protected function registerPolicies(): void
    {
        \Gate::policy(\App\Models\Dish::class, \App\Policies\DishPolicy::class);
        \Gate::policy(\App\Models\Order::class, \App\Policies\OrderPolicy::class);
        \Gate::policy(\App\Models\Report::class, \App\Policies\ReportPolicy::class);
    }
}
