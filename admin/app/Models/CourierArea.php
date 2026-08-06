<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourierArea extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'courier_id',
        'area_id'
    ];

    public $timestamps = false;


    public function courier() {
        return $this->belongsTo(Courier::class);
    }
    public function area() {
        return $this->belongsTo(Area::class);
    }
}
