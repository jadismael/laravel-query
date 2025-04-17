<?php

// tests/TestCaseWithDatabase.php

namespace Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;

abstract class TestCaseWithDatabase extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();
    }

    protected function setupDatabase(): void
    {
        Schema::connection('sqlite')->create('authors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
        });

        Schema::connection('sqlite')->create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('author_id')->constrained('authors');
            $table->string('title');
        });

        Schema::connection('sqlite')->create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained('posts');
            $table->string('body');
        });
    }
}
