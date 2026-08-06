<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bundle extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'short_desc',
        'full_desc',
        'purchase_price',
        'additional_price',
        'status'
    ];

    protected $appends = ['variants_count'];

    public function variants()
    {
        return $this->hasMany(BundleVariant::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'bundle_variants')
                    ->withPivot('product_variant_id')
                    ->distinct();
    }

    public function images()
    {
        return $this->hasMany(BundleImage::class);
    }

    public function firstImage()
    {
        return $this->hasOne(BundleImage::class)->oldestOfMany();
    }

    public function getVariantsCountAttribute()
    {
        return $this->variants()->count();
    }

    public function calculateTotalPurchasePrice()
    {
        return $this->variants->sum(function($variant) {
            if ($variant->variant) {
                return $variant->variant->purchase_price;
            }
            return $variant->product->purchase_price ?? 0;
        });
    }

    public function calculateTotalAdditionalPrice()
    {
        return $this->variants->sum(function($variant) {
            if ($variant->variant) {
                return $variant->variant->additional_price;
            }
            return $variant->product->price ?? 0;
        });
    }
}
