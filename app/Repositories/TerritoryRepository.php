<?php

namespace App\Repositories;

use App\Models\Territory;
use App\Repositories\BaseRepository;

/**
 * Class TerritoryRepository
 * @package App\Repositories
 * @version November 23, 2019, 5:39 pm UTC
*/

class TerritoryRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name'
    ];

    /**
     * Return searchable fields
     *
     * @return array
     */
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Territory::class;
    }
}
