<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Model implements JWTSubject,AuthenticatableContract, AuthorizableContract
{
    use SoftDeletes,Authenticatable, Authorizable;

    //use EntrustUserTrait; // add this trait to your user model

    public $incrementing = false;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

//    protected $fillable = [
//        'user_name','user_phone','user_email','user_address','role_id','dept_id','class_id'
//    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password'];

    protected $dates=['deleted_at'];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function role()
    {
        return $this->belongsTo('App\Models\Role','role_id');
    }

    public function cls()
    {
        return $this->belongsTo('App\Models\Cls','class_id');
    }

    public function dept()
    {
        return $this->belongsTo('App\Models\Department','dept_id');
    }

    public function groups()
    {
        return $this->hasMany('App\Models\Group','group_creator','id');
    }

    public function assessments()
    {
        return $this->hasMany('App\Models\Assesment','user_id','id');
    }

    public function weeklyReports()
    {
        return $this->hasMany('App\Models\WeeklyReport','user_id','id');
    }

}
