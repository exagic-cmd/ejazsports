<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerReminderListItem extends Model
{
    use HasFactory;

    protected $table = 'customer_reminder_list_items';

    protected $fillable = [
        'reminder_list_id',
        'customer_id',
        'snapshot_balance',
        'status',
        'remarks',
        'sort_order'
    ];

    const STATUS_PENDING = 1;
    const STATUS_CALLED = 2;
    const STATUS_PAID = 3;
    const STATUS_RESCHEDULED = 4;
    const STATUS_CANCELLED = 5;

    public function reminderList()
    {
        return $this->belongsTo(CustomerReminderList::class, 'reminder_list_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function getStatusLabelAttribute()
    {
        switch ($this->status) {
            case self::STATUS_CALLED:
                return 'Called';
            case self::STATUS_PAID:
                return 'Paid';
            case self::STATUS_RESCHEDULED:
                return 'Rescheduled';
            case self::STATUS_CANCELLED:
                return 'Cancelled';
            default:
                return 'Pending';
        }
    }
}
