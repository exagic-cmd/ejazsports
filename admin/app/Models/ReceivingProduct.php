<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceivingProduct extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'receiving_id',
        'product_id',
        'product_variant_id',
        'qty',
        'trade_price',
        'gst',
        'discount',
        'cost_price',
        'sale_price',
        'foc_product',
        'tester_product',
        'po_product',
        'expiry_date'
    ];

    public function variant() {
        return $this->belongsTo(ProductVariant::class,'product_variant_id');
    }

    public function product() {
        return $this->belongsTo(Product::class,'product_id');
    }
    
    public function receiving() {
        return $this->belongsTo(Receiving::class);
    }
}
