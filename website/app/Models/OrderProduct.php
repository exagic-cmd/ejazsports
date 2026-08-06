<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'order_id',
        'product_id',
        'variant_id',
        'qty',
        'price',
        'scanned',
        'barcode',
        'returned',
        'cost_price',
        'return_qty',
        'wholesale_price'
    ];

    public function product() {
        return $this->belongsTo(Product::class);
    }

    public function variant() {
        return $this->belongsTo(ProductVariant::class,'variant_id');
    }

    public function order() {
        return $this->belongsTo(Order::class);
    }
}
