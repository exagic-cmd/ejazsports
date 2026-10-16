<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerReminderListAudit extends Model
{
    protected $table = 'customer_reminder_list_audits';

    protected $fillable = [
        'reminder_list_id', 'user_id', 'action', 'subject_type', 'subject_id',
        'description', 'old_values', 'new_values'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
