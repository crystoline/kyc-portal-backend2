<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string nibss_code
 * @SWG\Definition(
 *      definition="Bank",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="bank_type_id",
 *          description="bank_type_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="name",
 *          description="name",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="nibss_code",
 *          description="nibss_code",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="isw_code",
 *          description="isw_code",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="status",
 *          description="status",
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
class Bank extends Model
{
   /// use SoftDeletes;

    public $table = 'banks';
    
  /*  const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';*/


    protected $dates = ['deleted_at'];

    protected $with = ['bankType'];


    public $fillable = [
        'bank_type_id',
        'name',
        'nibss_code',
        'isw_code',
        'status'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'bank_type_id' => 'integer',
        'name' => 'string',
        'nibss_code' => 'string',
        'isw_code' => 'string',
        'status' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'bank_type_id' => 'required',
        'name' => 'required',
        'status' => 'required'
    ];

    /**
     * @return BelongsTo
     **/
    public function bankType(): BelongsTo
    {
        return $this->belongsTo(BankType::class, 'bank_type_id');
    }
}
