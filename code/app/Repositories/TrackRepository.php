<?php

namespace App\Repositories;

use App\Models\Track;
use Illuminate\Pagination\LengthAwarePaginator;

class TrackRepository
{
    public function create(array $data): Track
    {
        return Track::create($data);
    }

    public function find($id): ?Track
    {
        return Track::find($id);
    }

    public function findByIsrc(string $isrc): ?Track
    {
        return Track::where('isrc', $isrc)->first();
    }

    public function update($id, array $data): bool
    {
        return (bool) Track::where('id', $id)->update($data);
    }

    public function delete($id): bool
    {
        $t = Track::findOrFail($id);
        return $t->delete();
    }

    public function paginate(array $filters = [], array $operators = [], array $sorts = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Track::query();

        if ($filters) {
            $ops = [];
            foreach ($operators as $col => [$op, $val]) {
                $ops[$col] = $op;
            }
            $query = $query->filter($filters, $ops);
        }

        if ($sorts) {
            $query = $query->sort($sorts);
        }

        return $query->paginate($perPage);
    }
}
