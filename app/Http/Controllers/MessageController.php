<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Ticket;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function addMessage(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'ticket_id' => 'required|exists:tickets,id',
            'description' => 'required|string',
        ]);

        $message = Message::create([
            'ticket_id' => $validated['ticket_id'],
            'client_id' => auth()->user()->id,
            'user_id' => null,
            'description' => $validated['description'],
        ]);

        $messages = Message::where('ticket_id', $validated['ticket_id'])
            ->orderBy('created_at', 'asc')
            ->get();

        /*$messages = [
            [
                'id' => 1,
                'ticket_id' => 1,
                'client_id' => 123,
                'user_id' => null,
                'description' => 'This is a new message for the ticket.',
                'created_at' => '2025-01-06T14:00:00.000000Z',
                'updated_at' => '2025-01-06T14:00:00.000000Z',
            ],
            [
                'id' => 2,
                'ticket_id' => 1,
                'client_id' => null,
                'user_id' => 456,
                'description' => 'This is the response from the expert.',
                'created_at' => '2025-01-06T14:05:00.000000Z',
                'updated_at' => '2025-01-06T14:05:00.000000Z',
            ]
        ];*/

        return response()->json([
            'message' => 'Message added successfully',
            'ticket_id' => $validated['ticket_id'],
            'messages' => $messages,
        ]);
    }
}
