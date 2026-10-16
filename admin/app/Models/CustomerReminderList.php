<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerReminderList extends Model
{
    use HasFactory;

    protected $table = 'customer_reminder_lists';

    protected $fillable = [
        'title',
        'reminder_date',
        'created_by',
        'status',
        'notes'
    ];

    const STATUS_ACTIVE = 1;
    const STATUS_COMPLETED = 2;

    public function items()
    {
        return $this->hasMany(CustomerReminderListItem::class, 'reminder_list_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function auditLogs()
    {
        return $this->hasMany(CustomerReminderListAudit::class, 'reminder_list_id')->latest();
    }

    public function getStatusNameAttribute()
    {
        return $this->status == self::STATUS_COMPLETED ? 'Completed' : 'Active';
    }
}
