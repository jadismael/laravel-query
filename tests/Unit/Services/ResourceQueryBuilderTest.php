<?php

namespace Tests\Unit\Services;

use Jadismael\LaravelQuery\Services\ModelInspector;
use Jadismael\LaravelQuery\Services\Query\QueryFilters;
use Jadismael\LaravelQuery\Services\Query\ResourceQueryBuilder;
use Jadismael\LaravelQuery\Services\Query\ResourceQueryExecutor;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\DataProviders\ResourceQueryFilterDataProvider;
use Tests\Models\Post;
use Tests\TestCase;

class ResourceQueryBuilderTest extends TestCase
{
    protected ResourceQueryBuilder $builder;

    protected ResourceQueryExecutor $executor;

    protected function setUp(): void
    {
        parent::setUp();

        $model = app(Post::class);
        $queryFilter = app(QueryFilters::class);

        // Mock the model inspector
        $mockInspector = \Mockery::mock(ModelInspector::class);
        $mockInspector
            ->shouldReceive('getColumns')
            ->with($model)
            ->andReturn(['name', 'email', 'status', 'score', 'priority', 'archived', 'created_at'])
        ;

        $mockInspector
            ->shouldReceive('getDateColumns')
            ->with($model)
            ->andReturn(['created_at'])
        ;

        // Build ResourceQueryBuilder with mocks
        $this->builder = new ResourceQueryBuilder($model, $queryFilter, $mockInspector);

        // Use the real executor to test final query SQL generation
        $this->executor = app(ResourceQueryExecutor::class);
    }

    protected function tearDown(): void
    {
        \Mockery::close();
        parent::tearDown();
    }

    #[DataProvider('queryDataProvider')]
    public function testItBuildsExpectedQuery(
        array $filters,
        array $sorts,
        array $includes,
        array $fields,
        string $expectedSql,
    ): void {
        $query = $this->builder->build(
            filters: $filters,
            sorts: $sorts,
            includes: $includes,
            fields: $fields,
        );

        $actualSql = $this->executor->getRawSql($query);

        $normalize = fn ($sql) => str_replace(['"', '`', "'"], '', $sql);

        $this->assertEquals(
            $normalize($expectedSql),
            $normalize($actualSql),
            'SQL queries are not equal after normalization.',
        );
    }

    public static function queryDataProvider(): array
    {
        return ResourceQueryFilterDataProvider::all();
    }
}
