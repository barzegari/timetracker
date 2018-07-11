<?php

namespace Barzegari\Timetracker\Model;
use Illuminate\Database\Eloquent\Model;

class Timelog extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'project_id', 'description','from_date','to_date','user_id'
    ];


    /**
     * get logged user object
     *
     */
    public function user()
    {
        return $this->belongsTo('Barzegari\Timetracker\Model\User');
    }


    /**
     *get logged project object
     *
     */
    public function project()
    {
        return $this->belongsTo('Barzegari\Timetracker\Model\Project');
    }


}
