<?php

namespace App\Models;

use App\Http\Requests\API\UpdateAgentAPIRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property Carbon last_verification_date
 * @property integer id
 * @SWG\Definition(
 *      definition="Agent",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="parent_agent_id",
 *          description="Parent agent id",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="type",
 *          description="principal agent, sole agent",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="is_app_only",
 *          description="0=No,1=Yes",
 *          type="boolean"
 *      ),
 *      @SWG\Property(
 *          property="first_name",
 *          description="first_name",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="code",
 *          description="Agent unique code",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="last_name",
 *          description="last_name",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="user_name",
 *          description="user_name",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="gender",
 *          description="Male, Female",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="date_of_birth",
 *          description="date_of_birth",
 *          type="string",
 *          format="date"
 *      ),
 *      @SWG\Property(
 *          property="passport",
 *          description="passport",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="status",
 *          description="2=Pending, 1=Verified, 0=Re-Verification",
 *          type="boolean"
 *      ),
 *       @SWG\Property(
 *          property="last_verification_date",
 *          description="Date of the previuos verification",
 *          type="date-time"
 *      ),
 *       @SWG\Property(
 *          property="Verification_status",
 *          description="Reverivication status",
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
class Agent extends Model
{
    // use SoftDeletes;

    public $table = 'agents';
    
   /* const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';*/


    protected $dates = ['deleted_at'];

    protected $appends = ['verification_status'];

    public $fillable = [
        'type',
        'parent_agent_id',
        'is_app_only',
        'first_name',
        'last_name',
        'user_name',
        'gender',
        'date_of_birth',
        'passport',
        'status',
        'code',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'parent_agent_id' => 'integer',
        'type' => 'string',
        'is_app_only' => 'boolean',
        'first_name' => 'string',
        'last_name' => 'string',
        'user_name' => 'string',
        'gender' => 'string',
        'date_of_birth' => 'date',
        'passport' => 'string',
        //'status' => 'boolean'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

        'parent_agent_id' => 'required|exists:agents,id', // TODO validation for parent agent code
        'type' => 'required|in:principal-agent,sole-agent',
        'is_app_only' => 'required|in:0,1',
        'first_name' => 'required',
        'last_name' => 'required',
        'user_name' => 'required|unique:agents',
        'code' => 'required|unique:agents',
        'gender' => 'required|in:male,female',
        'date_of_birth' => 'required|date',
        //'status' => 'required',
    ];

    /**
     * @param UpdateAgentAPIRequest $request
     * @return array
     */
    public static function getValidationRulesForUpdate(UpdateAgentAPIRequest $request): array
    {
        return [
            'parent_agent_id' => 'required|exists:agents,id', // TODO validation for parent agent code
            'type' => 'required|in:principal-agent,sole-agent',
            'is_app_only' => 'required|in:0,1',
            'first_name' => 'required',
            'last_name' => 'required',
            'user_name' => 'required|unique:agents,user_name,'.$request->route()->parameter('agent'),
            'code' => 'required|unique:agents,code,'.$request->route()->parameter('agent'),
            'gender' => 'required|in:male,female',
            'date_of_birth' => 'required|date',
            //'status' => 'required',
        ];
    }

    /**
     * @return HasMany
     **/
    public function verifications(): HasMany
    {
        return $this->hasMany(Verification::class, 'agent_id');
    }

    public function parentAgent(): BelongsTo
    {
        return $this->belongsTo(__CLASS__, 'parent_agent_id');
    }
    public function getVerificationStatusAttribute(): int
    {
        $today = new \DateTime();
        try{
            $interval =  new \DateInterval(config('re-verification-interval', 'P7D'));
            if($this->last_verification_date && $today->getTimestamp() <= $this->last_verification_date->add($interval)){
                return 1;
            }
        }catch (\Exception $exception){}
        return 0;
    }
}
