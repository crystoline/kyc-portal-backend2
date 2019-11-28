<?php

namespace App\Repositories;

use App\Models\VerificationPeriod;
use App\Repositories\BaseRepository;

/**
 * Class VerificationPeriodRepository
 * @package App\Repositories
 * @version November 23, 2019, 5:47 pm UTC
*/

class VerificationPeriodRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'title',
        'date_start',
        'territory_id',
        'state_id',
        'lga_id'
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
        return VerificationPeriod::class;
    }
}
