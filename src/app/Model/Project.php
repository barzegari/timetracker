<?php

namespace Barzegari\Timetracker\Model;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description',
    ];


    /**
     * Get the log times for the project.
     */
    public function timelogs()
    {
        return $this->hasMany('Barzegari\Timetracker\Model\Timelog');
    }

    /**
     * Get the log times for the project.
     */
    public function user()
    {
        return $this->hasMany('Barzegari\Timetracker\Model\User');
    }

}
