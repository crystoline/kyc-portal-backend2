<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property integer state_id
 * @SWG\Definition(
 *      definition="Territory",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="name",
 *          description="name",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="state_id",
 *          description="state id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="created_at",
 *          description="created_at",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="updated_at",
 *          description="updated_at",
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class Territory extends Model
{
    //use SoftDeletes;

    public $table = 'territories';


    protected $dates = ['deleted_at'];



    public $fillable = [
        'name',
        'state_id',
        'territory',
        'region'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'state_id' => 'integer',
        'name' => 'string',
        'territory' => 'string',
        'region' => 'string',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required',
        'state_id' => 'sometimes|exists:states,id'
    ];

    /**
     * @return HasMany
     **/
    public function verificationPeriods(): HasMany
    {
        return $this->hasMany(VerificationPeriod::class, 'territory_id');
    }
    /**
     * @return BelongsTo
     **/
    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

}
