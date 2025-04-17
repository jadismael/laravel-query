<?php

namespace Jadismael\LaravelQuery;

use Illuminate\Support\ServiceProvider;
use Jadismael\LaravelQuery\Services\ModelInspector;
use Jadismael\LaravelQuery\Services\Query\FilterParser;
use Jadismael\LaravelQuery\Services\Query\OperatorRegistry;
use Jadismael\LaravelQuery\Services\Query\QueryFilters;
use Jadismael\LaravelQuery\Services\Query\QueryInclude;
use Jadismael\LaravelQuery\Services\Query\QuerySort;
use Jadismael\LaravelQuery\Services\Query\ResourceQueryBuilder;
use Jadismael\LaravelQuery\Services\Query\ResourceQueryBuilderFactory;
use Jadismael\LaravelQuery\Services\Query\ResourceQueryExecutor;
use Jadismael\LaravelQuery\Services\ResourceQueryService;

class LaravelQueryServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     */
    public function register(): void
    {
        // Singleton bindings
        $this->app->singleton(ModelInspector::class, fn () => new ModelInspector());
        $this->app->singleton(OperatorRegistry::class, fn () => new OperatorRegistry());
        $this->app->singleton(ResourceQueryExecutor::class, fn () => new ResourceQueryExecutor());

        // Non-singleton bindings (new instance per request/use)
        $this->app->bind(FilterParser::class, fn () => new FilterParser());

        // Bind complex services with dependencies
        $this->app->bind(QueryFilters::class, function ($app) {
            return new QueryFilters($app->make(OperatorRegistry::class));
        });

        $this->app->bind(QuerySort::class, function ($app, $params) {
            return new QuerySort($params['modelColumns']);
        });

        $this->app->bind(QueryInclude::class, function ($app, $params) {
            return new QueryInclude($params['model'], $params['strict'] ?? false);
        });

        $this->app->bind(ResourceQueryBuilder::class, function ($app, $params) {
            return new ResourceQueryBuilder(
                $params['model'],
                $app->make(QueryFilters::class),
                $app->make(ModelInspector::class),
                $app->make(QuerySort::class, [
                    'modelColumns' => $params['modelColumns'],
                ]),
                $app->make(QueryInclude::class, [
                    'model' => $params['model'],
                ]),
            );
        });

        $this->app->bind(ResourceQueryBuilderFactory::class, function ($app) {
            return new ResourceQueryBuilderFactory();
        });

        $this->app->bind(ResourceQueryService::class, function ($app) {
            return new ResourceQueryService($app->make(ResourceQueryBuilderFactory::class, []), $app->make(ResourceQueryExecutor::class));
        });

        // Merge default configuration (optional)
        $this->mergeConfigFrom(__DIR__ . '/config/laravel-query.php', 'laravel-query');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/config/laravel-query.php' => config_path('laravel-query.php'),
        ], 'laravel-query-config');
    }
}
