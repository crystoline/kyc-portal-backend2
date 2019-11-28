<?php


namespace App;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{

    /**
     * @param Builder $builder
     * @param array $orderBy
     * @return Builder
     */
    public function scopeMultipleOrderBy(Builder $builder, array  $orderBy): Builder
    {
        foreach ($orderBy as $column => $direction) {
            $builder->orderBy($column, $direction);
        }
        return $builder;
    }
}