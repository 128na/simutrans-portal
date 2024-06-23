<?php

declare(strict_types=1);

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

final class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     */
    protected $dontReport = [
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    #[\Override]
    public function register(): void {}

    /**
     * Report or log an exception.
     */
    #[\Override]
    public function report(Throwable $throwable): void
    {
        parent::report($throwable);
    }

    /**
     * Render an exception into an HTTP response.
     */
    #[\Override]
    public function render($request, Throwable $throwable)
    {
        return parent::render($request, $throwable);
    }
}
