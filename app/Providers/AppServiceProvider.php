<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
        // register bindings, singletons, etc.
    }

    /**
     * Bootstrap any application services.
     */
// ...existing code...
    public function boot()
    {
        if ($this->app->environment('production')) {
            \config(['view.compiled' => sys_get_temp_dir()]);
        }
    }
}
// ...existing code...