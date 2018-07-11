<?php

namespace Barzegari\Timetracker\Model;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * @param $pEmployeeNo Employee Number
     *
     * @return User object
     */
    public static function getUserByEmployeeNo($employeeNo)
    {
        return User::Where('employee_no',$employeeNo)->first();
    }

    /**
     * Get the log times for the project.
     */
    public function timelogs()
    {
        return $this->hasMany('Barzegari\Timetracker\Model\Timelog');
    }

    /**
     * get user project
     *
     */
    public function project()
    {
        return $this->hasOne('Barzegari\Timetracker\Model\Project');
    }

}
