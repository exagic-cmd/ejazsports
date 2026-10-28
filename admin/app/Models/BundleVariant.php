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

    /**
     * Remove deleted products / variants from bundles.
     * A bundle left with no items is deactivated so it can't be sold empty in POS.
     */
    public static function removeDeleted($productId, $variantIds = null)
    {
        $query = static::where('product_id', $productId);
        if ($variantIds !== null) {
            $query->whereIn('product_variant_id', $variantIds);
        }

        $bundleIds = (clone $query)->pluck('bundle_id')->unique();
        if ($bundleIds->isEmpty()) {
            return;
        }

        $query->delete();

        $emptyBundleIds = $bundleIds->diff(static::whereIn('bundle_id', $bundleIds)->pluck('bundle_id'));
        if ($emptyBundleIds->isNotEmpty()) {
            Bundle::whereIn('id', $emptyBundleIds)->update(['status' => 0]);
        }
    }

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
