<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductDealVariant extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'deal_id',
        'variant_id'
    ];

    public function deal() {
        return $this->belongsTo(ProductDealProduct::class,'deal_id');
    }

    public function variant() {
        return $this->belongsTo(ProductVariant::class);
    }
}
