<?php

namespace Tests\Unit;

use Illuminate\Database\Eloquent\Builder;
use Jadismael\LaravelQuery\Services\Query\OperatorRegistry;
use Jadismael\LaravelQuery\Services\Query\QueryFilters;
use Jadismael\LaravelQuery\Services\Query\ResourceQueryExecutor;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class QueryFiltersEdgeCasesTest extends TestCase
{
    protected QueryFilters $queryFilters;

    protected array $modelColumns = ['name', 'score', 'created_at'];

    protected array $dateColumns = ['created_at'];

    protected ResourceQueryExecutor $executor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->queryFilters = new QueryFilters(new OperatorRegistry());
        $this->executor = app(ResourceQueryExecutor::class);
    }

    protected function tearDown(): void
    {
        \Mockery::close();
        parent::tearDown();
    }

    #[DataProvider('invalidTypeFilters')]
    public function testItThrowsOnInvalidTypes(array $filter): void
    {
        $this->expectException(\BadMethodCallException::class);
        $this->runFilter($filter);
    }

    public static function invalidTypeFilters(): array
    {
        return [
            'gt operator with array' => [[
                'score:gt' => ['invalid'],
            ]],
            'equals with object' => [[
                'score:equals' => new \stdClass(),
            ]],
        ];
    }

    #[DataProvider('invalidColumnFilters')]
    public function testItThrowsOnInvalidColumns(array $filter): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->runFilter($filter);
    }

    public static function invalidColumnFilters(): array
    {
        return [
            'malicious column' => [[
                'DROP TABLE:equals' => 'x',
            ]],
            'nonsense column' => [[
                ';;;;;:equals' => 'x',
            ]],
        ];
    }

    private function runFilter(array $filter, ?Builder $query = null): Builder
    {
        $query = $query ?: \Mockery::mock(Builder::class);

        return $this->queryFilters->apply($query, $filter, $this->modelColumns, $this->dateColumns);
    }
}
