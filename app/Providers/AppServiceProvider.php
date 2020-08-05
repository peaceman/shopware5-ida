<?php

namespace App\Providers;

use App\Domain\DataExtractor;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(DataExtractor::class, function ($app) {
            return new DataExtractor($app['db.connection']);
        });
    }
}
