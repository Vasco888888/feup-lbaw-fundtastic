<?php


use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Handle authentication failures (not logged in or wrong guard)
        $exceptions->render(function (AuthenticationException $e, $request) {
            // Check if user is trying to access admin routes
            if (in_array('admin', $e->guards())) {
                session()->flash('error', 'You are not authorized to access that page.');
                return redirect()->route('landing');
            }
            // Otherwise, redirect to login
            return redirect()->route('login');
        });
        
        // Handle authorization failures (logged in but not permitted)
        $exceptions->render(function (AuthorizationException $e, $request) {
            session()->flash('error', 'You are not authorized to access that page.');
            return redirect()->route('landing');
        });
        
        $exceptions->render(function (QueryException $e, $request) {
            session()->flash('error', 'Invalid user or page.');
            return redirect()->route('landing');
        });
        
        $exceptions->render(function (ModelNotFoundException $e, $request) {
            session()->flash('error', 'User or resource not found.');
            return redirect()->route('landing');
        });
    })->create();
