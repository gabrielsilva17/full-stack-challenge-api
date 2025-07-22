<?php

namespace App\Services;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class BaseService
{
    protected object $repo;

    /**
     * @param  mixed  $repo
     */
    public function __construct(object $repo)
    {
        $this->repo = $repo;
    }

    /**
     * Cria ou atualiza um registro.
     *
     * @param  array              $data
     * @param  int|string|null    $id
     * @return mixed
     */
    public function save(array $data, int|string|null $id = null)
    {
        if ($id === null) {
            $entity = $this->repo->create($data);
            return $entity;
        }

        $entity = $this->repo->find($id);
        if (! $entity) {
            throw new ModelNotFoundException("Registro não encontrado: {$id}");
        }

        $this->repo->update($id, $data);

        return $entity->refresh();
    }

    /**
     * Recupera um registro pelo ID.
     *
     * @param  int|string  $id
     * @return mixed|null
     */
    public function find(int|string $id)
    {
        return $this->repo->find($id);
    }

    /**
     * Deleta um registro.
     *
     * @param  int|string  $id
     * @return bool
     *
     * @throws ModelNotFoundException
     */
    public function delete(int|string $id): bool
    {
        $entity = $this->repo->find($id);
        if (! $entity) {
            throw new ModelNotFoundException("Registro não encontrado: {$id}");
        }

        return $this->repo->delete($id);
    }

    /**
     * Paginates with optional filters, operators and sorts.
     *
     * @param  array  $params   [
     *     'filters'   => [...],
     *     'operators' => [...],
     *     'sorts'     => [...],
     * @param  int    $perPage
     * @return LengthAwarePaginator
     */
    public function paginate(array $params = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repo->paginate(
            $params['filters']   ?? [],
            $params['operators'] ?? [],
            $params['sorts']     ?? [],
            $perPage
        );
    }
}
