<?php
namespace Jadismael\LaravelQuery\Services\Query;

use Illuminate\Database\Eloquent\Builder;
use InvalidArgumentException;

class QuerySort
{
    protected array $validDirections = ['asc', 'desc'];

    public function __construct(protected array $modelColumns) { }
    public function apply(Builder $query, array $sorts): Builder
    {
        foreach ($sorts as $field => $direction) {


             // Normalize direction (e.g. ASC → asc)
             $direction = strtolower($direction);

             if (!in_array($direction, $this->validDirections)) {
                 throw new InvalidArgumentException("Invalid sort direction: {$direction}");
             }

            if (!in_array($field, $this->modelColumns)) {
                throw new InvalidArgumentException("Invalid field: {$field}");
            
            }
            $query->orderBy($field, $direction);
        }
        return $query;
    }


    

}
