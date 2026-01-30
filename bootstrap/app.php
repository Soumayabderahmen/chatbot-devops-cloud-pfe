<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Inertia\Inertia;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        api: __DIR__.'/../routes/api.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Gestion des erreurs 404 (page non trouvée)
        $exceptions->render(function (NotFoundHttpException $e, $request) {
            if (!$request->expectsJson() && !$request->is('api/*')) {
                \Log::info('404 Exception interceptée - Affichage page personnalisée');
                return Inertia::render('Errors/404')
                    ->toResponse($request)
                    ->setStatusCode(404);
            }
        });

        // Gestion des erreurs de modèle non trouvé
        $exceptions->render(function (ModelNotFoundException $e, $request) {
            if (!$request->expectsJson() && !$request->is('api/*')) {
                \Log::info('ModelNotFound Exception interceptée - Affichage page 404 personnalisée');
                return Inertia::render('Errors/404')
                    ->toResponse($request)
                    ->setStatusCode(404);
            }
        });

        // Gestion des erreurs d'authentification
        $exceptions->render(function (AuthenticationException $e, $request) {
            if (!$request->expectsJson() && !$request->is('api/*')) {
                return Inertia::render('Errors/401')
                    ->toResponse($request)
                    ->setStatusCode(401);
            }
        });

        // Gestion des erreurs d'autorisation
        $exceptions->render(function (AuthorizationException $e, $request) {
            if (!$request->expectsJson() && !$request->is('api/*')) {
                return Inertia::render('Errors/403')
                    ->toResponse($request)
                    ->setStatusCode(403);
            }
        });

        // Gestion des erreurs CSRF
        $exceptions->render(function (TokenMismatchException $e, $request) {
            if (!$request->expectsJson() && !$request->is('api/*')) {
                return Inertia::render('Errors/419')
                    ->toResponse($request)
                    ->setStatusCode(419);
            }
        });

        // Gestion générale des exceptions HTTP
        $exceptions->render(function (HttpExceptionInterface $e, $request) {
            if (!$request->expectsJson() && !$request->is('api/*')) {
                $status = $e->getStatusCode();
                \Log::info("HTTP Exception {$status} interceptée");
                
                if (in_array($status, [401, 403, 404, 419, 429, 500, 503])) {
                    return Inertia::render("Errors/{$status}")
                        ->toResponse($request)
                        ->setStatusCode($status);
                }
            }
        });

        // Fallback pour toutes les autres exceptions
        $exceptions->render(function (Throwable $e, $request) {
            if (!$request->expectsJson() && !$request->is('api/*')) {
                \Log::error('Exception non gérée: ' . get_class($e) . ' - ' . $e->getMessage());
                
                return Inertia::render('Errors/500')
                    ->toResponse($request)
                    ->setStatusCode(500);
            }
        });
    })
    ->create();