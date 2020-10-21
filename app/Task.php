<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model {
	
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'title', 'description', 'date', 'hours',
    ];

    /**
     * Get the user record associated with the task.
     */
    public function user()
    {
        return $this->hasOne('App\User');
    }

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'user_id' => 'integer',
        'hours' => 'integer'
    ];
}