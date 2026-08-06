<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class BundleVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'bundle_id',
        'product_variant_id',
        'product_id'
    ];


protected $casts = [
    'purchase_price' => 'float',
    'additional_price' => 'float'
];

    public function bundle()
    {
        return $this->belongsTo(Bundle::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}
