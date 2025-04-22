<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\TicketMessage;

class TicketController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $req)
    {
        $this->authorize('viewAny', Ticket::class);

        $tickets = Ticket::with('user', 'category')->paginate(10);
        return response()->json($tickets);
    }

    public function store(Request $req)
    {
        $validated = $req->validate([
            'subject'     => 'required|string',
            'category_id' => 'required|exists:ticket_types,id',
            'priority'    => 'required|in:low,medium,high',
            'message'     => 'required|string',
        ]);

        $ticket = Ticket::create([
            'user_id'     => $req->user()->id,
            'category_id' => $validated['category_id'],
            'priority'    => $validated['priority'],
            'subject'     => $validated['subject'],
        ]);

        TicketMessage::create([
            'ticket_id'  => $ticket->id,
            'user_id'    => $req->user()->id,
            'message'    => $validated['message'],
            'attachment' => null,
        ]);

        $ticket = Ticket::with(['user', 'category', 'messages'])->find($ticket->id);

        return response()->json($ticket, 201);
    }

    public function close(Request $req, $ticketId)
    {
        $ticket = Ticket::findOrFail($ticketId);
        $this->authorize('close', $ticket);

        $ticket->update(['status' => 'closed']);

        return response()->json(['message' => 'Ticket closed']);
    }
}
