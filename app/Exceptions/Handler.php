<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Throwable;

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
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->renderable(function (Throwable $e, Request $request) {
            if ($e instanceof ThrottleException) {
                return response()->make(
                    $e->getMessage() ?? 'Du calme moussaillon. Tu postes beaucoup, prends une petite pause.',
                    HttpResponse::HTTP_TOO_MANY_REQUESTS
                );
            }

            if ($e instanceof UnsubscribedException) {
                return $this->unsubscribed($request, $e);
            }
        });
    }

    /**
     * Convert an authentication exception into a response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Exceptions\UnsubscribedException  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function unsubscribed(Request $request, UnsubscribedException $exception)
    {
        return $request->expectsJson()
            ? response()->json(['message' => $exception->getMessage()], HttpResponse::HTTP_PAYMENT_REQUIRED)
            : redirect()->to($exception->redirectTo() ?? route('subscription.edit'));
    }
}
