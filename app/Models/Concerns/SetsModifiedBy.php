<?php

namespace App\Models\Concerns;

trait SetsModifiedBy
{
    public static function bootSetsModifiedBy(): void
    {
        static::saving(function ($model) {
            if (auth()->check() && in_array('modified_by_id', $model->getFillable())) {
                $model->modified_by_id = auth()->id();
            }
        });
    }
}
