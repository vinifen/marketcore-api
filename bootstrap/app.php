<?php

use App\Exceptions\ApiException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Responses\ApiResponse;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\QueryException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (NotFoundHttpException $e) {
            return ApiResponse::error(
                $e->getMessage(),
                null,
                404
            );
        });

        $exceptions->render(function (AuthenticationException $e) {
            throw new ApiException(
                $e->getMessage(),
                null,
                401,
            );
        });

        $exceptions->render(function (AuthorizationException $e) {
            throw new ApiException(
                $e->getMessage(),
                null,
                403
            );
        });

        $exceptions->render(function (QueryException $e) {
            return ApiResponse::error(
                $e->getMessage(),
                500
            );
        });

        $exceptions->render(function (ApiException $e) {
            return ApiResponse::error(
                $e->getMessage(),
                $e->toArray(),
                $e->getStatusCode()
            );
        });

        $exceptions->render(function (\Throwable $e) {
            return ApiResponse::error(
                'Internal server error.',
                app()->isProduction() ? 'Something went wrong.' : $e->getMessage(),
                500
            );
        });

    })
    ->create();
