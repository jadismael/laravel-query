<?php

namespace Jadismael\LaravelQuery\Facades;

use Illuminate\Support\Facades\Facade;

class LaravelQuery extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Jadismael\LaravelQuery\Services\ResourceQueryService::class;
    }
}
