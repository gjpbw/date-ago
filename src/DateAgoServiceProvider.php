<?php
namespace Gjpbw\DateAgo;

use Illuminate\Support\ServiceProvider;

class DateAgoServiceProvider  extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([__DIR__ . '/../config/' => config_path('gjpbw/')]);
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'gjpbw.date-ago');
    }


    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(DateAgo::class);

    }
}