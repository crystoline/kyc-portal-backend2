<?php

namespace App\Models;

use App\BaseModel;
use App\Http\Requests\API\UpdateAgentAPIRequest;
use App\Http\Requests\API\UpdateVerificationAPIRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int status
 * @property string first_name
 * @property User verifiedBy
 * @property string passport
 * @property string agent_id
 * @property PersonalInformation personalInformation
 * @property integer parent_agent_id
 * @property Agent agent
 * @property integer id
 * @property integer territory_id
 * @property integer device_owner_id
 * @property integer verified_by
 * @property integer verification_period_id
 * @property integer approved_by
 * @property Carbon date
 * @property string type
 * @property integer agent_type_id
 * @property integer is_app_only
 * @property string last_name
 * @property string user_name
 * @property string gender
 * @property Carbon date_of_birth
 * @property string home_address
 * @property GuarantorsInformation guarantorInformation
 * @property Collection documents
 * @property int backup
 * @property VerificationPeriod verificationPeriod
 * @SWG\Definition(
 *      definition="Verification",
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
 *          property="parent_agent_id",
 *          description="Parent agent id",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="territory_id",
 *          description="territory id",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="device_owner_id",
 *          description="device owner id",
 *          type="string"
 *      ),
 *
 *      @SWG\Property(
 *          property="verified_by",
 *          description="verified_by",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="approved_by",
 *          description="approved_by",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="date",
 *          description="date",
 *          type="string",
 *          format="date"
 *      ),
 *      @SWG\Property(
 *          property="status",
 *          description="2=Pending, 1=Approved, 0=Declined,3=discarded",
 *          type="boolean"
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
class Verification extends  BaseModel
{
    // use SoftDeletes;

    public $table = 'verifications';
    
//    const CREATED_AT = 'created_at';
//    const UPDATED_AT = 'updated_at';

    protected $dates = ['deleted_at'];
    protected $hidden =[ 'backup'];
    public $fillable = [
        'agent_id',
        'territory_id',
        'device_owner_id',
        'verified_by',
        'verification_period_id',
        'approved_by',
        'date',
        'status',

        /** Agent Info */
        'type',
        'parent_agent_id',
        'agent_type_id',
        'is_app_only',
        'first_name',
        'last_name',
        'user_name',
        'gender',
        'date_of_birth',
        'passport',
        /* End Agent info*/
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'verification_period_id' => 'integer',
        'territory_id' => 'integer',
        'device_owner_id' => 'integer',

        /** Agent Information  */
        'type' => 'string',
        'is_app_only' => 'boolean',
        'first_name' => 'string',
        'last_name' => 'string',
        'user_name' => 'string',
        'gender' => 'string',
        'date_of_birth' => 'date',
        'passport' => 'string',
        /** Agent Information */

        'parent_agent_id' => 'integer',
        'agent_type_id' => 'integer',
        'status' => 'integer',
        'date' => 'date',

        'verified_by' => 'integer',
        'approved_by' => 'integer',


    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'parent_agent_id' => 'sometimes|exists:agents,id', // TODO validation for parent agent code
        'territory_id' => 'sometimes|exists:territories,id',
        'device_owner_id' => 'sometimes|exists:device_owners,id',
        'verification_period_id' => 'sometimes|exists:verification_periods,id', // TODO validation for parent agent code
        'agent_type_id' => 'sometimes|exists:agent_types,id',
        'date' => 'required',

        'type' => 'required|in:principal-agent,sole-agent',
        'is_app_only' => 'required|in:0,1',
        'first_name' => 'required',
        'last_name' => 'required',
        'user_name' => 'required',//|unique:agents', //todo complete unique
        'gender' => 'required|in:male,female',
        'date_of_birth' => 'required|date',
    ];

    /**
     * @param UpdateVerificationAPIRequest $request
     * @return array
     */
    public static function getValidationRulesForUpdate(UpdateVerificationAPIRequest $request): array
    {
        return [
            'parent_agent_id' => 'sometimes|exists:agents,id', // TODO validation for parent agent code
            'territory_id' => 'sometimes|exists:territories,id',
            'device_owner_id' => 'sometimes|exists:device_owners,id',
            // 'verification_period_id' => 'required|exists:verification_periods,id', // TODO validation for parent agent code
            'code' => 'sometimes|exists:agents,code',
            'date' => 'required',

            'type' => 'required|in:principal-agent,sole-agent',
            'is_app_only' => 'required|in:0,1',
            'first_name' => 'required',
            'last_name' => 'required',
            'user_name' => 'required',//|unique:agents,user_name,'.$request->input('agent_id'),
            'gender' => 'required|in:male,female',
            'date_of_birth' => 'required|date',
            //'status' => 'required',
        ];
    }

    /**
     * @return BelongsTo
     **/
    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }
    public function parentAgent(): BelongsTo
    {
        return $this->belongsTo(Agent::class, 'parent_agent_id');
    }

    /**
     * @return BelongsTo
     **/
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * @return BelongsTo
     **/
    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * @return HasMany
     **/
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class, 'verification_id');
    }

    /**
     * @return HasOne
     **/
    public function guarantorInformation(): HasOne
    {
        return $this->hasOne(GuarantorsInformation::class);
    }

    /**
     * @return HasOne
     **/
    public function personalInformation(): HasOne
    {
        return $this->hasOne(PersonalInformation::class);
    }

    /**
     * @return HasMany
     **/
    public function verificationApprovals(): HasMany
    {
        return $this->hasMany(VerificationApproval::class, 'verification_id');
    }

    /**
     * @return BelongsTo
     */
    public function AgentType(): BelongsTo
    {
        return $this->belongsTo(AgentType::class);
    }


    public function territory(): BelongsTo
    {
        return $this->belongsTo(Territory::class);
    }

    public function deviceOwner(): BelongsTo
    {
        return $this->belongsTo(DeviceOwner::class);
    }

    public function verificationPeriod(): BelongsTo
    {
        return $this->belongsTo(VerificationPeriod::class);
    }

    public function getTelephoneVerificationStatusAttribute(): bool
    {
        $verified = TelephoneVerification::query()->where([
            'telephone' => $this->personalInformation->phone_number?? '',
            'agent_id' => $this->agent_id,
            'status' => 1,
        ])->first();
        return $verified !== null;
    }

    public function getBvnVerificationStatusAttribute(): bool
    {
        $that = $this;
        $verified = BvnVerification::query()->where([
            'bvn' => $this->personalInformation->bvn?? '',
            'status' => 1,
        ])->where( static function(Builder $query) use ($that){
            $query->where('agent_id', $that->agent_id)->orWhere('agent_id', $that->parent_agent_id);
        })
        ->first();
        return $verified !== null;
    }
    public function getBvnIsForLinkedAgentAttribute(): bool
    {
        $that = $this;
        $bvn = BvnVerification::query()->where([
            'bvn' => $this->personalInformation->bvn?? '',
            'agent_id' => $that->parent_agent_id
        ])
        ->first();
        return $bvn !== null;
    }
}
