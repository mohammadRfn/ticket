<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\TicketMessage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Log;

class TicketController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $req)
    {
        $this->authorize('viewAny', Ticket::class);
        $tickets = Ticket::with('user', 'category')->paginate(10);
        $tickets->transform(function ($ticket) {
            $ticket->category_name = $ticket->category ? $ticket->category->name : null;
            return $ticket;
        });
        return response()->json($tickets);
    }

    public function store(Request $req)
    {
        $validated = $req->validate([
            'subject'     => 'required|string',
            'category_id' => 'required|exists:ticket_types,id',
            'priority'    => 'required|in:low,medium,high',
            'message'     => 'required|string',
            'file'        => 'nullable|file|max:10240', 
        ]);
    
        if ($req->hasFile('file')) {
            $filePath = $req->file('file')->store('tickets', 'public');
        } else {
            $filePath = null;
        }
    
        $ticket = Ticket::create([
            'user_id'     => $req->user()->id,
            'category_id' => $validated['category_id'],
            'priority'    => $validated['priority'],
            'subject'     => $validated['subject'],
            'file'        => $filePath,
            'status'      => 'in_review',
        ]);
    
        TicketMessage::create([
            'ticket_id'  => $ticket->id,
            'user_id'    => $req->user()->id,
            'message'    => $validated['message'],
            'attachment' => $filePath,  
        ]);
    
        return response()->json($ticket, 201);
    }
    
    

    public function close(Request $req, $ticketId)
    {
        $ticket = Ticket::findOrFail($ticketId);
        if ($req->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);  // اگر کاربر ادمین نباشد
        }
        $this->authorize('close', $ticket);
        $ticket->update(['status' => 'closed']);
        return response()->json(['message' => 'Ticket closed'], 200);
    }
}
