<?php

namespace App;

use App\BaseModel;

class Group extends BaseModel
{
    //
    protected $fillable = ['name'];

   /* public $hidden = ['active_hour_id'];
    public $with = ['active_hour'];*/

    static function boot(){

        static::creating(function($group){
            $group->role = strtoupper(str_replace(' ','_',$group->name));
        });

        static::updating(function($group){
            $group->role = strtoupper(str_replace(' ','_',$group->name));
        });

        parent::boot();

    }


    public function permissions(){
        return $this->hasMany(Permission::class);
    }

    public function tasks(){
        return $this->belongstoMany(Task::class,'permissions');
    }



    public function users(){
        return $this->belongsToMany(User::class, 'group_users');
    }

    public function scopeEnabled($q){
        $q->where(function($q){
            $q->whereEnabled(1)->orWhere('enabled',2);
        });
    }

    public function scopeDisabled($q){
        $q->where(function($q){
            $q->whereEnabled(0)->orWhere('enabled',3);
        });
    }

}
