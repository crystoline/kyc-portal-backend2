<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @SWG\Definition(
 *      definition="Task",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="module_id",
 *          description="module_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="parent_task_id",
 *          description="Parent task if any default is 0 meaning no parent",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="route",
 *          description="Matching laravel route name for this task",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="name",
 *          description="name",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="task_type",
 *          description="0=Menu, 1=Action, 2=Plugin Menu, 3=Plugin Action",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="description",
 *          description="description",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="visibility",
 *          description="Can this task be seen or not",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="order",
 *          description="order",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="icon",
 *          description="icon",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="extra",
 *          description="extra",
 *          type="string"
 *      )
 * )
 */
class Task extends Model
{
    // use SoftDeletes;

    public $table = 'tasks';
    
    /*const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';*/


    protected $dates = ['deleted_at'];


    public $fillable = [
        'module_id',
        'parent_task_id',
        'route',
        'name',
        'task_type',
        'description',
        'visibility',
        'order',
        'icon',
        'extra'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'module_id' => 'integer',
        'parent_task_id' => 'integer',
        'route' => 'string',
        'name' => 'string',
        'task_type' => 'string',
        'description' => 'string',
        'visibility' => 'string',
        'order' => 'integer',
        'icon' => 'string',
        'extra' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'parent_task_id' => 'required',
        'route' => 'required',
        'name' => 'required',
        'task_type' => 'required',
        'description' => 'required',
        'visibility' => 'required',
        'order' => 'required'
    ];

    /**
     * @return BelongsTo
     **/
    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class, 'module_id');
    }

    /**
     * @return HasMany
     **/
    public function permissions(): HasMany
    {
        return $this->hasMany(Permission::class, 'task_id');
    }
}
