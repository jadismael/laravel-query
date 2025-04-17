<?php
namespace Jadismael\LaravelQuery\Services\Query;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Jadismael\LaravelQuery\Services\ModelInspector;

class ResourceQueryBuilder
{


    protected array $dateColumns = [];
    protected array $modelColumns = [];
    protected QuerySort $querySorter;
    protected QueryInclude $queryInclude;

    public function __construct(protected Model $model,protected QueryFilters $queryFilters, protected ModelInspector $modelInspector) {

        $this->modelColumns = $modelInspector->getColumns($model);     
        $this->dateColumns = $modelInspector->getDateColumns($model);   
        $this->querySorter = new QuerySort($this->modelColumns);
        $this->queryInclude = new QueryInclude($model);

    }



    public function build(array $filters = [], array $sorts = [], array $includes = [], array $fields = []): Builder
    {
        $query = $this->model->newQuery();
        $query = $this->queryFilters->apply($query, $filters, $this->modelColumns, $this->dateColumns);
        $query = $this->querySorter->apply($query, $sorts);
        $query = $this->queryInclude->apply ($query, $includes);
        $query = $this->applyFields($query, $fields);

        return $query;
    }

    protected function applyFields(Builder $query, array $fields): Builder
    {
        if (!empty($fields)) {
            $query->select($fields);
        }
        return $query;
    }
}
