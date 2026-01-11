<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\App;

class AppServiceProvider extends ServiceProvider
{

public function boot()
{
    if (App::environment('production')) {
        config([
            'view.compiled' => sys_get_temp_dir(),
            'cache.stores.file.path' => sys_get_temp_dir(),
            'session.files' => sys_get_temp_dir(),
        ]);
    }
}

}
