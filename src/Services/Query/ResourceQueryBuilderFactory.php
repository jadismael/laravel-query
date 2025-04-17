<?php
namespace Jadismael\LaravelQuery\Services\Query;

use Illuminate\Database\Eloquent\Model;

class ResourceQueryBuilderFactory
{
    public function make(string $resourceClass): ResourceQueryBuilder
    {
        // Check if the resource class is a valid Eloquent model
        if (!class_exists($resourceClass)) {
            throw new \InvalidArgumentException("Resource class {$resourceClass} does not exist.");
        }

        // Check if the resource class is an Eloquent model
        if (!is_subclass_of($resourceClass, Model::class)) {
            throw new \InvalidArgumentException("{$resourceClass} is not a valid Eloquent model.");
        }

        // Create an instance of the model
        $model = app($resourceClass);

        if (! $model instanceof Model) {
            throw new \InvalidArgumentException("{$resourceClass} is not a valid Eloquent model.");
        }

        return app(ResourceQueryBuilder::class, ['model' => $model]);
    }
}
