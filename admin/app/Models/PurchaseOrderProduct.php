<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderProduct extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'product_id',
        'purchase_order_id',
        'quantity',
        'product_variant_id',
        'trade_price'
    ];

    public function variant() {
        return $this->belongsTo(ProductVariant::class,'product_variant_id');
    }

    public function product() {
        return $this->belongsTo(Product::class);
    }

}
