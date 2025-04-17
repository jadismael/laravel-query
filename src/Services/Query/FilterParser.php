<?php

namespace Jadismael\LaravelQuery\Services\Query;

class FilterParser
{
    public static function parseFilters(array $filters, array $dateColumns): array
    {
        $parsed = [];

        foreach ($filters as $key => $value) {
            [$column, $operator] = self::parseFilterKey($key);

            $operator = strtolower($operator); // 🛡️ normalize operator

            if (self::isEmpty($value) && ! in_array($operator, ['null', 'notnull'])) {
                continue;
            }
            $isDate = in_array($column, $dateColumns);

            $parsed[] = compact('column', 'operator', 'value', 'isDate');
        }

        return $parsed;
    }

    public static function isEmpty(mixed $value): bool
    {
        return is_null($value) || '' === $value || (is_array($value) && empty($value));
    }

    public static function filterValidParsedFilters(array $allowedOperators, array $parsed, array $modelColumns): array
    {
        return array_filter($parsed, function ($filter) use ($modelColumns, $allowedOperators) {
            return in_array($filter['column'], $modelColumns)
                && in_array($filter['operator'], $allowedOperators)
                && self::isValidValueForOperator($filter['operator'], $filter['value']);
        });
    }

    private static function parseFilterKey(string $key): array
    {
        // Basic sanitation
        if (! preg_match('/^[a-zA-Z0-9_\.]+(:[a-zA-Z0-9_]+)?$/', $key)) {
            throw new \InvalidArgumentException("Invalid filter key: {$key}");
        }

        if (str_contains($key, ':')) {
            return explode(':', $key, 2); // [column, operator]
        }

        return [$key, 'equals'];
    }

    private static function isValidValueForOperator(string $operator, mixed $value): bool
    {
        return match ($operator) {
            'in', 'notin' => is_array($value) && count($value) > 0,
            'between', 'notbetween' => is_array($value) && 2 === count($value),
            'null', 'notnull' => true,
            default => ! self::isEmpty($value),
        };
    }
}
