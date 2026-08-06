<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'product_id',
        'barcode',
        'size',
        'shade',
        'additional_price',
        'available_stock',
        'online_available_stock',
        'status',
        'image',
        'purchase_price',
        'dz_price',
        're_order_level'
    ];

    public function product() {
        return $this->belongsTo(Product::class);
    }

    public function stock() {
        return $this->hasMany(StoreProductStock::class,'variant_id');
    }
}
