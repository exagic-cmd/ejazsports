<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class wishlist extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'product_id',
        'customer_id',
        ];

    public function product() {
        return $this->belongsTo(Product::class)->with('brand');
    }

    public function customer() {
        return $this->belongsTo(Customer::class);
    }
}
