<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Followup extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'customer_id','status','next_followup_date'
    ];
    
    const INPROCESS = 1;
    const COMPLETE  = 2;

    public function customer() {
    	return $this->belongsTo(Customer::class);
    }
    
    public function detail() {
        return $this->hasMany(FollowupDetail::class);
    }
    
    
}
