<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierReturnProduct extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'supplier_return_id',
        'product_id',
        'product_variant_id',
        'qty',
        'trade_price',
        'gst',
        'discount',
        'cost_price',
        'sale_price'
    ];

    public function variant() {
        return $this->belongsTo(ProductVariant::class,'product_variant_id');
    }

    public function product() {
        return $this->belongsTo(Product::class,'product_id');
    }
    
    public function supplierReturn() {
        return $this->belongsTo(SupplierReturn::class);
    }
}
