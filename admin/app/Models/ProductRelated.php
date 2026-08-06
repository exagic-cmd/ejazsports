<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductRelated extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'product_id',
        'related_product_id'
    ];


    public function product() {
        return $this->belongsTo(Product::class);
    }
    public function relatedProduct() {
        return $this->belongsTo(Product::class)->with('brand','thumbnail');
    }
}
