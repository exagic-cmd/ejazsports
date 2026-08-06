<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BundleImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'bundle_id',
        'path',

    ];

    public function bundle()
    {
        return $this->belongsTo(Bundle::class);
    }
}
