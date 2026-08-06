<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreProductStock extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'store_id',
        'receiving_id',
        'supply_id',
        'expiry_date',
        'product_id',
        'variant_id',
        'purchase_qty',
        'sold_qty',
        'cost'
    ];

    public function store() {
        $this->belongsTo(Store::class);
    }

    public function product() {
        $this->belongsTo(Product::class);
    }

    public function Variant() {
        $this->belongsTo(ProductVariant::class);
    }
}
