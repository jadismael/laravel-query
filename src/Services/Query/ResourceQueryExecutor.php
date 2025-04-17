<?php

namespace Jadismael\LaravelQuery\Services\Query;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

class ResourceQueryExecutor
{
    public function get(Builder $query): Collection
    {
        return $query->get();
    }

    public function paginate(Builder $query, int $perPage = 15, ?int $page = null)
    {
        if (null !== $page) {
            Paginator::currentPageResolver(fn () => $page);
        }

        return $query->paginate($perPage);
    }

    public function getRawSql(Builder $query): string
    {
        return vsprintf(
            str_replace('?', "'%s'", $query->toSql()),
            $query->getBindings(),
        );
    }
}
