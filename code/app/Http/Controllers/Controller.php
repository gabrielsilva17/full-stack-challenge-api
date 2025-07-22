<?php

namespace App\Http\Controllers;

use App\Constants\Messages;
use App\Services\BaseService;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use ResponseTrait;

    protected BaseService $service;

    public function __construct(BaseService $service)
    {
        $this->service = $service;
    }

    /**
     * Lista recursos paginados.
     */
    public function index(Request $request): JsonResponse
    {
        $query   = $request->query();
        $perPage = Arr::pull($query, 'per_page', 15);
        Arr::forget($query, 'page');

        $params = [
            'filters'   => [],
            'operators' => [],
            'sorts'     => [],
        ];

        foreach ($query as $key => $value) {
            if ($value === '' || $value === null) {
                continue;
            }
            if (str_contains($key, '__')) {
                [$field, $op] = explode('__', $key, 2);
                $params['operators'][$field] = [$op, $value];
                continue;
            }
            if ($key === 'sort') {
                $params['sorts'] = Arr::wrap($value);
                continue;
            }
            $params['filters'][$key] = $value;
        }

        $data = $this->service->paginate($params, (int) $perPage);

        return $this->sendResponse($data, Messages::MSG007, 200);
    }

    /**
     * Exibe um recurso específico.
     */
    public function show(int $id): JsonResponse
    {
        $model = $this->service->find($id);
        if (! $model) {
            return $this->sendError(Messages::MSG002 ?? 'Registro não encontrado.', 404);
        }
        return $this->sendResponse($model, Messages::MSG007, 200);
    }
}
