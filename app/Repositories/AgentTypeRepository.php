<?php

namespace App\Repositories;

use App\Models\AgentType;
use App\Repositories\BaseRepository;

/**
 * Class AgentTypeRepository
 * @package App\Repositories
 * @version November 10, 2019, 11:34 pm UTC
*/

class AgentTypeRepository extends BaseRepository
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
        return AgentType::class;
    }
}
