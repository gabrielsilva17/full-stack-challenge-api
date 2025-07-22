<?php

namespace App\Traits;

use App\Constants\Messages;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Trait ResponseTrait
 *
 * Padroniza respostas JSON da API.
 */
trait ResponseTrait
{
    /**
     * Envia resposta JSON de sucesso ou paginação.
     *
     * @param mixed  $result  Dados ou LengthAwarePaginator
     * @param string $message Mensagem de retorno
     * @param int    $status  Código HTTP
     * @return JsonResponse
     */
    public function sendResponse(mixed $result, string $message = '', int $status = Response::HTTP_OK): JsonResponse
    {
        // Erro via Exception
        if ($result instanceof Exception) {
            return $this->sendError(
                $result->getMessage(),
                $result->getCode(),
                $status >= 400 && $status < 600 ? $status : Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        // Paginação
        if ($result instanceof LengthAwarePaginator) {
            return response()->json(
                $this->formatPaginator($result, $message),
                $status
            );
        }

        // Resposta simples
        return response()->json([
            'success' => true,
            'message' => $message ?: Messages::MSG007,
            'data'    => $result,
        ], $status);
    }

    /**
     * Envia resposta JSON de erro.
     *
     * @param string     $error   Mensagem de erro
     * @param int|string $code    Código de erro customizado
     * @param int        $status  Código HTTP
     * @return JsonResponse
     */
    public function sendError(string $error = '', int|string $code = '', int $status = Response::HTTP_BAD_REQUEST): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $error ?: Messages::MSG099,
            'code'    => $code,
        ], $status);
    }

    /**
     * Formata dados de paginação.
     *
     * @param LengthAwarePaginator $paginator
     * @param string               $message
     * @return array<string, mixed>
     */
    protected function formatPaginator(LengthAwarePaginator $paginator, string $message): array
    {
        return [
            'success' => true,
            'message' => $message ?: Messages::MSG007,
            'data'    => $paginator->items(),
            'meta'    => [
                'current_page' => $paginator->currentPage(),
                'last_page'    => $paginator->lastPage(),
                'per_page'     => $paginator->perPage(),
                'total'        => $paginator->total(),
            ],
        ];
    }
}
