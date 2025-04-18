<?php

namespace Tests\Unit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Jadismael\LaravelQuery\Services\Query\QueryInclude;
use Tests\Models\Author;
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

    public function testIncludesValidRelations(): void
    {
        $query = $this->includeService($this->model)->apply($this->model->newQuery(), ['posts']);

        $this->assertEquals(['posts'], array_keys($query->getEagerLoads()));
    }

    public function testIgnoresInvalidRelationsByDefault(): void
    {
        $query = $this->includeService($this->model)->apply($this->model->newQuery(), ['nonexistent']);

        $this->assertEquals([], $query->getEagerLoads());
    }

    public function testThrowsOnInvalidRelationInStrictMode(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->includeService($this->model, strict: true)->apply($this->model->newQuery(), ['invalidRelation']);
    }

    public function testValidatesNestedRelations(): void
    {
        $this->assertTrue($this->includeService($this->model)->isValidNestedRelation($this->model, 'posts.comments'));
    }

    public function testRejectsInvalidNestedRelations(): void
    {
        $service = $this->includeService($this->model);

        $this->assertFalse($service->isValidNestedRelation($this->model, 'posts.nonexistent'));
        $this->assertFalse($service->isValidNestedRelation($this->model, 'nonexistent.comments'));
    }

    protected function includeService(Model $model, bool $strict = false): QueryInclude
    {
        return new QueryInclude($model, $strict);
    }
}
