<?php

use App\Enums\AuditAction;
use App\Http\Middleware\AddSecurityHeaders;
use App\Http\Middleware\SetApplicationLocale;
use App\Models\AdminActivityLog;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            SetApplicationLocale::class,
            AddSecurityHeaders::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->renderable(function (HttpException $e): null {
            if ($e->getStatusCode() !== 403) {
                return null;
            }

            $request = request();

            if (! str_starts_with($request->path(), 'admin-portal')) {
                return null;
            }

            try {
                AdminActivityLog::create([
                    'user_id' => auth()->id(),
                    'action' => AuditAction::AccessDenied->value,
                    'description' => "403 op {$request->path()}",
                    'ip_address' => $request->ip(),
                ]);
            } catch (Throwable) {
                // never interfere with the 403 response
            }

            return null;
        });
    })->create();
