<?php

namespace Jadismael\LaravelQuery\Services;

use Illuminate\Database\Eloquent\Model;
use Jadismael\LaravelQuery\Services\Query\ResourceQueryBuilderFactory;
use Jadismael\LaravelQuery\Services\Query\ResourceQueryExecutor;

class ResourceQueryService
{
    protected Model $model;

    public function __construct(
        protected ResourceQueryBuilderFactory $builderFactory,
        protected ResourceQueryExecutor $queryExecutor,
    ) {}

    public function fetch(
        string $resourceClass,
        array $filters = [],
        array $sorts = [],
        array $includes = [],
        array $fields = [],
        ?int $paginate = null,
        ?int $page = null,
    ) {
        $builder = $this->builderFactory->make($resourceClass);

        $query = $builder->build(
            filters: $filters,
            sorts: $sorts,
            includes: $includes,
            fields: $fields,
        );

        return $paginate
            ? $this->queryExecutor->paginate($query, $paginate, $page)
            : $this->queryExecutor->get($query);
    }
}
