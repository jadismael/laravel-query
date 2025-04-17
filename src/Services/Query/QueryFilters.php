<?php
namespace Jadismael\LaravelQuery\Services\Query;

use Illuminate\Database\Eloquent\Builder;

class QueryFilters
{

    protected array $modelColumns = [];
    protected array $dateColumns = [];
    protected array $operatorMap = [];

    
    public function __construct(OperatorRegistry $registry
    
    ) {
        $this->operatorMap = $registry->all();

    }
    public function apply(Builder &$query, array $filters, array $modelColumns, array $dateColumns) : Builder
    {
        $parsedFilters = FilterParser::parseFilters($filters, $dateColumns);

        $allowedOperators = array_keys($this->operatorMap);

        $validatedParsedFilters = FilterParser::filterValidParsedFilters($allowedOperators,$parsedFilters,$modelColumns);


        foreach ($validatedParsedFilters as ['column' => $column, 'operator' => $operator, 'value' => $value ,'isDate' => $isDate]) {
         
            $this->applyFilter($query, $column, $operator, $value,$isDate);
        }
    
        return $query;
    }

    
    protected function getOperatorDefinition(string $operator): array
{
    return $this->operatorMap[$operator]
        ?? throw new \InvalidArgumentException("Unknown filter operator: {$operator}");
}

  protected function applyFilter(Builder $query, string $column, string $operator, mixed $value, bool $isDate): void
{
    $definition = $this->operatorMap[$operator] ?? throw new \InvalidArgumentException("Unknown filter operator: {$operator}");
    $method = $isDate && $definition['method'] === 'where' ? 'whereDate' : $definition['method'];

    if (in_array($method, ['whereNull', 'whereNotNull'])) {
        $this->applyNullFilter($query, $method, $column);
    } elseif (in_array($method, ['whereBetween', 'whereNotBetween', 'whereIn', 'whereNotIn'])) {
        $this->applyArrayFilter($query, $method, $column, $value);
    } elseif (isset($definition['symbol'])) {
        $this->applyComparisonFilter($query, $method, $column, $definition['symbol'], $value);
    } else {
        $query->{$method}($column, $value); // fallback
    }
}

    protected function applyNullFilter(Builder $query, string $method, string $column): void
{
    $query->{$method}($column);
}

protected function applyArrayFilter(Builder $query, string $method, string $column, array $value): void
{
    $query->{$method}($column, $value);
}

protected function applyComparisonFilter(Builder $query, string $method, string $column, string $operator, mixed $value): void
{
    if ($operator === 'like' && is_string($value)) {
        $value = $this->escapeLike($value);
    }

    $query->{$method}($column, $operator, $value);
}

    
protected function escapeLike(string $value): string
{
    return '%' . str_replace(['%', '_'], ['\\%', '\\_'], $value) . '%';
}

}
