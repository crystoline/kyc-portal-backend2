<?php

namespace App\Models;

use App\Http\Requests\API\UpdateGroupAPIRequest;
use App\Http\Requests\API\UpdateUserAPIRequest;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property Permission permissions
 * @property string role
 * @property string name
 * @property Collection tasks
 * @SWG\Definition(
 *      definition="Group",
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
 *          property="role",
 *          description="URL friendly Group slug",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="enabled",
 *          description="1=Enabled, 0=Disabled, 2=Pending Disable, 3=Pending Enabled",
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
class Group extends Model
{
    // use SoftDeletes;

    public $table = 'groups';
    
   /* const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';*/


  protected $dates = ['created_at', 'updated_at'];


    public $fillable = [
        'name',
        'role',
        'enabled'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'role' => 'string',
        'enabled' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required|unique:groups',
        'role' => 'required|unique:groups',
        //'enabled' => 'required'
    ];

    /**
     * @param UpdateGroupAPIRequest $request
     * @return array
     */
    public static function getValidationRulesForUpdate(UpdateGroupAPIRequest $request): array
    {
        return [
            'name' => 'required|unique:groups,name,'.$request->route()->parameter('group', 0),
            'role' => 'required|unique:groups,role,'.$request->route()->parameter('group', 0)
        ];
    }
    /**
     * @return HasMany
     **/
    public function permissions(): HasMany
    {
        return $this->hasMany(Permission::class);
    }

    /**
     * @return HasMany
     **/
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'group_id');
    }

    /**
     * @return BelongsToMany
     */
    public function tasks(): BelongsToMany
    {
        return $this->belongsToMany(Task::class, 'permissions');
    }
}
