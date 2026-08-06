<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductDealProduct extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'product_id',
        'quantity',
        'deal_id'
    ];


    public function product() {
        return $this->belongsTo(Product::class);
    }
    public function deal() {
        return $this->belongsTo(ProductDeal::class,'deal_id');
    }
}
