<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
        'current_password',
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
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $e
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $e)
    {
        // Se for uma requisição API (aceitando JSON) e o erro for ModelNotFoundException
        if ($request->wantsJson() && $e instanceof ModelNotFoundException) {
            $model = class_basename($e->getModel());
            $model = str_replace('Models\\', '', $model); // Remove o namespace Models se existir
            
            return response()->json([
                'message' => "O recurso solicitado não foi encontrado.",
                'errors' => [
                    'id' => ["O {$model} com o ID informado não foi encontrado."]
                ]
            ], 404);
        }

        // Se for uma requisição API (aceitando JSON) e o erro for 404
        if ($request->wantsJson() && $this->isHttpException($e) && $e->getStatusCode() === 404) {
            return response()->json([
                'message' => 'O recurso solicitado não foi encontrado.',
                'errors' => [
                    'url' => ['A URL solicitada não foi encontrada.']
                ]
            ], 404);
        }

        return parent::render($request, $e);
    }
}
