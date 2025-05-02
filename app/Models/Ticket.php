<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = ['user_id', 'category_id', 'priority', 'status', 'subject'];
    public function getStatusAttribute($value)
    {
        $adminMessage = $this->messages()->whereHas('user', function ($query) {
            $query->where('role', 'admin');  
        })->first();

        if (!$adminMessage) {
            return 'in_review';  
        }

        return $value;  
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function category()
    {
        return $this->belongsTo(TicketType::class, 'category_id');
    }
    public function messages()
    {
        return $this->hasMany(TicketMessage::class);
    }
}
