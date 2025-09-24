<?php

namespace App\Providers;

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
        $this->app->singleton(
            \Laravel\Fortify\Contracts\VerifyEmailViewResponse::class,
            function () {
                return new class implements \Laravel\Fortify\Contracts\VerifyEmailViewResponse {
                    public function toResponse($request)
                    {
                        return view('auth.verify-email');
                    }
                };
            }
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
