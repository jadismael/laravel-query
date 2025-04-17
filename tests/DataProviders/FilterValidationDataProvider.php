<?php


namespace Tests\DataProviders;

class FilterValidationDataProvider
{
    public static function all(): array
    {

        return [
            'valid filter' => [
                ['equals'],
                [[
                    'column' => 'status',
                    'operator' => 'equals',
                    'value' => 'active',
                    'isDate' => false,
                ]],
                ['status'],
                [[
                    'column' => 'status',
                    'operator' => 'equals',
                    'value' => 'active',
                    'isDate' => false,
                ]]
            ],
    
            'invalid operator' => [
                ['equals'],
                [[
                    'column' => 'status',
                    'operator' => 'unknownop',
                    'value' => 'active',
                    'isDate' => false,
                ]],
                ['status'],
                []
            ],
    
            'invalid column' => [
                ['equals'],
                [[
                    'column' => 'nonexistent',
                    'operator' => 'equals',
                    'value' => 'active',
                    'isDate' => false,
                ]],
                ['status'],
                []
            ],
    
            'invalid between value' => [
                ['between'],
                [[
                    'column' => 'age',
                    'operator' => 'between',
                    'value' => [10], // invalid: needs 2 items
                    'isDate' => false,
                ]],
                ['age'],
                []
            ],
    
            'valid null operator' => [
                ['null'],
                [[
                    'column' => 'status',
                    'operator' => 'null',
                    'value' => null,
                    'isDate' => false,
                ]],
                ['status'],
                [[
                    'column' => 'status',
                    'operator' => 'null',
                    'value' => null,
                    'isDate' => false,
                ]]
            ],
        ];
}
}