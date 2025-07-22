<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class BaseModel
 *
 * Modelo base para todas as entidades, provendo:
 * - Soft deletes (deleted_at)
 * - Escopos reutilizáveis para filtros e ordenação dinâmicos
 */
abstract class BaseModel extends Model
{
    use SoftDeletes;

    /**
     * Atributos protegidos contra mass assignment.
     * Cada model filha deve definir seus próprios atributos fillable ou guarded.
     *
     * @var array<int,string>
     */
    protected $guarded = [];

    /**
     * Escopo: aplica filtros dinâmicos com operadores customizáveis.
     *
     * @param  Builder               $query
     * @param  array<string,mixed>   $filters
     * @param  array<string,string>  $operators
     * @return Builder
     */
    public function scopeFilter(Builder $query, array $filters, array $operators = []): Builder
    {
        foreach ($filters as $column => $value) {
            if ($value === null || ($value === '' && $value !== '0')) {
                continue;
            }

            $operator = $operators[$column] ?? '=';

            if (strtolower($operator) === 'like') {
                $query->where($column, 'like', "%{$value}%");
            } else {
                $query->where($column, $operator, $value);
            }
        }

        return $query;
    }

    /**
     * Escopo: filtro simplificado para um único campo.
     *
     * @param  Builder     $query
     * @param  string      $column
     * @param  mixed       $value
     * @param  string      $operator
     * @return Builder
     */
    public function scopeFilterBy(Builder $query, string $column, mixed $value, string $operator = '='): Builder
    {
        if ($value === null || ($value === '' && $value !== '0')) {
            return $query;
        }

        return $query->filter([
            $column => $value
        ], [
            $column => $operator
        ]);
    }

    /**
     * Escopo: aplica ordenação dinâmica.
     *
     * @param  Builder              $query
     * @param  array<string,string> $sorts
     * @return Builder
     */
    public function scopeSort(Builder $query, array $sorts): Builder
    {
        foreach ($sorts as $column => $direction) {
            $dir = strtolower($direction);
            if (in_array($dir, ['asc', 'desc'], true)) {
                $query->orderBy($column, $dir);
            }
        }

        return $query;
    }

    /**
     * Escopo: ordena pelos registros mais recentes primeiro (created_at).
     *
     * @param  Builder $query
     * @return Builder
     */
    public function scopeLatest(Builder $query): Builder
    {
        return $query->orderBy($this->getCreatedAtColumn(), 'desc');
    }

    /**
     * Escopo: exclui um registro específico pelo ID.
     *
     * @param  Builder          $query
     * @param  int|string       $id
     * @return Builder
     */
    public function scopeExcept(Builder $query, $id): Builder
    {
        return $query->where($this->getKeyName(), '!=', $id);
    }
}
