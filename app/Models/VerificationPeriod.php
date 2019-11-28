<?php

namespace App\Models;

use App\BaseModel;
use App\Http\Requests\API\CreateVerificationAPIRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use InfyOm\Generator\Request\APIRequest;

/**
 * @property integer id
 * @SWG\Definition(
 *      definition="VerificationPeriod",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="title",
 *          description="title",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="date_start",
 *          description="date_start",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="territory_id",
 *          description="territory_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="state_id",
 *          description="state_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="lga_id",
 *          description="lga_id",
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
 * @method static Builder any(Request $request, Agent $agent)
 */
class VerificationPeriod extends BaseModel
{
  //  use SoftDeletes;

    public $table = 'verification_periods';
    
   /* const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';*/


  //  protected $dates = ['deleted_at'];



    public $fillable = [
        'title',
        'date_start',
        'territory_id',
        'state_id',
        'lga_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'title' => 'string',
        'date_start' => 'datetime',
        'territory_id' => 'integer',
        'state_id' => 'integer',
        'lga_id' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'title' => 'required',
        'date_start' => 'required'
    ];

    /**
     * @return BelongsTo
     **/
    public function lga(): BelongsTo
    {
        return $this->belongsTo(Lga::class, 'lga_id');
    }

    /**
     * @return BelongsTo
     **/
    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class, 'state_id');
    }

    /**
     * @return BelongsTo
     **/
    public function territory(): BelongsTo
    {
        return $this->belongsTo(Territory::class, 'territory_id');
    }

    /**
     * @return HasMany
     */
    public function verifications(): HasMany
    {
        return $this->hasMany(Verification::class);
    }

    public function scopeAny(Builder $builder, Request $request, Agent $agent){
        $builder->where(static function (Builder $builder) use ($request, $agent){
            $builder->where(static function (Builder $builder) use ($request, $agent){
                $builder->where('verification_periods.territory_id', $request->input('verification_periods.territory_id', $agent->territory_id))
                    ->orWhereNull('verification_periods.territory_id');
            });
            $builder->where(static function (Builder $builder) use ($request, $agent){
                $builder->where('verification_periods.lga_id', $request->input('verification_periods.lga_id', $agent->lga_id))
                    ->orWhereNull('verification_periods.lga_id');
            });
            $builder->where(static function (Builder $builder) use ($request, $agent){
                $builder->where('verification_periods.state_id', $request->input('verification_periods.state_id', $agent->territory_id))
                    ->orWhereNull('verification_periods.state_id');
            });
        });
            return $builder;
    }
}
