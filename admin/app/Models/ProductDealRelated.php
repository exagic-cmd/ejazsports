<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductDealRelated extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'deal_id',
        'related_product_id'
    ];


    public function deal() {
        return $this->belongsTo(ProductDeal::class);
    }
    public function relatedProduct() {
        return $this->belongsTo(Product::class)->with('brand','thumbnail');
    }
}
