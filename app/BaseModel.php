<?php


namespace App;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class BaseModel extends Model
{

    /**
     * @param Builder $builder
     * @param array $orderBy
     * @return Builder
     */
    public function scopeMultipleOrderBy(Builder $builder, array $orderBy): Builder
    {
        foreach ($orderBy as $column => $direction) {
            $builder->orderBy($column, $direction);
        }
        return $builder;
    }

    /**
     * @param array $relations
     * @param Collection|null $collection
     * @return string
     */
    public function toFormattedString($relations = [], Collection $collection = null): string
    {
        $data = $this->load($relations)->toArray();
        if ($collection === null) {
            $collection = new Collection();
        }
        self::processStringData($collection, $data);

        return implode("\n", $collection->toArray());
    }

    /**
     * @param Collection $collection
     * @param array $data
     */
    private static function processStringData(Collection $collection, array $data): void
    {
        foreach ($data as $field => $value) {

            if (in_array($field, ['id', 'status', 'created_at', 'updated_at']) || strpos($field, '_id') !== false) {
                continue;
            }

            $title = strtoupper(str_replace('_', ' ', $field));
            if (is_array($value)) {
                $collection->push("********************* {$title} *********************");
                self::processStringData($collection, $value);
                continue;
            }


            $collection->push("{$title}: $value");


        }
    }

}