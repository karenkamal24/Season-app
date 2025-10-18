<?php


use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

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
->withSchedule(function (Illuminate\Console\Scheduling\Schedule $schedule) {
    $schedule->command('users:delete-unverified')->everyTenMinutes();
})


 ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\Throwable $e, Request $request) {
            if ($e instanceof ValidationException) {
                return response()->json([
                    'status' => Response::HTTP_BAD_REQUEST,
                    'message' => 'Validation Error',
                    'errors' => $e->errors()
                ], Response::HTTP_BAD_REQUEST);
            }


            if ($e instanceof AuthenticationException) {
                if ($request->expectsJson() || $request->is('api/*')) {
                    return response()->json([
                        'status' => Response::HTTP_UNAUTHORIZED,
                        'message' => $e->getMessage() ?? 'Unauthenticated'
                    ], Response::HTTP_UNAUTHORIZED);
                }
                return redirect()->guest(route('filament.admin.auth.login'));
            }

            if ($e instanceof ModelNotFoundException) {
                $model = strtolower(class_basename($e->getModel()));
                return response()->json([
                    'status' => Response::HTTP_NOT_FOUND,
                    'message' => $e->getMessage() ?? "{$model} not found"
                ], Response::HTTP_NOT_FOUND);
            }

            if ($e instanceof NotFoundHttpException) {
                return response()->json([
                    'status' => Response::HTTP_NOT_FOUND,
                    'message' => $e->getMessage() ?? 'The specified URL cannot be found'
                ], Response::HTTP_NOT_FOUND);
            }

            if ($e instanceof \InvalidArgumentException) {
                return response()->json([
                    'status' => Response::HTTP_BAD_REQUEST,
                    'message' => $e->getMessage()
                ], Response::HTTP_BAD_REQUEST);
            }

            if ($e instanceof MethodNotAllowedHttpException) {
                return response()->json([
                    'status' => Response::HTTP_METHOD_NOT_ALLOWED,
                    'message' => $e->getMessage() ?? 'The specified method for the request is invalid'
                ], Response::HTTP_METHOD_NOT_ALLOWED);
            }

            if ($e instanceof ThrottleRequestsException) {
                return response()->json([
                    'status' => Response::HTTP_TOO_MANY_REQUESTS,
                    'message' => $e->getMessage() ?? 'Too many requests'
                ], Response::HTTP_TOO_MANY_REQUESTS);
            }

            if ($e instanceof HttpExceptionInterface) {
                return response()->json([
                    'status' => $e->getStatusCode(),
                    'message' => $e->getMessage() ?: 'HTTP error'
                ], $e->getStatusCode());
            }

            if (config('app.debug')) {
                return response()->json([
                    'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                    'message' => $e->getMessage(),
                    'debug' => [
                        'exception' => get_class($e),
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                        'trace' => collect($e->getTrace())->take(5),
                    ]
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            Log::error('Unexpected exception caught', [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => collect($e->getTrace())->take(5)->toArray()
            ]);

            return response()->json([
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'Unexpected error. Try later'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        });
    })->create();

