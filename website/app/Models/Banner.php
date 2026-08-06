<?php

namespace App\Models;

use http\Env\Request;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Banner extends Model
{
    use HasFactory,SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'web_image',
        'mobile_image',
        'serial_no',
        'web_heading',
        'web_sub_heading',
        'mobile_heading',
        'mobile_sub_heading',
        'url',
        'status',
    ];
    protected $dates = ['deleted_at'];

}
