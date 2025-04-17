<?php

namespace Tests\DataProviders;

class FilterParserDataProvider
{
    public static function all(): array
    {
        return [
            'simple equals' => [
                [
                    'status:equals' => 'active',
                ],
                [[
                    'column' => 'status',
                    'operator' => 'equals',
                    'value' => 'active',
                    'isDate' => false,
                ]],
            ],

            'fallback to equals when no operator' => [
                [
                    'name' => 'Jedi',
                ],
                [[
                    'column' => 'name',
                    'operator' => 'equals',
                    'value' => 'Jedi',
                    'isDate' => false,
                ]],
            ],

            'like filter' => [
                [
                    'name:like' => 'jed%',
                ],
                [[
                    'column' => 'name',
                    'operator' => 'like',
                    'value' => 'jed%',
                    'isDate' => false,
                ]],
            ],

            'in operator' => [
                [
                    'age:in' => [20, 30],
                ],
                [[
                    'column' => 'age',
                    'operator' => 'in',
                    'value' => [20, 30],
                    'isDate' => false,
                ]],
            ],

            'between operator' => [
                [
                    'age:between' => [18, 25],
                ],
                [[
                    'column' => 'age',
                    'operator' => 'between',
                    'value' => [18, 25],
                    'isDate' => false,
                ]],
            ],

            'null operator' => [
                [
                    'status:null' => null,
                ],
                [[
                    'column' => 'status',
                    'operator' => 'null',
                    'value' => null,
                    'isDate' => false,
                ]],
            ],

            'notnull operator' => [
                [
                    'status:notnull' => null,
                ],
                [[
                    'column' => 'status',
                    'operator' => 'notnull',
                    'value' => null,
                    'isDate' => false,
                ]],
            ],

            'notin operator' => [
                [
                    'status:notin' => ['inactive'],
                ],
                [[
                    'column' => 'status',
                    'operator' => 'notin',
                    'value' => ['inactive'],
                    'isDate' => false,
                ]],
            ],

            'notbetween operator' => [
                [
                    'age:notbetween' => [10, 20],
                ],
                [[
                    'column' => 'age',
                    'operator' => 'notbetween',
                    'value' => [10, 20],
                    'isDate' => false,
                ]],
            ],

            'date-aware column' => [
                [
                    'created_at:equals' => '2024-01-01',
                ],
                [[
                    'column' => 'created_at',
                    'operator' => 'equals',
                    'value' => '2024-01-01',
                    'isDate' => true,
                ]],
            ],

            'empty string is ignored' => [
                [
                    'name:like' => '',
                ],
                [],
            ],

            'null value is ignored' => [
                [
                    'name:like' => null,
                ],
                [],
            ],

            'empty array is ignored for in' => [
                [
                    'age:in' => [],
                ],
                [],
            ],

            'invalid operator still parses' => [
                [
                    'status:unknownop' => 'yes',
                ],
                [[
                    'column' => 'status',
                    'operator' => 'unknownop',
                    'value' => 'yes',
                    'isDate' => false,
                ]],
            ],
        ];
    }
}
