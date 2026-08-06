<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'category_id',
        'product_id'
    ];


    public function category() {
        return $this->belongsTo(Category::class);
    }
    public function product() {
        return $this->belongsTo(Product::class);
    }
}
