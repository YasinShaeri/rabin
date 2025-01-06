<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_mobile' => 'required|exists:clients,id',
            'priority_id' => 'required|exists:priorities,id',
            'app_channel_category_id' => 'required|exists:app_channel_categories,id',
            'title' => 'required|string|max:255',
            'message_description' => 'required|string',
        ]);

        $ticket = Ticket::create($validated);

        $message = $ticket->messages()->create([
            'description' => $validated['message_description'],
        ]);

        return response()->json([
            'ticket' => $ticket->load([
                'client:id,first_name,last_name',
                'priority:id,title',
                'appChannel:id,title',
                'appChannelCategory:id,title',
            ]),
            'message' => $message,
        ], 201);

        /*return response()->json([
            'ticket' => [
                'id' => 1,
                'client_id' => 1,
                'ticket_status_id' => 1,
                'priority_id' => 2,
                'app_channel_id' => 3,
                'app_channel_category_id' => 5,
                'title' => 'Sample Ticket Title',
                'created_at' => '2025-01-05T10:30:00Z',
                'updated_at' => '2025-01-05T10:30:00Z',
                'client' => [
                    'id' => 1,
                    'first_name' => 'John',
                    'last_name' => 'Doe'
                ],
                'priority' => [
                    'id' => 2,
                    'title' => 'High Priority'
                ],
                'appChannel' => [
                    'id' => 3,
                    'title' => 'Mobile App'
                ],
                'appChannelCategory' => [
                    'id' => 5,
                    'title' => 'Support Department'
                ]
            ],
            'message' => [
                'id' => 1,
                'ticket_id' => 1,
                'description' => 'This is the first message for this ticket.',
                'created_at' => '2025-01-05T10:30:00Z',
                'updated_at' => '2025-01-05T10:30:00Z'
            ]
        ], 201);*/
    }

    public function getTickets(Request $request)
    {
        $validated = $request->validate([
            'ticket_id' => 'required|exists:tickets,id',
        ]);

        $ticket = Ticket::with([
            'client:id,first_name,last_name',
            'messages:id,ticket_id,client_id,user_id,description,created_at',
            'status:id,title',
            'priority:id,title',
            'appChannel:id,title',
            'category:id,title',
        ])->find($validated['ticket_id']);

        /*$ticket = [
            'id' => 1,
            'client' => [
                'id' => 123,
                'first_name' => 'Ali',
                'last_name' => 'Ahmadi',
            ],
            'messages' => [
                [
                    'id' => 1,
                    'ticket_id' => 1,
                    'client_id' => 123,
                    'user_id' => null,
                    'description' => 'This is a client message.',
                    'created_at' => '2025-01-06T14:00:00.000000Z',
                ],
                [
                    'id' => 2,
                    'ticket_id' => 1,
                    'client_id' => null,
                    'user_id' => 456,
                    'description' => 'This is an expert response.',
                    'created_at' => '2025-01-06T14:05:00.000000Z',
                ],
            ],
            'status' => [
                'id' => 1,
                'title' => 'Open',
            ],
            'priority' => [
                'id' => 1,
                'title' => 'High',
            ],
            'appChannel' => [
                'id' => 1,
                'title' => 'Web',
            ],
            'category' => [
                'id' => 1,
                'title' => 'Technical Support',
            ],
            'created_at' => '2025-01-06T13:00:00.000000Z',
            'updated_at' => '2025-01-06T13:05:00.000000Z',
        ];*/

        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $ticket,
        ]);
    }
}
