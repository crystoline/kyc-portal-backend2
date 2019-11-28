<?php

namespace App\Repositories;

use App\Models\DeviceOwner;
use App\Repositories\BaseRepository;

/**
 * Class DeviceOwnerRepository
 * @package App\Repositories
 * @version November 21, 2019, 4:05 pm UTC
*/

class DeviceOwnerRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'title'
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
        return DeviceOwner::class;
    }
}
