<?php

use App\Http\Middleware\AssignRequestId;
use App\Http\Middleware\AuthenticateDevice;
use App\Http\Middleware\EnsureOrganizationMembership;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append(AssignRequestId::class);

        $middleware->alias([
            'device.auth' => AuthenticateDevice::class,
            'organization' => EnsureOrganizationMembership::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (Throwable $exception, Request $request) {
            if (! $request->is('api/*')) {
                return null;
            }

            $requestId = $request->attributes->get('request_id');
            $payload = fn (string $code, string $message, array $details = [], int $status = 400) => response()->json([
                'error' => [
                    'code' => $code,
                    'message' => $message,
                    'details' => $details,
                    'request_id' => $requestId,
                ],
            ], $status);

            if ($exception instanceof ValidationException) {
                return $payload('VALIDATION_ERROR', 'The submitted data is invalid.', $exception->errors(), 422);
            }

            if ($exception instanceof AuthenticationException) {
                return $payload('UNAUTHENTICATED', 'Authentication is required.', [], 401);
            }

            if ($exception instanceof AuthorizationException) {
                return $payload('FORBIDDEN', 'You are not authorized to perform this action.', [], 403);
            }

            if ($exception instanceof ModelNotFoundException || $exception instanceof NotFoundHttpException) {
                return $payload('NOT_FOUND', 'The requested resource was not found.', [], 404);
            }

            if ($exception instanceof ThrottleRequestsException) {
                return $payload('RATE_LIMITED', 'Too many requests. Please retry later.', [], 429);
            }

            if ($exception instanceof HttpExceptionInterface) {
                return $payload('HTTP_ERROR', $exception->getMessage() ?: 'The request could not be completed.', [], $exception->getStatusCode());
            }

            report($exception);

            return $payload('SERVER_ERROR', 'An unexpected server error occurred.', [], 500);
        });
    })->create();
