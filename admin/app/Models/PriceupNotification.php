<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use DB;

class PriceupNotification extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'product_id',
        'variant_id',
        'old_price',
        'new_price',
        'old_purchase',
        'new_purchase'
    ];
    
    public function product() {
        return $this->belongsTo(Product::class);
    }
    
    public function variant() {
        return $this->belongsTo(ProductVariant::class);
    }

    
}
