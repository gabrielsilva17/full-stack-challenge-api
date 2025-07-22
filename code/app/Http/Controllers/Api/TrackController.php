<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TrackRequest;
use Illuminate\Http\Request;
use App\Services\TrackService;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

class TrackController extends Controller
{
    public function __construct(TrackService $service)
    {
        parent::__construct($service);
    }

    /**
     * @OA\Post(
     *   path="/api/tracks/import",
     *   operationId="importTracks",
     *   tags={"Tracks"},
     *   summary="Importar faixas pelo código ISRC e salvar no banco",
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       required={"isrcs"},
     *       @OA\Property(property="isrcs", type="array", @OA\Items(type="string"))
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Faixas importadas com sucesso",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="status",  type="boolean", example=true),
     *       @OA\Property(property="message", type="string",  example="Importação concluída."),
     *       @OA\Property(
     *         property="data",
     *         type="object",
     *         @OA\Property(property="results",  type="object", @OA\AdditionalProperties(ref="#/components/schemas/Track")),
     *         @OA\Property(property="messages", type="array", @OA\Items(type="string"))
     *       )
     *     )
     *   )
     * )
     */
    public function import(TrackRequest $request): JsonResponse
    {
        $payload = $this->service->importTracks($request->validated()['isrcs']);

        return response()->json([
            'status'  => true,
            'message' => 'Importação concluída.',
            'data'    => $payload,
        ], 200);
    }

    /**
     * @OA\Get(
     *   path="/api/tracks",
     *   operationId="listTracks",
     *   tags={"Tracks"},
     *   summary="Listar faixas com paginação e filtros de busca",
     *
     *   @OA\Parameter(
     *     name="isrc", in="query",
     *     @OA\Schema(type="string"),
     *     description="Filtrar pelo código ISRC (igual)"
     *   ),
     *   @OA\Parameter(
     *     name="title", in="query",
     *     @OA\Schema(type="string"),
     *     description="Filtrar por título (LIKE)"
     *   ),
     *   @OA\Parameter(
     *     name="artist", in="query",
     *     @OA\Schema(type="string"),
     *     description="Filtrar por artista (LIKE)"
     *   ),
     *   @OA\Parameter(
     *     name="available_in_br", in="query",
     *     @OA\Schema(type="boolean"),
     *     description="Filtrar se está disponível no Brasil"
     *   ),
     *   @OA\Parameter(
     *     name="release_date_from", in="query",
     *     @OA\Schema(type="string", format="date"),
     *     description="Data de lançamento mínima (YYYY-MM-DD)"
     *   ),
     *   @OA\Parameter(
     *     name="release_date_to", in="query",
     *     @OA\Schema(type="string", format="date"),
     *     description="Data de lançamento máxima (YYYY-MM-DD)"
     *   ),
     *   @OA\Parameter(
     *     name="page", in="query",
     *     @OA\Schema(type="integer", default=1),
     *     description="Número da página"
     *   ),
     *   @OA\Parameter(
     *     name="per_page", in="query",
     *     @OA\Schema(type="integer", default=15),
     *     description="Quantidade de itens por página"
     *   ),
     *
     *   @OA\Response(
     *     response=200,
     *     description="Lista de faixas retornada com sucesso",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="status",  type="boolean", example=true),
     *       @OA\Property(property="message", type="string",  example="Operação bem‑sucedida."),
     *       @OA\Property(
     *         property="data",
     *         type="object",
     *         @OA\Property(
     *           property="data",
     *           type="array",
     *           @OA\Items(ref="#/components/schemas/Track")
     *         ),
     *         @OA\Property(
     *           property="meta",
     *           type="object",
     *           @OA\Property(property="current_page", type="integer", example=1),
     *           @OA\Property(property="per_page",     type="integer", example=15),
     *           @OA\Property(property="last_page",    type="integer", example=1),
     *           @OA\Property(property="total",        type="integer", example=42)
     *         )
     *       )
     *     )
     *   )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $perPage   = (int) $request->get('per_page', 15);
        $paginated = $this->service->paginate([], $perPage);

        return response()->json([
            'status'  => true,
            'message' => 'Operação bem‑sucedida.',
            'data'    => [
                'data' => $paginated->items(),
                'meta' => [
                    'current_page' => $paginated->currentPage(),
                    'per_page'     => $paginated->perPage(),
                    'last_page'    => $paginated->lastPage(),
                    'total'        => $paginated->total(),
                ],
            ],
        ], 200);
    }

    /**
     * @OA\Get(
     *   path="/api/tracks/{id}",
     *   operationId="getTrack",
     *   tags={"Tracks"},
     *   summary="Obter detalhes de uma faixa pelo ID",
     *
     *   @OA\Parameter(
     *     name="id", in="path",
     *     description="ID numérico da faixa",
     *     required=true,
     *     @OA\Schema(type="integer", example=3)
     *   ),
     *
     *   @OA\Response(
     *     response=200,
     *     description="Detalhes da faixa retornados com sucesso",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="status",  type="boolean", example=true),
     *       @OA\Property(property="message", type="string",  example="Operação bem‑sucedida."),
     *       @OA\Property(
     *         property="data",
     *         ref="#/components/schemas/Track"
     *       )
     *     )
     *   ),
     *
     *   @OA\Response(
     *     response=404,
     *     description="Faixa não encontrada",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="status",  type="boolean", example=false),
     *       @OA\Property(property="message", type="string",  example="Registro não encontrado.")
     *     )
     *   )
     * )
     */
    public function show(int $id): JsonResponse
    {
        $model = $this->service->find($id);

        if (! $model) {
            return response()->json([
                'status'  => false,
                'message' => 'Registro não encontrado.',
            ], 404);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Operação bem‑sucedida.',
            'data'    => $model,
        ], 200);
    }
}
