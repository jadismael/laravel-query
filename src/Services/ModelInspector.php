<?php

namespace Jadismael\LaravelQuery\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class ModelInspector
{
    public function getColumns(Model $model): array
    {
        return Schema::getColumnListing($model->getTable());
    }

    public function getDateColumns(Model $model): array
    {
        $dateCastTypesOrPrefixes = [
            'date',
            'datetime',
            'custom_datetime',
            'immutable_date',
            'immutable_datetime',
            'immutable_custom_datetime',
            'timestamp',
        ];

        $casts = $model->getCasts();

        $dateAttributes = array_filter($casts, function ($castType) use ($dateCastTypesOrPrefixes) {
            if (in_array($castType, $dateCastTypesOrPrefixes, true)) {
                return true;
            }

            if (str_contains((string) $castType, ':')) {
                $baseCastType = explode(':', $castType, 2)[0];

                return in_array($baseCastType, $dateCastTypesOrPrefixes, true);
            }

            return false;
        });

        return array_keys($dateAttributes);
    }
}
