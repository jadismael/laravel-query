<?php

namespace Jadismael\LaravelQuery\Services\Query;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

class QueryInclude
{
    public function __construct(
        protected Model $model,
        protected bool $strict = false, // 👈 Optional toggle
    ) {}

    public function apply(Builder $query, array $includes): Builder
    {
        $validIncludes = [];

        foreach ($includes as $include) {
            if ($this->isValidNestedRelation($this->model, $include)) {
                $validIncludes[] = $include;
            } elseif ($this->strict) {
                throw new \InvalidArgumentException("Invalid include path: '{$include}'");
            }
        }

        return $query->with($validIncludes);
    }

    public function isValidNestedRelation(Model $model, string $path): bool
    {
        $parts = explode('.', $path);

        return $this->validateNestedRelation($parts, $model);
    }

    protected function validateNestedRelation(array $relationParts, Model $baseModel): bool
    {
        $currentModel = $baseModel;

        foreach ($relationParts as $relation) {
            if (! method_exists($currentModel, $relation)) {
                return false;
            }

            $reflection = new \ReflectionMethod($currentModel, $relation);

            if ($reflection->getNumberOfParameters() > 0) {
                return false;
            }

            try {
                $relationInstance = $reflection->invoke($currentModel);
            } catch (\Throwable) {
                return false;
            }

            if (! $relationInstance instanceof Relation) {
                return false;
            }

            $currentModel = $relationInstance->getRelated();
        }

        return true;
    }
}
