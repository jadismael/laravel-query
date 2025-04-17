<?php

namespace Tests\Unit;

use Jadismael\LaravelQuery\Services\Query\FilterParser;
use Jadismael\LaravelQuery\Services\Query\QueryFilters;
use Mockery;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\DataProviders\FilterParserDataProvider;
use Tests\DataProviders\FilterValidationDataProvider;
use Tests\TestCase as TestsTestCase;

class FilterParserTest extends TestsTestCase
{
    protected QueryFilters $queryFilters;
    protected array $modelColumns = ['name', 'age', 'status'];
    protected array $dateColumns = ['created_at'];

    protected function setUp(): void
    {
        parent::setUp();

     
    }

    protected function tearDown(): void
    {
        Mockery::close(); // Needed for Mockery to verify expectations
        parent::tearDown();
    }
    
public static function filterParsingProvider(): array
{
    return FilterParserDataProvider::all();
}
    #[DataProvider('filterParsingProvider')]

    public function test_parse_filters(array $input, array $expected)
    {
        $parsed = FilterParser::parseFilters( 
            $input,
            $this->dateColumns
        );

        $this->assertEquals($expected, $parsed);
    }

    #[DataProvider('filterValidationProvider')]
    public function test_filter_valid_parsed_filters(array $allowedOperators, array $parsed, array $modelColumns, array $expected)
    {
        $result = FilterParser::filterValidParsedFilters($allowedOperators, $parsed, $modelColumns);
    
        $this->assertEquals($expected, $result);
    }


    public static function filterValidationProvider(): array
{
    return FilterValidationDataProvider::all();
}

#[DataProvider('invalidKeyProvider')]
    public function test_invalid_keys_are_skipped(array $input)
    {
        $this->expectException(\InvalidArgumentException::class);
        $parsed = FilterParser::parseFilters($input, $this->dateColumns);
    
    }

    public static function invalidKeyProvider(): array
    {
        return [
            [['status@equals' => 'active']],
            [['id--gt' => 5]],
            [[':like' => 'hello']],
            [['' => 'test']],
        ];
    }

    public function test_nested_key_parsing_if_supported()
    {
        $input = ['user.name:like' => 'jedi'];
        $parsed = FilterParser::parseFilters($input, $this->dateColumns);

        $this->assertEquals([
            [
                'column' => 'user.name',
                'operator' => 'like',
                'value' => 'jedi',
                'isDate' => false,
            ]
        ], $parsed);
    }
    
#[DataProvider('caseInsensitiveOperatorProvider')]
    public function test_case_insensitive_operator_support(string $inputKey, string $expectedOperator)
    {
        $input = [$inputKey => 'active'];
        $parsed = FilterParser::parseFilters($input, $this->dateColumns);

        $this->assertEquals($expectedOperator, $parsed[0]['operator']);
    }

    public static function caseInsensitiveOperatorProvider(): array
    {
        return [
            ['status:GT', 'gt'],
            ['score:Gt', 'gt'],
            ['price:Lt', 'lt'],
            ['created_at:BETWEEN', 'between'],
        ];
    }

}    