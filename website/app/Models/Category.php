<?php

namespace App\Models;

use Carbon\Carbon;
use http\Env\Request;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class Category extends Model
{
    use HasFactory,SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'title',
        'serial_no_brand_listing',
        'image',
        'status',
        'parent_category_id',
        'show_in_menu',
        'discount_id'
    ];
    protected $dates = ['deleted_at'];

    public function parentCategory() {
        return $this->hasMany(CategoryParent::class,'category_id');
    }

    public function childCategory() {
        return $this->hasMany(CategoryParent::class,'parent_category_id')->with('category');
    }

    public function discount() {
        return $this->belongsTo(Discount::class);
    }


}
