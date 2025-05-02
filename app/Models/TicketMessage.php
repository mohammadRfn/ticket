<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketMessage extends Model
{
    protected $fillable = ['ticket_id', 'user_id', 'message', 'attachment', 'status', 'parent_id'];
    public function parent()
    {
        return $this->belongsTo(TicketMessage::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(TicketMessage::class, 'parent_id');
    }
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
