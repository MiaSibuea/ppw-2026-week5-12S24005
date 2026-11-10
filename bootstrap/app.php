<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request, Throwable $exception): bool => $request->is('api/*') || $request->expectsJson()
        );

        $exceptions->render(function (Throwable $exception, Request $request) {
            if (! $request->is('api/*')) {
                return null;
            }

            $status = match (true) {
                $exception instanceof ValidationException => $exception->status,
                $exception instanceof AuthenticationException => 401,
                $exception instanceof ModelNotFoundException => 404,
                $exception instanceof HttpExceptionInterface => $exception->getStatusCode(),
                default => 500,
            };

            $titles = [
                401 => 'Unauthorized',
                403 => 'Forbidden',
                404 => 'Not Found',
                409 => 'Conflict',
                422 => 'Unprocessable Content',
                429 => 'Too Many Requests',
                502 => 'Bad Gateway',
                503 => 'Service Unavailable',
            ];
            $detail = $status >= 500
                ? 'Terjadi kesalahan internal pada server.'
                : ($exception->getMessage() ?: ($titles[$status] ?? 'Permintaan tidak dapat diproses.'));

            $problem = [
                'type' => 'about:blank',
                'title' => $titles[$status] ?? 'Internal Server Error',
                'status' => $status,
                'detail' => $detail,
                'instance' => $request->getPathInfo(),
            ];
            if ($exception instanceof ValidationException) {
                $problem['errors'] = $exception->errors();
            }

            return response()->json($problem, $status)
                ->header('Content-Type', 'application/problem+json');
        });
    })->create();
