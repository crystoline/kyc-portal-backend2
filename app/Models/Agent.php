<?php

namespace App\Models;

use App\Http\Requests\API\UpdateAgentAPIRequest;
use DateInterval;
use DateTime;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property Carbon last_verification_date
 * @property integer id
 * @property integer status
 * @property string passport
 * @property integer territory_id
 * @property integer lga_id
 * @property string type
 * @property integer parent_agent_id
 * @property integer agent_type_id
 * @property integer device_owner_id
 * @property integer is_app_only
 * @property string first_name
 * @property mixed|string last_name
 * @property mixed|string user_name
 * @property mixed|string gender
 * @property \Illuminate\Support\Carbon|mixed date_of_birth
 * @property string phone_number
 * @property string email
 * @property string address
 * @property int state_id
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
 *          property="territory_id",
 *          description="territory id",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="device_owner_id",
 *          description="device owner id",
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
 *      ),
 *       @SWG\Property(
 *          property="lga_id",
 *          description="local govt",
 *          type="integer",
 *          format="int32"
 *      ),
 *       @SWG\Property(
 *          property="state_id",
 *          description="State",
 *          type="integer",
 *          format="int32"
 *      ),
 *       @SWG\Property(
 *          property="user_id",
 *          description="User",
 *          type="integer",
 *          format="int32"
 *      ),
 *       @SWG\Property(
 *          property="address",
 *          description="Address",
 *          type="string"
 *      ),
 *       @SWG\Property(
 *          property="city",
 *          description="City",
 *          type="string"
 *      ),
 *       @SWG\Property(
 *          property="email",
 *          description="Email",
 *          type="string"
 *      ),
 *       @SWG\Property(
 *          property="phone_number",
 *          description="Telephone ",
 *          type="string"
 *      )
 * )
 */
class Agent extends Model
{
    // use SoftDeletes;

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

        'parent_agent_id' => 'sometimes|exists:agents,id', // TODO validation for parent agent code
        'agent_type_id' => 'sometimes|exists:agent_types,id',
        'territory_id' => 'sometimes|exists:territories,id',
        'device_owner_id' => 'sometimes|exists:device_owners,id',
        'type' => 'required|in:principal-agent,sole-agent',
        'is_app_only' => 'required|in:0,1',
        'first_name' => 'required',
        'last_name' => 'required',
        'user_name' => 'required|unique:agents',
        'code' => 'required|unique:agents',
        'gender' => 'required|in:male,female',
        'date_of_birth' => 'required|date',
        //'status' => 'required',
        'lga_id' => 'sometimes|exists:lgas,id',
        'state_id' => 'sometimes|exists:states,id',
        'email' => 'sometimes|email',
    ];

    /* const CREATED_AT = 'created_at';
     const UPDATED_AT = 'updated_at';*/
    public $table = 'agents';
    public $fillable = [
        'type',
        'parent_agent_id',
        'agent_type_id',
        'device_owner_id',
        'territory_id',
        'is_app_only',
        'first_name',
        'last_name',
        'user_name',
        'gender',
        'date_of_birth',
        'passport',
        'status',
        'code',
        'phone_number',
        'email',
        'address',
        'city',
        'lga_id',
        'state_id',
        'user_id'
    ];
    protected $with = ['agentType'];
    protected $dates = ['deleted_at'];
    protected $appends = ['verification_status'];
    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'territory_id' => 'integer',
        'parent_agent_id' => 'integer',
        'device_owner_id' => 'integer',
        'agent_type_id' => 'integer',
        'type' => 'string',
        'is_app_only' => 'boolean',
        'first_name' => 'string',
        'last_name' => 'string',
        'user_name' => 'string',
        'gender' => 'string',
        'date_of_birth' => 'date',
        'passport' => 'string',
        'status' => 'integer'
    ];

    /**
     * @param UpdateAgentAPIRequest $request
     * @return array
     */
    public static function getValidationRulesForUpdate(UpdateAgentAPIRequest $request): array
    {
        return [
            'parent_agent_id' => 'sometimes|exists:agents,id', // TODO validation for parent agent code
            'territory_id' => 'sometimes|exists:territories,id',
            'device_owner_id' => 'sometimes|exists:device_owners,id',
            'type' => 'required|in:principal-agent,sole-agent',
            'is_app_only' => 'required|in:0,1',
            'first_name' => 'required',
            'last_name' => 'required',
            'user_name' => 'required|unique:agents,user_name,' . $request->route()->parameter('agent'),
            'code' => 'required|unique:agents,code,' . $request->route()->parameter('agent'),
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

    public function assignOfficer(): BelongsTo
    {
        return $this->belongsTo(__CLASS__, 'user_id');
    }

    public function agentType(): BelongsTo
    {
        return $this->belongsTo(AgentType::class, 'parent_agent_id');
    }

    public function territory(): BelongsTo
    {
        return $this->belongsTo(Territory::class);
    }

    public function deviceOwner(): BelongsTo
    {
        return $this->belongsTo(DeviceOwner::class);
    }


    public function getVerificationStatusAttribute(): ?int
    {
        $agent = $this;
        $verificationPeriod = VerificationPeriod::any(request(), $agent)
            ->multipleOrderBy([
                'verification_periods.date_start' => 'DESC',
                'verification_periods.territory_id' => 'DESC',
                'verification_periods.lga_id' => 'DESC',
                'verification_periods.state_id' => 'DESC',
            ])->first();

        $verification = $this->verifications()->whereHas('verificationPeriod', static function (Builder $builder) use($verificationPeriod){
            $builder->where('id', $verificationPeriod->id);
        })->first();
        switch ($verification->status??null) {
            case null:
                return 0;
            case 0:
                return 2;
            case 9:
                return 2;
            default:
                return $verification->status;
        }
        /** @var Verification $verification */
        $verification = VerificationPeriod::any(request(), $agent)
            ->whereHas('verifications', static function (Builder $builder) use($agent){
                $builder->where('agent_id', $agent->id);
            })
            ->select([
                'verifications.status AS v_status'
            ])
            ->rightJoin('verifications', 'verifications.verification_period_id', 'verification_periods.id')
            ->multipleOrderBy([
                'verification_periods.territory_id' => 'DESC',
                'verification_periods.lga_id' => 'DESC',
                'verification_periods.state_id' => 'DESC',
            ])->first();

        switch ($verification->v_status??null) {
            case null:
                return 0;
            case 0:
                return 2;
            case 9:
                return 2;
            default:
                return $verification->v_status;
        }
        return $verification->status??null;
        /** @var Verification $verification */
        $verification = $this->verifications()
            ->whereHas('verificationPeriod', static function ( Builder $builder) use($agent){
                $builder->any(request(), $agent) ->multipleOrderBy([
                    'data_start' => 'DESC',
                    'territory_id' => 'DESC',
                    'lga_id' => 'DESC',
                    'state_id' => 'DESC',
                ])->first();
            }
        )->first();
        return $verification->status??null;
    }




    public function getTime()
    {

    }
}
