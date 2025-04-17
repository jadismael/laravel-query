<?php

namespace Tests\Unit\Services;


use Illuminate\Database\Eloquent\Builder;
use Jadismael\LaravelQuery\Services\Query\QuerySort;
use Mockery;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class QuerySortTest extends TestCase
{
    protected array $modelColumns = ['name', 'created_at', 'priority'];

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[DataProvider('validSortsProvider')]
    public function test_applies_valid_sorts(array $sorts, array $expectedCalls)
    {
        $query = Mockery::mock(Builder::class);

        // Expect orderBy for each expected call
        foreach ($expectedCalls as [$field, $direction]) {
            $query->shouldReceive('orderBy')
                ->with($field, $direction)
                ->once()
                ->andReturnSelf();
        }

        $sorter = new QuerySort($this->modelColumns);
        $result = $sorter->apply($query, $sorts);

        $this->assertInstanceOf(Builder::class, $result);
    }

    public static function validSortsProvider(): array
    {
        return [
            'single valid sort' => [
                ['name' => 'asc'],
                [['name', 'asc']]
            ],

            'single valid capital sort' => [
                ['name' => 'ASC'],
                [['name', 'asc']]
            ],
            'multiple valid sorts' => [
                ['priority' => 'desc', 'created_at' => 'asc'],
                [['priority', 'desc'], ['created_at', 'asc']]
            ],
        ];
    }

    #[DataProvider('invalidSortsProvider')]
    public function test_ignores_invalid_columns(array $sorts)
    {
        $query = Mockery::mock(Builder::class);
        // No calls expected since the field isn't valid
        $query->shouldNotReceive('orderBy');
        $this->expectException(\InvalidArgumentException::class);
        $sorter = new QuerySort(['name', 'created_at']);
        $result = $sorter->apply($query, $sorts);



    }

    public static function invalidSortsProvider(): array
    {
        return [
            'invalid column name' => [
                ['invalid_field' => 'asc'],
            ],

            'invalid direction sort' => [
                ['name' => 'HEUL'],
            ],
            'multiple invalid sorts' => [
                ['invalid_field' => 'HEUL', 'created_at' => 'asc'],
            
            ],
        ];
    }
}
