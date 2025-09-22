<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        // 處理CSRF Token Mismatch異常
        if ($exception instanceof TokenMismatchException) {
            // 如果是AJAX請求，返回JSON響應
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'error' => 'CSRF token mismatch',
                    'message' => '安全驗證已過期，請重新載入頁面',
                    'reload' => true
                ], 419);
            }
            
            // 一般請求返回自定義錯誤頁面
            return response()->view('errors.csrf', [], 419);
        }

        return parent::render($request, $exception);
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }
        
        //custom 
        $guard = array_get($exception->guards(),0);

        switch ($guard) {
            case 'admin':
                $login = 'admin.login';
                break;
            
            default:
                $login = 'login';
                break;
        }
        return redirect()->guest(route($login));
        //custom

        // return redirect()->guest(route('login'));
    }
}
