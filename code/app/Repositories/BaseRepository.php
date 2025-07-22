<?php
namespace App\Repositories;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;

abstract class BaseRepository
{
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    protected function applyFilters(Builder $query, array $filters): void
    {
        $dateFields = $this->model->getDates();
        foreach (Arr::except($filters, ['page','per_page','sort']) as $field => $value) {
            if ($value === '' || $value === null) continue;
            if (in_array($field, $dateFields, true)) {
                $query->whereDate($field, $value);
            } elseif (is_numeric($value)) {
                $query->where($field, $value);
            } else {
                $query->whereRaw("LOWER(`{$field}`) LIKE ?", ['%'.mb_strtolower($value).'%']);
            }
        }
    }

    public function paginate(
        array $filters = [],
        array $operators = [],
        array $sorts = [],
        int   $perPage = 15,
        ?Closure $scope = null
    ): LengthAwarePaginator {
        $query = $this->model->newQuery();
        if ($scope) $scope($query);
        if (in_array(SoftDeletes::class, class_uses($this->model), true)) {
            $query->whereNull($this->model->getDeletedAtColumn());
        }
        $this->applyFilters($query, $filters);
        foreach ($operators as $f => [$op,$v]) {
            $query->where($f, $op, $v);
        }
        foreach ($sorts as $s) {
            [$c,$d] = explode(',', $s, 2);
            $query->orderBy($c, $d);
        }
        return $query->paginate($perPage);
    }

    public function all(array $filters = [], array $with = [], array $sorts = [])
    {
        $query = $this->model->newQuery()->with($with);
        $this->applyFilters($query, $filters);
        foreach ($sorts as $s) {
            [$c,$d] = explode(',', $s, 2);
            $query->orderBy($c, $d);
        }
        return $query->get();
    }

    public function find(int|string $id): ?Model
    {
        return $this->model->find($id);
    }

    public function findOrFail(int|string $id): Model
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    public function update(int|string $id, array $data): ?Model
    {
        $model = $this->find($id);
        if (! $model) {
            return null;
        }

        $current = $model->getAttributes();
        $filtered = Arr::where($data, fn($v) => ! is_null($v));
        $merged = array_merge($current, $filtered);
        $model->fill($merged)->save();

        return $model;
    }

    public function delete(int|string $id): bool
    {
        $m = $this->find($id);
        return $m ? $m->delete() : false;
    }

    public function firstOrCreate(array $attrs, array $vals = []): Model
    {
        return $this->model->firstOrCreate($attrs, $vals);
    }

    public function updateOrCreate(array $attrs, array $vals = []): Model
    {
        return $this->model->updateOrCreate($attrs, $vals);
    }

    public function exists(array $filters = []): bool
    {
        $q = $this->model->newQuery();
        $this->applyFilters($q, $filters);
        return $q->exists();
    }

    public function count(array $filters = []): int
    {
        $q = $this->model->newQuery();
        $this->applyFilters($q, $filters);
        return $q->count();
    }
}
