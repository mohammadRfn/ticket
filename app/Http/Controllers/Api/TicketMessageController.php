<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\TicketMessage;

class TicketMessageController extends Controller
{
    public function index($ticketId)
    {
        return TicketMessage::where('ticket_id', $ticketId)->get();
    }

    public function store(Request $req, $ticketId)
    {
        $validated = $req->validate([
            'message' => 'required|string',
        ]);

        $ticket = Ticket::findOrFail($ticketId);

        $message = TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id'   => $req->user()->id,
            'message'   => $validated['message'],
        ]);

        return response()->json($message, 201);
    }
}
