<?php

namespace App\Repositories;

use App\Models\Lga;
use App\Repositories\BaseRepository;

/**
 * Class LgaRepository
 * @package App\Repositories
 * @version October 28, 2019, 1:27 pm UTC
*/

class LgaRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'state_id',
        'name',
        'code'
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
        return Lga::class;
    }
}
