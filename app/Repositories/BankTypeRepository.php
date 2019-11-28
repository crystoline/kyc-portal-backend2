<?php

namespace App\Repositories;

use App\Models\BankType;
use App\Repositories\BaseRepository;

/**
 * Class BankTypeRepository
 * @package App\Repositories
 * @version November 10, 2019, 11:31 pm UTC
*/

class BankTypeRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'status'
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
        return BankType::class;
    }
}
