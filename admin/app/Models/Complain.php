<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complain extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'phone_number',
        'email',
        'order_no',
        'type',
        'detail',
        'status',
        'entertain_date',
        'close_date'
    ];

    //status
    const PENDING = 1;
    const InPROGRESS = 2;
    const RESOLVED = 3;
    const CANCELED = 4;

    //type
    const DAMAGE_PRODUCT = 1;
    const MISSING_PRODUCT = 2;
    const NOT_DELIVERED = 3;
    const WRONG_PRODUCT = 4;

    public function documents() {
        return $this->hasMany(ComplainDocument::class);
    }

    public function notes() {
        return $this->hasMany(ComplainNote::class);
    }
}
