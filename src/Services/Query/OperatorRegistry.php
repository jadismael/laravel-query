<?php
namespace Jadismael\LaravelQuery\Services\Query;

class OperatorRegistry
{
    protected array $map =  [
        'equals'    => ['method' => 'where',      'symbol' => '='],
        'gt'        => ['method' => 'where',      'symbol' => '>'],
        'lt'        => ['method' => 'where',      'symbol' => '<'],
        'gte'       => ['method' => 'where',      'symbol' => '>='],
        'lte'       => ['method' => 'where',      'symbol' => '<='],
        'like'      => ['method' => 'where',      'symbol' => 'like'],
        'in'        => ['method' => 'whereIn'],
        'null'      => ['method' => 'whereNull'],
        'notnull'   => ['method' => 'whereNotNull'],
        'between'   => ['method' => 'whereBetween'],
        'notin' => ['method' => 'whereNotIn'],
        'notbetween' => ['method' => 'whereNotBetween'],
    ];

    public function register(string $key, array $definition): void
    {
        $this->map[$key] = $definition;
    }

    public function all(): array
    {
        return $this->map;
    }
}
