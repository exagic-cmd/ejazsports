<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockAuditDetail extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'stock_audit_id',
        'product_id',
        'variant_id',
        'in_hand_qty',
        'difference_qty',
        'adjust_in_stock',
        'adjust_in_expiry',
        'adjust_in_damage',
        'adjust_in_missing',
        'reason'
    ];

    public function product() {
        return $this->belongsTo(Product::class);
    }

    public function variant() {
        return $this->belongsTo(ProductVariant::class);
    }
    
    public function audit() {
         return $this->belongsTo(StockAudit::class,'stock_audit_id');
    }
}
