<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplyProduct extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'supply_id',
        'product_id',
        'variant_id',
        'qty',
        'received_qty'
    ];

    public function supply() {
        return $this->belongsTo(Supply::class);
    }
    public function product() {
        return $this->belongsTo(Product::class);
    }
    public function variant() {
        return $this->belongsTo(ProductVariant::class,'variant_id');
    }
}
