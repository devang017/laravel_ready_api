<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        apiPrefix: 'api/v1',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        $exceptions->render(function (
            \Throwable $e,
            \Illuminate\Http\Request $request
        ) {
            if ($request->is('api/*')) {
                $statusCode = match (true) {
                    $e instanceof AuthenticationException => 401,
                    $e instanceof HttpExceptionInterface => $e->getStatusCode(),
                    default => $e->getCode() ?: 500
                };

                return response()->json([
                    'status' => false,
                    'message' =>  config('params.app_env') == 'production' ? 'Something went wrong.' : $e->getMessage(),
                    'data' => []
                ], $statusCode);
            }
        });
    })->create();
