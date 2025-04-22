<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketTypes extends Model
{
    protected $fillable = ['name'];
    protected $table = 'ticket_types';

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
