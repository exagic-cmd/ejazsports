<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductDealImage extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'deal_id',
        'url',
        'serial_no',
        'status'
    ];

    public function deal() {
        return $this->belongsTo(ProductDeal::class,'deal_id');
    }
}
