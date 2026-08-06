<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FollowupDetail extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'followup_id','remarks','user_id','next_followup_date'
    ];

    public function user() {
    	return $this->belongsTo(User::class,'user_id');
    }
    
    
}
