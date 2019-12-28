<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\BaseRepository;

/**
 * Class UserRepository
 * @package App\Repositories
 * @version October 21, 2019, 1:56 pm UTC
*/

class UserRepository extends BaseRepository
{

    /**
     * Return searchable fields
     *
     * @return array
     */
    public function getFieldsSearchable() : array
    {
        return[
            'group_id',
            'first_name',
            'last_name',
            'telephone',
            'gender',
            'email',
            'email_verified_at',
            'password',
            'remember_token'
        ];
    }

    /**
     * Configure the Model
     **/
    public function model(): string
    {
        return User::class;
    }
}
