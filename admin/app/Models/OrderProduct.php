<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    use HasFactory;

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
        'wholesale_price',
        'bundle_id',
        'parent_id',
        'is_bundle',
        'is_bundle_item'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class)->with('thumbnail');
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function parent()
    {
        return $this->belongsTo(OrderProduct::class, 'parent_id');
    }

    public function bundleItems()
    {
        return $this->hasMany(OrderProduct::class, 'parent_id');
    }

    public function bundle()
    {
        return $this->belongsTo(Bundle::class, 'bundle_id');
    }

    // Accessor to get the name (product title or bundle name)
    public function getNameAttribute()
    {
        if ($this->is_bundle && $this->bundle) {
            return $this->bundle->name ?? 'Unknown Bundle';
        }
        return $this->product ? $this->product->title : 'Unknown Product';
    }
}
