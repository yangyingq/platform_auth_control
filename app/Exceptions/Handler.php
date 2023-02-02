<?php

namespace App\Exceptions;

use App\Helpers\Tools;
use Exception;
use App\Exceptions\RequestException;
use App\Exceptions\ValidationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        RequestException::class,
        HttpException::class,
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
        $this->handle($request, $exception);
    }

    /**
     * 统一异常处理
     * @param $request
     * @param Exception $exception
     * @return \Illuminate\Http\JsonResponse
     * @throws Exception
     */
    public function handle($request, Exception $exception)
    {
        if ($exception instanceof ValidationException) {
            // 接口传参缺少
            return response()->json(Tools::error($exception->getMessage()));
        } else if ($exception instanceof RequestException) {
            // 接口传参缺少
            return response()->json(Tools::error($exception->getMessage(), $exception->getResultCode()));
        } else {

            $requestUrl = $request->url();
            $endArr = explode('.', $requestUrl);
            $fileEnd = $endArr[(count($endArr) ?? 0 )-1];

            if(!$exception instanceof NotFoundHttpException){
                // error log
                Tools::logUnusualError($exception);
            }
            if( 'ts' == $fileEnd) {
                throw($exception);
            }
            if(!in_array($fileEnd, ['png', 'jpg', 'jpeg', 'css', 'js', 'gif', 'ico'])) {
                // 接口报错
                return response()->json(Tools::error('系统开小差了'));
            }
        }
    }
}
