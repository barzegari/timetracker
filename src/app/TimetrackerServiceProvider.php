<?php

namespace Barzegari\Timetracker;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class TimetrackerServiceProvider extends ServiceProvider
{

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        // Register Our Package routes
        require __DIR__.'/../routes/api.php';

        // Register database migration
        $this->loadMigrationsFrom(__DIR__.'/../migrations');
        $this->publishes([__DIR__.'/../migrations' => database_path('migrations')], 'migrations');


    }

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {

    }
}