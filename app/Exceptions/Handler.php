<?php

namespace App\Exceptions;

use App\Models\ErrorLog;
use App\Models\User;
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

        // To be uncommented during production
        $this->renderable(function (Throwable $e, $request) {
            return $this->handleException($e, $request);
        });
    }

    private function handleException(Throwable $exception, $request): JsonResponse 
    {
        //echo $exception->getMessage();


        // Default response format
        $response = [
            'status' => "failed",
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
        } elseif ($exception instanceof \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException) {
            $response['message'] = 'Unauthorized!';
            $status = 401;
        } elseif ($exception instanceof \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException) {
            $response['message'] = 'Access denied!';
            $status = 403;
        } else {
            //$response['message'] = 'Server Error';
            // $response['message'] = $exception->__toString();
            $response['message'] = $exception->getMessage();
            $status = 500;
        }

        // Log the exception if needed
        ErrorLog::create([
            "class" => get_class($exception), 
            "method_and_line_number" => $this->getFunctionName($exception).' LINE NUMBER : '.$exception->getLine(), 
            "error_description" => $exception->getMessage(), 
            "related_user" => User::getLoggedInUserEmail(), 
            "related_user_ip" => request()->ip()
        ]);

        return response()->json($response, $status);
    }

    protected function getFunctionName(Throwable $exception): ?string
    {
        $trace = $exception->getTrace();
        return $trace[0]['function'] ?? null;
    }

}
