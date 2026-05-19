<?php

namespace App\Providers;

use Carbon\CarbonInterval;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Response;
use Illuminate\Cache\RateLimiting\Limit;
use Laravel\Passport\Passport;
use Illuminate\Http\Request;

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
        // rate limiter
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by(
                $request->user()?->id ?: $request->ip()
            );
        });

        // passport token settings
        Passport::tokensExpireIn(CarbonInterval::days(15));
        Passport::refreshTokensExpireIn(CarbonInterval::days(30));
        Passport::personalAccessTokensExpireIn(CarbonInterval::months(6));

        // api response format
        Response::macro('success', function (string $message, mixed $data, int $code = 200) {
            return Response::json([
                'status'  => true,
                'message' => $message,
                'data'    => $data,
            ], $code);
        });

        Response::macro('error', function (string $message, mixed $errors = [], int $code = 404) {
            return Response::json([
                'status' => false,
                'message' => $message,
                'errors' => $errors,
            ], $code);
        });
    }
}
