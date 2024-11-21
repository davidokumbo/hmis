<?php

namespace App\Exceptions;

use App\Models\ErrorLog;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->renderable(function (Throwable $e, $request) {
            return $this->handleException($e, $request);
        });
    }

    private function handleException(Throwable $exception, $request): JsonResponse 
    {
        // Default response format
        $response = [
            'success' => false,
            'message' => $exception->getMessage(),
        ];

        // Customize response based on the exception type
        if ($exception instanceof \Illuminate\Validation\ValidationException) {
            $response['message'] = 'Validation Error';
            $response['errors'] = $exception->errors();
            $status = 422;
        } elseif ($exception instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
            $response['message'] = 'Route Not Found';
            $status = 404;
        } else {
            $response['message'] = 'Server Error';
            $status = 500;
        }

        // Log the exception if needed
        ErrorLog::create([]);

        return response()->json($response, $status);
    }
}
