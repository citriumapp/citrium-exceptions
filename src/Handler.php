<?php

namespace Citrium\Exceptions;

use Throwable;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException as LaravelTokenMismatchException;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Validation\ValidationException;

class Handler extends ExceptionHandler
{
    public function report(Throwable $throwable)
    {
        parent::report($throwable);
    }

    public function render($request, Throwable $throwable)
    {
        if ($throwable instanceof ValidationException) {
            $throwable = new JsonValidationException($throwable);
        }

        if ($throwable instanceof LaravelTokenMismatchException) {
            $throwable = new TokenMismatchException;
        }

        if ($throwable instanceof ModelNotFoundException) {
            $throwable = new ResourceNotFoundException($throwable);
        }

        if ($throwable instanceof UnauthorizedException) {
            $throwable = new NotAuthorizedException;
        }

        if ($throwable instanceof Exception) {
            return $request->wantsJson()
                ? new JsonResponse($throwable->toArray(), $throwable->status())
                : new Response($throwable->errorCode(), $throwable->status());
        }

        return parent::render($request, $throwable);
    }

    protected function exceptionContext(Throwable $e): array
    {
        return $e instanceof Exception ? $e->toArray() : parent::exceptionContext($e);
    }

    protected function shouldntReport(Throwable $e): bool
    {
        return $e instanceof Exception ? $e->reportable() : parent::shouldntReport($e);
    }
}
