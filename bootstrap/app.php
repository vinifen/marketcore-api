<?php

use App\Exceptions\ApiException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Responses\ApiResponse;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
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
                $e->getStatusCode()
            );
        });

        $exceptions->render(function (AuthenticationException $e) {
            return ApiResponse::error(
                $e->getMessage(),
                null,
                $e->getCode() ?: 401
            );
        });

        $exceptions->render(function (AuthorizationException $e) {
            return ApiResponse::error(
                $e->getMessage(),
                null,
                $e->getCode() ?: 403
            );
        });

        $exceptions->render(function (QueryException $e) {
            return ApiResponse::error(
                $e->getMessage(),
                null,
                500
            );
        });

        $exceptions->render(function (MethodNotAllowedHttpException $e) {
            return ApiResponse::error(
                $e->getMessage(),
                null,
                $e->getStatusCode()
            );
        });

        $exceptions->render(function (ModelNotFoundException $e) {
            return ApiResponse::error(
                $e->getMessage(),
                null,
                $e->getCode() ?: 404
            );
        });

        $exceptions->render(function (AccessDeniedHttpException $e) {
            return ApiResponse::error(
                $e->getMessage(),
                null,
                $e->getCode() ?: 403
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
