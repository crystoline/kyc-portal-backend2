<?php

namespace App\Repositories;

use App\Models\Agent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class AgentRepository
 * @package App\Repositories
 * @version October 21, 2019, 2:09 pm UTC
*/

class AgentRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'type',
        'is_app_only',
        'first_name',
        'last_name',
        'user_name',
        'gender',
        'date_of_birth',
        'passport',
        'status',
        'device_owner_id',
        'territory_id',
        'lga_id',
        'state_id',
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
        return Agent::class;
    }

    /**
     * @param array $input
     * @return Agent
     */
    public function findOrCreate(array $input): Agent
    {
        $query = $this->model->newQuery();
        $code = $input['code']?? null;
        /** @var Agent $data */
        $data = $query->where('id',$code)->orWhere('code', $code)->first();
        if($data === null ){
            /** @var Agent $data */
            $data = $query->create($input);
        }
        return $data;
    }


}
