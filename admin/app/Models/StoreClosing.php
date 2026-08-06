<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Hash;
use DB;

class StoreClosing extends Model
{
    use HasFactory,SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'date',
        'store_id',
        'opening_balance',
        'cash_bills',
        'card_bills',
        'return_bills',
        'expense',
        'expecting_amount',
        'closing_amount',
        'difference',
        'note',
        'five_coin_count',
        'ten_note_count',
        'twenty_note_count',
        'fifty_note_count',
        'hundred_note_count',
        'five_hundred_note_count',
        'one_thousand_note_count',
        'five_thousand_note_count',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $dates = ['deleted_at'];


}
