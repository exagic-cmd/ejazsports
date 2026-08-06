<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourierHandoverOrder extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'courier_id',
        'total_orders',
        'total_amount'
    ];

    public function courier() {
        return $this->belongsTo(Courier::class);
    }

    public function orders() {
        return $this->hasMany(Order::class,'handover_id');
    }
}
