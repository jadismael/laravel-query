<?php


namespace Tests\DataProviders;

class ResourceQueryFilterDataProvider
{
    public static function all(): array
    {

      return [
        'basic filter and sort' => [
            ['name' => 'Hello World', 'status' => 'open'],
            ['name' => 'asc'],
            [],
            ['id', 'name'],
            "select `id`, `name` from `posts` where `name` = 'Hello World' and `status` = 'open' order by `name` asc"
        ],
        'single filter and descending sort' => [
            ['status' => 'archived'],
            ['created_at' => 'desc'],
            [],
            ['id', 'status'],
            "select `id`, `status` from `posts` where `status` = 'archived' order by `created_at` desc"
        ],
        
    'gt operator' => [
            ['priority:gt' => 5],
            [],
            [],
            ['*'],
            "select * from `posts` where `priority` > '5'"
        ],
        'lt operator' => [
            ['score:lt' => 100],
            [],
            [],
            ['*'],
            "select * from `posts` where `score` < '100'"
        ],
        'like operator' => [
            ['name:like' => '%World%'],
            [],
            [],
            ['*'],
"select * from `posts` where `name` like '%\\%World\\%%'"

        ],
        'null operator' => [
            ['archived:null' => true],
            [],
            [],
            ['*'],
            "select * from `posts` where `archived` is null"
        ],
        'notnull operator' => [
            ['archived:notnull' => true],
            [],
            [],
            ['*'],
            "select * from `posts` where `archived` is not null"
        ],
        'between operator' => [
            ['created_at:between' => ['2024-01-01', '2024-01-31']],
            [],
            [],
            ['*'],
            "select * from `posts` where `created_at` between '2024-01-01' and '2024-01-31'"
        ],
          'invalid operator is ignored' => [
    ['status:invalidop' => 'active'], // 🚫 Not in registry
    [],
    [],
    ['*'],
    "select * from `posts`" // ✅ Nothing applied
],  
'multiple valid filters combined' => [
    [
        'status:equals' => 'open',
        'priority:gt' => 3,
        'created_at:between' => ['2024-01-01', '2024-01-31']
    ],
    [],
    [],
    ['*'],
    "select * from `posts` where `status` = 'open' and `priority` > '3' and `created_at` between '2024-01-01' and '2024-01-31'"
],

'mixed valid and invalid filters' => [
    [
        'status:equals' => 'archived',
        'foo:invalidop' => 'something',
        'score:lt' => 100
    ],
    [],
    [],
    ['*'],
    "select * from `posts` where `status` = 'archived' and `score` < '100'"
],

'all filters invalid — returns base query' => [
    [
        'unknown:notreal' => 'x',
        'created_at:explode' => '💥'
    ],
    [],
    [],
    ['*'],
    "select * from `posts`"
],
'true string as value' => [
    ['score:equals' => 'true'],
    [],
    [],
    ['*'],
    "select * from `posts` where `score` = 'true'"
],

'escaped like filter' => [
    ['name:like' => '%hello_'],
    [],
    [],
    ['*'],
    "select * from `posts` where `name` like '%\\%hello\\_%'"
],

'unknown operator is ignored' => [
    ['score:explode' => 5],
    [],
    [],
    ['*'],
    "select * from `posts`"
]
    ];
}
      
}
