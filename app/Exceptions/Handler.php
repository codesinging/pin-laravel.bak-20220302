<?php

namespace App\Exceptions;

use App\Support\Routing\ApiResponse;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
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
     * Handle the exception data
     *
     * @param $request
     * @param Throwable $e
     *
     * @return JsonResponse|Response|\Symfony\Component\HttpFoundation\Response
     * @throws Throwable
     */
    public function render($request, Throwable $e): Response|JsonResponse|\Symfony\Component\HttpFoundation\Response
    {
        if ($request->expectsJson()) {
            if ($e instanceof ValidationException) {
                return ApiResponse::error($e->validator->errors()->first(), ErrorCode::VALIDATION_ERROR, $e->validator->getMessageBag());
            } elseif ($e instanceof ApiException){
                return ApiResponse::error($e->getMessage(), $e->getCode());
            }
        }

        return parent::render($request, $e);
    }
}
