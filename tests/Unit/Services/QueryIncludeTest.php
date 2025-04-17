<?php

namespace Tests\Unit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Jadismael\LaravelQuery\Services\Query\QueryInclude;
use Tests\Models\Author;
use Tests\TestCase;
use Tests\TestCaseWithDatabase;

class QueryIncludeTest extends TestCaseWithDatabase
{
    use RefreshDatabase;

    protected Model $model;

    protected function setUp(): void
    {
        parent::setUp();

        $this->model = new Author();
    }

    protected function includeService(Model $model, bool $strict = false): QueryInclude
    {
        return new QueryInclude($model, $strict);
    }

    public function test_includes_valid_relations(): void
    {
      
        $query = $this->includeService($this->model)->apply($this->model->newQuery(), ['posts']);

        $this->assertEquals(['posts'], array_keys($query->getEagerLoads()));
    }

    public function test_ignores_invalid_relations_by_default(): void
    {

        $query = $this->includeService($this->model)->apply($this->model->newQuery(), ['nonexistent']);

        $this->assertEquals([], $query->getEagerLoads());
    }

    public function test_throws_on_invalid_relation_in_strict_mode(): void
    {
  
        $this->expectException(InvalidArgumentException::class);

        $this->includeService($this->model, strict: true)->apply($this->model->newQuery(), ['invalidRelation']);
    }

    public function test_validates_nested_relations(): void
    {
        $this->assertTrue($this->includeService($this->model)->isValidNestedRelation($this->model, 'posts.comments'));
    }

    public function test_rejects_invalid_nested_relations(): void
    {
    
        $service = $this->includeService($this->model);

        $this->assertFalse($service->isValidNestedRelation($this->model, 'posts.nonexistent'));
        $this->assertFalse($service->isValidNestedRelation($this->model, 'nonexistent.comments'));
    }
}
