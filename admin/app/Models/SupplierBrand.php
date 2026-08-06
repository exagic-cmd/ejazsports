<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierBrand extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'supplier_id',
        'brand_id',
        'margin',
        'payment_terms'
    ];

    protected $table = 'suppliers_brands';

    const CASH = 1;
    const CREDIT = 2;
    const SALE_BASIS = 3;

    public $timestamps = false;

    public function supplier() {
        return $this->belongsTo(Supplier::class);
    }
    public function brand() {
        return $this->belongsTo(Brand::class);
    }
}
