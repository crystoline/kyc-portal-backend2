<?php

namespace App\Models;

use App\Http\Requests\API\UpdateUserAPIRequest;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\HasApiTokens;

/**
 * @property integer id
 * @property integer status
 * @SWG\Definition(
 *      definition="User",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="group_id",
 *          description="group_id",
 *          type="integer",
 *          format="int32"
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
 *          property="gender",
 *          description="gender",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="email",
 *          description="email",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="email_verified_at",
 *          description="email_verified_at",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="status",
 *          description="status",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="password",
 *          description="password",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="remember_token",
 *          description="remember_token",
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
class User extends  Authenticatable
{
    use HasApiTokens, Notifiable, HasApiTokens;
    // use SoftDeletes;

    private static $stringPassword;
    public $table = 'users';
    /*  const CREATED_AT = 'created_at';
      const UPDATED_AT = 'updated_at';*/

    protected $dates = ['deleted_at'];
    protected $appends = [ 'status_text'];
    protected $hidden = [
        'email_verified_at',
        'password',
        'remember_token',
    ];

    public $fillable = [
        'group_id',
        'first_name',
        'last_name',
        'gender',
        'email',
        'status',
        'email_verified_at',
        'password',
        'remember_token'
    ];


    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'status' => 'integer',
        'group_id' => 'integer',
        'first_name' => 'string',
        'last_name' => 'string',
        'gender' => 'string',
        'email' => 'string',
        'email_verified_at' => 'datetime',
        'password' => 'string',
        'remember_token' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'group_id' => 'required|exists:groups,id',
        'first_name' => 'required',
        'last_name' => 'required',
        'gender' => 'required',
        'email' => 'required|unique:users,email',
        'password' => 'sometimes'
    ];

    public function AuthAccessToken(): HasMany
    {
        return $this->hasMany(OauthAccessToken::class);
    }

    /**
     * @param UpdateUserAPIRequest $request
     * @return array
     */
    public static function getValidationRulesForUpdate(UpdateUserAPIRequest $request): array
    {
        return [
            'group_id' => 'required|exists:groups,id',
            'first_name' => 'required',
            'last_name' => 'required',
            'gender' => 'required',
            'email' => 'required|unique:users,email,'.$request->route()->parameter('user', 0),
        ];
    }

    /**
     * @return BelongsTo
     **/
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    /**
     * @return HasMany
     **/
    public function verificationApprovals(): HasMany
    {
        return $this->hasMany(VerificationApproval::class, 'user_id');
    }

    /**
     * @return HasMany
     **/
    public function verifications(): HasMany
    {
        return $this->hasMany(Verification::class, 'approved_by');
    }

    /**
     * @return HasMany
     **/
    public function verification1s(): HasMany
    {
        return $this->hasMany(Verification::class, 'verified_by');
    }
    public function getStatusTextAttribute()
    {
        return $this->status === 0? 'Disabled': 'Enabled';
    }

    public function setPasswordAttribute($value): void {
        if(!$value) $value = config('app.default_password');
        $this->attributes['password'] = bcrypt($value);
        self::$stringPassword = $value;
    }
}
