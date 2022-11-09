<?php

namespace App\Providers;

use App\Services\ExchangeRatesIO\ExchangeRatesIOService;
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
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->singleton(
            abstract: ExchangeRatesIOService::class,
            concrete: fn() => new ExchangeRatesIOService(
                baseUrl: strval(config('services.exchange-rates-io.url')),
                apiKey: strval(config('services.exchange-rates-io.key')),
            ),
        );
    }
}
