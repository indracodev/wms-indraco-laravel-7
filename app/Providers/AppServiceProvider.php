<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::defaultView('vendor.pagination.default');

        \Illuminate\Support\Facades\Blade::if('canAccessModule', function (string $module, string $action = 'read') {
            return auth()->check() && auth()->user()->hasPermission($module, $action);
        });
    }
}
