<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Validation\ValidationException;
use Modules\Core\Exceptions\BaseApiException;
use Modules\Core\Exceptions\BaseException;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;
use Throwable;

/**
 * Class Handler
 * @package App\Exceptions
 */
class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param \Throwable $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }


    /**
     * @param \Illuminate\Http\Request $request
     * @param Throwable $exception
     * @return RedirectResponse|Response
     * @throws Throwable
     */
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof BaseApiException) {
            return response()->json(
                ['message' => $exception->getMessage()],
                $exception->getStatusCode()
            );
        }

        switch (true) {
            case $exception instanceof PostTooLargeException:
                if ($request->ajax()) {
                    return response()->json(
                        ['message' => 'Too big file'],
                        $exception->getStatusCode()
                    );
                }

                return response()->make(view('errors.post-too-large'));
            case $exception instanceof ValidationException:
            case $exception instanceof PermissionDoesNotExist:
            case $exception instanceof BaseException:
                return \Redirect::back()->with(
                    'fail',
                    $this->getExceptionMessage($exception)
                )->withInput();
        }

        return parent::render($request, $exception);
    }

    /**
     * [getErrorMessage description]
     * @param Throwable $exception
     * @return string
     */
    private function getExceptionMessage(Throwable $exception): string
    {
        if ($exception instanceof ValidationException) {
            $validationErrors = $exception->validator
                ->getMessageBag()
                ->getMessages();

            $errorsArr = array_map(function ($value) {
                return implode(',', $value);
            }, $validationErrors);

            return implode(',', $errorsArr);
        }

        return $exception->getMessage();
    }

    /**
     *
     * @param \Illuminate\Http\Request $request
     * @param AuthenticationException $exception
     *
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            response()->json(['message' => $exception->getMessage()], 401);
        }

        $middlewares = request()->route()->gatherMiddleware();
        $guard = config('auth.defaults.guard');
        foreach($middlewares as $middleware) {
            if(preg_match("/auth:/", $middleware)) {
                list($mid, $guard) = explode(":", $middleware);
                break;
            }
        }

        switch($guard) {
            case 'investor':
                $login = '/login';
                break;
            default:
                $login = '/admin/login';
                break;
        }

        return redirect()->guest($login);
    }
}
