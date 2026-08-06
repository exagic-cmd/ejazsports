<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductDealBrand extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'brand_id',
        'deal_id'
    ];


    public function brand() {
        return $this->belongsTo(Brand::class);
    }
    public function deal() {
        return $this->belongsTo(ProductDeal::class,'deal_id');
    }
}
