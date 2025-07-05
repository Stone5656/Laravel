<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    public function render($request, Throwable $e)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => $e->getMessage(),
                'type' => class_basename($e),
            ], $this->getStatusCode($e));
        }

        return parent::render($request, $e);
    }

    protected function getStatusCode(Throwable $e): int
    {
        return match (true) {
            $e instanceof \Illuminate\Auth\AuthenticationException => 401,
            $e instanceof \Illuminate\Auth\Access\AuthorizationException => 403,
            $e instanceof \Illuminate\Validation\ValidationException => 422,
            $e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException => 404,
            default => 500,
        };
    }
}

