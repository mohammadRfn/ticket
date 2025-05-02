<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\TicketMessage;

class TicketMessageController extends Controller
{
    public function index(Request $req, $ticketId)
    {
        if ($req->user()->role !== 'admin' && $req->user()->id !== Ticket::findOrFail($ticketId)->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $ticket = Ticket::findOrFail($ticketId);

        $messages = $ticket->messages;

        return response()->json([
            'status' => $ticket->status,
            'messages' => $messages
        ]);
    }
    public function store(Request $req, $ticketId)
    {
        $validated = $req->validate([
            'message' => 'required|string',
            'parent_id' => 'nullable|exists:ticket_messages,id',
        ]);

        $ticket = Ticket::findOrFail($ticketId);

        if ($req->user()->role !== 'admin' && $ticket->user_id !== $req->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        if ($req->user()->role === 'admin' && !isset($validated['parent_id'])) {
        
            $message = TicketMessage::create([
                'ticket_id' => $ticket->id,
                'user_id'   => $req->user()->id,
                'message'   => $validated['message'],
                'parent_id' =>  $ticket->id,
            ]);
            $ticket->update(['status' => 'answered']);
        } else {
            $firstMessage = $ticket->messages()->whereNull('parent_id')->first();
            $message = TicketMessage::create([
                'ticket_id' => $ticket->id,
                'user_id'   => $req->user()->id,
                'message'   => $validated['message'],
                'parent_id' => $firstMessage ? $firstMessage->id : null,
            ]);
            $ticket->update(['status' => 'answered']);
        }
        return response()->json([
            'message' => $message,
            'ticket_status' => $ticket->status
        ], 201);
    }
}
