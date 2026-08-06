<?php

namespace App\Models;

use Carbon\Carbon;
use http\Env\Request;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;

class Brand extends Model
{
    use HasFactory,SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'title',
        'serial_no',
        'image',
        'status',
        'show_in_menu',
        'discount_id'
    ];
    protected $dates = ['deleted_at'];

    public function suppliers()
    {
        return $this->hasMany(Supplier::class);
    }

    public function products() {
        return $this->hasMany(Product::class);
    }
    public function discount() {
        return $this->belongsTo(Discount::class);
    }

 }
