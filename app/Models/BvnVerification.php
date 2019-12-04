<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int status
 * @property int user_id
 * @SWG\Definition(
 *      definition="BvnVerification",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="agent_id",
 *          description="agent_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="user_id",
 *          description="user_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="status",
 *          description="status",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="name",
 *          description="name",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="updated_at",
 *          description="updated_at",
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class BvnVerification extends Model
{
    protected $fillable = [
        'data',
        'agent_id',
        'bvn',
        'status',
        'user_id'
    ];
    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'agent_id' => 'integer',
        'data' => 'array'
    ];
}
