<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryParent extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'category_id',
        'parent_category_id'
    ];


    public function category() {
        return $this->belongsTo(Category::class);
    }
    public function parentCategory() {
        return $this->belongsTo(Category::class);
    }
}
