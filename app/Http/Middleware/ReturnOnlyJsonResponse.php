<?php

namespace App\Exceptions;

use Throwable;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $throwable
     * @return void
     */
    public function report(Throwable $throwable)
    {
        parent::report($throwable);
    }

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render the exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function render($request, Throwable $throwable)
    {
        if ($throwable instanceof NotFoundHttpException) {
            return response()->json([
                'success' => false,
                'message' => 'Error, Resource Not Found',
                'error' => true
            ], 405);
        }

        if ($throwable instanceof MethodNotAllowedHttpException) {
            return response()->json([
                'success' => false,
                'message' => 'Error, Method Not Allowed',
                'error' => true
            ], 405);
        }

        if ($throwable instanceof \Illuminate\Database\QueryException) {
            return response()->json([
                'success' => false,
                'message' => 'Server Error',
                'error' => true
            ], 500);
        }

        // JWT Errors
        if ($throwable instanceof UnauthorizedHttpException) {
            return response()->json([
                'success' => false,
                'message' => 'Error, Unauthorized Token Invalid or Not provided',
                'error' => true
            ], 401);
        }

        if ($throwable instanceof TokenExpiredException) {
            return response()->json([
                'success' => false,
                'message' => 'Error, Token Expired',
                'error' => true
            ], $throwable->getMessage());
        }

        if ($throwable instanceof TokenInvalidException) {
            return response()->json([
                'success' => false,
                'message' => 'Error, Token Invalid',
                'error' => true
            ], $throwable->getMessage());
        }

        return parent::render($request, $throwable);
    }
}
