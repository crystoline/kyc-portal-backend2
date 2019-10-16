<?php

namespace App;

use App\BaseModel;

class Task extends BaseModel
{
    //
    protected $fillable = ['route','name','task_type','description','icon','visibility','order','parent_task_id','extra','module_id'];
    public $timestamps = false;

    public $hidden = ['module_id'];

    public $with = ['module'];

    public static function boot(){

        static::deleting(function($task){
            $task->permissions()->delete();
        });

        parent::boot();
    }

    public static function root($task){
        while($task->parent) $task = $task->parent;

        return $task;
    }

    public function groups(){
        return $this->belongsToMany(Group::class,'permissions');
    }

    public function permissions(){
        return $this->hasMany(Permission::class);
    }


    public function module(){
        return $this->belongsTo(Module::class);
    }

    public function parent_task(){
        return $this->belongsTo(Task::class,'parent_task_id');
    }

    public function scopeChildrenOf($q,$task_parent_id){
        $q->where('parent_task_id',$task_parent_id);
    }

    public function scopeNoModule($q){
        $q->whereNull('module_id');
    }

    public function scopeVisible($q){
        $q->where('visibility', '1');
    }

    public function scopeNoParent($q){
        $q->where('parent_task_id','0');
    }

    public function scopeHasParent($q){
        $q->where('parent_task_id','<>','0');
    }

    public function scopeMenu($q){
        $q->where('task_type','0');
    }

    public function scopeAction($q){
        $q->whereIn('task_type',[1,3]);
    }

    public function getIsVisibleAttribute(){
        return $this->attributes['visibility']?"Yes":"No";
    }

    public function getPathAttribute(){
        if(!$this->module) return $this->attributes['name'];

        if($this->parent_task_id) return "<i class='text-primary fa fa-child'></i><i class='text-muted'>".static::find($this->parent_task_id)->path."</i> - ".$this->attributes['name'];

        return "<i class='text-muted'>".$this->module->name."</i> - ".$this->attributes['name'];
    }

    public function getLabelRawAttribute(){

        if($this->parent_task_id) return static::find($this->parent_task_id)->label."|".$this->attributes['name'];

        return $this->attributes['name'];
    }

    public function getLabelAttribute(){

        if($this->parent_task_id) return "<i class='text-muted'><small>".static::find($this->parent_task_id)->label."</small>|</i>".$this->attributes['name'];

        return $this->attributes['name'];
    }

    public function getTypeAttribute(){
        if(!$this->task_type) return "Menu";

        if($this->task_type == 1) return "Action";

        if($this->task_type == 2) return "Plugin Menu";

        return "Plugin Action";
    }

}
