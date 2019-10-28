<?php

namespace App\Repositories;

use App\Models\Verification;
use App\Repositories\BaseRepository;

/**
 * Class VerificationRepository
 * @package App\Repositories
 * @version October 21, 2019, 2:16 pm UTC
*/

class VerificationRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'is_first_registration',
        'agent_id',
        'verified_by',
        'approved_by',
        'date',
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
        return Verification::class;
    }
}
